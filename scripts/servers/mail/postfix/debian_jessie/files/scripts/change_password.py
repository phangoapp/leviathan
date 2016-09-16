#!/usr/bin/python3 -u

import time
import os
import re
import argparse
import json
import pwd
import sys
import crypt
from subprocess import call, DEVNULL

def lock_file(file_lock):
    with open('/tmp/lock_'+file_lock, 'w') as f:
        f.write('lock')
    return True
        
def unlock_file(file_lock):
    os.remove('/tmp/lock_'+file_lock)
    
def check_lock(file_lock):
    while os.path.isfile('/tmp/lock_'+file_lock)==True:
        time.sleep(1)
    
    lock_file(file_lock)
    
    return True

def add_user():

    parser=argparse.ArgumentParser(prog='change_password.py', description='A tool for change password of a mailbox user')

    parser.add_argument('--user', help='User mailbox', required=True)
    
    parser.add_argument('--password', help='Password of the user', required=True)
    
    args=parser.parse_args()

    json_return={'error':0, 'status': 0, 'progress': 0, 'no_progress':0, 'message': ''}
    
    check_lock('virtual_domains')


    #domain_check=re.compile('^(([a-zA-Z]{1})|([a-zA-Z]{1}[a-zA-Z]{1})|([a-zA-Z]{1}[0-9]{1})|([0-9]{1}[a-zA-Z]{1})|([a-zA-Z0-9][a-zA-Z0-9-_]{1,61}[a-zA-Z0-9]))\.([a-zA-Z]{2,6}|[a-zA-Z0-9-]{2,30}\.[a-zA-Z]{2,3})$')

    user_check=re.compile('^[a-zA-Z0-9-_|\.]+$')

    if not user_check.match(args.user):
        json_return['error']=1
        json_return['status']=1
        json_return['progress']=100
        json_return['message']='Error: domain or user is not valid'
        
        print(json.dumps(json_return))

        unlock_file('virtual_domains')

        exit(1)

    json_return['progress']=50
    json_return['message']='Is a valid domain and user'

    print(json.dumps(json_return))

    time.sleep(1)
    
    try:
        user_pwd=pwd.getpwnam(args.user)
        
        #salt=crypt.mksalt(crypt.METHOD_SHA512)
            
        #password=crypt.crypt(args.password, salt).replace('$', '\$')
        
        if call("sudo echo \"%s:%s\" | chpasswd" % (args.user, args.password),  shell=True, stdout=DEVNULL) > 0:
            json_return['error']=1
            json_return['status']=1
            json_return['progress']=100
            json_return['message']='Error: user no exists in system'

            print(json.dumps(json_return))
            
            unlock_file('virtual_domains')

            exit(1)
    
        json_return['progress']=100
        json_return['message']='Changed password succesfully'

        print(json.dumps(json_return))
        unlock_file('virtual_domains')
        exit(0)

    except KeyError:
    
        json_return['error']=1
        json_return['status']=1
        json_return['progress']=100
        json_return['message']='Error: user not exists'

        print(json.dumps(json_return))
        
        unlock_file('virtual_domains')
        
        sys.exit(1)
    
        
    #echo "USERNAME:NEWPASSWORD" | chpasswd
    
    """
    yes_domain=0

    #Check if domain exists
    line_domain=args.domain+' '+args.domain
    final_domains=[]
    with open('/etc/postfix/virtual_domains') as f:
        for domain in f:
            if domain.strip()==line_domain:
                yes_domain=1

    if yes_domain==1:
        # check if user exits

        try:
            user_pwd=pwd.getpwnam(args.user+'_'+args.domain)
            json_return['error']=1
            json_return['status']=1
            json_return['progress']=100
            json_return['message']='Error: user exists'

            print(json.dumps(json_return))
            
            unlock_file('virtual_domains')
            
            sys.exit(1)

        except KeyError:
     
            # add user
           
            stat_group=os.stat('/home/%s' % args.domain)

            gid=stat_group.st_gid
            
            salt=crypt.mksalt(crypt.METHOD_SHA512)
            
            password=crypt.crypt(args.password, salt).replace('$', '\$')

            if call("sudo useradd -m -s /usr/sbin/nologin -g %i -p \"%s\" -d /home/%s/%s@%s %s_%s" % (gid, password, args.domain, args.user, args.domain, args.user, args.domain),  shell=True, stdout=DEVNULL) > 0:
                json_return['error']=1
                json_return['status']=1
                json_return['progress']=100
                json_return['message']='Error: cannot create a new user'

                print(json.dumps(json_return))
                
                unlock_file('virtual_domains')

                exit(1)
    
            json_return['progress']=40
            json_return['message']='User added'

            print(json.dumps(json_return))

            time.sleep(1)

            # Add user to virtual_mailbox

            mailbox=args.user+'@'+args.domain
            mailbox_user=args.user+'_'+args.domain

            with open('/etc/postfix/virtual_mailbox', 'a') as f:
                if f.write(mailbox+' '+mailbox_user+"\n"):
                    json_return['progress']=80
                    json_return['message']='Mailbox added'

                    print(json.dumps(json_return))
                else:
                   json_return['error']=1
                   json_return['status']=1
                   json_return['progress']=100
                   json_return['message']='Error: cannot add the new mailbox to file'

                   print(json.dumps(json_return))
                   
                   unlock_file('virtual_domains')

                   exit(1)          
            

            if call("postmap hash:/etc/postfix/virtual_mailbox",  shell=True, stdout=DEVNULL) > 0:

                json_return['error']=1
                json_return['status']=1
                json_return['progress']=100
                json_return['message']='Error: cannot refresh the domain mapper'

                print(json.dumps(json_return))
                
                unlock_file('virtual_domains')

                exit(1)
           
            # add quota

            try:

                args.quota=int(args.quota)
                num_m=0
                num_mhard=0

            except:

                args.quota=0
                num_m=0
                num_mhard=0

            if args.quota>0:
                num_m=args.quota*1024
                num_mhard=(num_m*0.10)+num_m
            if call("sudo setquota -u %s -a %d %d 0 0" % (mailbox_user, num_m, num_mhard),  shell=True, stdout=DEVNULL) > 0:
                json_return['error']=1
                json_return['status']=1
                json_return['progress']=100
                json_return['message']='Error: cannot add quota to this user group'

                print(json.dumps(json_return))
                
                unlock_file('virtual_domains')

                exit(1)
            else:
                json_return['progress']=90
                json_return['message']='Quota added sucessfully'
                print(json.dumps(json_return))

                time.sleep(1)

            json_return['progress']=100
            json_return['status']=1
            json_return['message']='New mailbox account added sucessfully'

            print(json.dumps(json_return))
    else:
        json_return['error']=1
        json_return['status']=1
        json_return['progress']=100
        json_return['message']='Error: no exists the domain'

        print(json.dumps(json_return))
        
        unlock_file('virtual_domains')
        
        exit(1)
    
    """
    
    unlock_file('virtual_domains')
        

if __name__=='__main__':
    add_user()

