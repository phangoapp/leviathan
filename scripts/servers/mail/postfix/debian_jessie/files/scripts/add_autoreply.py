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
from subprocess import call, DEVNULL

def add_autoreply():

    parser=argparse.ArgumentParser(prog='add_redirection.py', description='A tool for add redirections')

    parser.add_argument('--mailbox', help='Mailbox to add redirection', required=True)
        
    args=parser.parse_args()

    domain=args.mailbox.split("@")[1]

    home_mailbox='/home/'+domain+'/'+args.mailbox+'/.vacations'

    json_return={'error':0, 'status': 0, 'progress': 0, 'no_progress':0, 'message': ''}

    if os.path.isdir('/home/'+domain+'/'+args.mailbox):

        config = configparser.ConfigParser()

        config['vacation']={'subject': "", 'text': ""}

        with open('tmp/'+args.mailbox+'_autoreply_subject') as f:
            subject=f.read()
            config['vacation']['subject']=subject

        with open('tmp/'+args.mailbox+'_autoreply_text') as f:
            text=f.read()
            config['vacation']['text']=text

        with open(home_mailbox, 'w') as configfile:
            config.write(configfile)

        json_return['progress']=100
        json_return['status']=1
        json_return['message']='Added autoreply file to mailbox directory'

        print(json.dumps(json_return))

    else:
        json_return['error']=1
        json_return['status']=1
        json_return['progress']=100
        json_return['message']='Error: mailbox no exists'

        print(json.dumps(json_return))        

if __name__=='__main__':
    
    add_autoreply()
