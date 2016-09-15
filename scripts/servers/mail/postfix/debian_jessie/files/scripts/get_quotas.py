#!/usr/bin/python3 -u

import time
import os
import re
import argparse
import json
import pwd
import sys
import socket
import configparser
from subprocess import call, DEVNULL, check_output
import math

def filesize(size):
   if (size == 0):
       return '0B'
   size_name = ("b", "Kb", "Mb", "Gb", "Tb", "Pb", "Eb", "Zb", "Yb")
   i = int(math.floor(math.log(size,1024)))
   p = math.pow(1024,i)
   s = round(size/p,2)
   return '%s %s' % (s,size_name[i])


def get_quotas():

    parser=argparse.ArgumentParser(prog='get_quotas.py', description='A tool for get quotas for a server')

    parser.add_argument('--domain', help='Domain to get quotas', required=True)
        
    args=parser.parse_args()

    json_return={'error':0, 'status': 0, 'progress': 0, 'no_progress':0, 'message': ''}

    domain_return={}

    domain_pattern=re.compile('.*_'+args.domain)

    quota_check=check_output("sudo repquota -a -u",  shell=True)

    arr_quota=quota_check.decode('utf-8').split("\n")

    for line in arr_quota:
        q=re.split("\s+", line)
       
        if(domain_pattern.match(q[0])):
            mailbox=q[0].replace(args.domain, '')[:-1]+'@'+args.domain
            domain_return[mailbox]=filesize(int(q[2])*1024)

    #print(domain_return)

    json_return['progress']=100
    json_return['status']=1
    json_return['message']='Data of occupied space of mailboxes'

    json_return['data']=domain_return

    print(json.dumps(json_return))

    
    """

        json_return['error']=1
        json_return['status']=1
        json_return['progress']=100
        json_return['message']='Error: mailbox no exists'

        print(json.dumps(json_return))        
   """

if __name__=='__main__':
    
    get_quotas()
