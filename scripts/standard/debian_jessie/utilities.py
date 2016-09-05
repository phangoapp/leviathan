#!/usr/bin/env python3

import subprocess
import pwd
import os
import shutil
import re

def check_domain(domain):
    
    check_url = re.compile(
        r'^(?:http|ftp)s?://' # http:// or https://
        r'(?:(?:[A-Z0-9](?:[A-Z0-9-]{0,61}[A-Z0-9])?\.)+(?:[A-Z]{2,6}\.?|[A-Z0-9-]{2,}\.?)|' #domain...
        r'localhost|' #localhost...
        r'\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})' # ...or ip
        r'(?::\d+)?' # optional port
        r'(?:/?|[/?]\S+)$', re.IGNORECASE)
        
    if not check_url.match(domain):
        return False
        
    return True

def add_line(line, file_line):

    line=line.strip()

    line_exists=0

    try:

        file=open(file_line, 'r')
    
    except:
        
        return False

    for old_line in file:
        old_line=old_line.strip()
        
        if old_line == line:
            line_exists=1

    file.close()

    if line_exists==0:
        
        try:
        
            file=open(file_line, 'a')
            
            file.write(line+"\n")

            file.close()
        
            return True
        
        except:
            
            return False
        

def del_line(file_line, file_name):

    line_exists=0

    arr_lines=[]

    try:

        f=open(file_name, 'r')

    except:
        
        return False

    for line in f:
        line=line.strip()
        
        if line == file_line:
            line_exists=1
        else:
            arr_lines.append(line)

    f.close()

    if line_exists==1:
        
        try:
        
            f=open(file_name, 'w')
            
            final_file="\n".join(arr_lines)
            
            f.write(final_file+"\n")
                #print(line)
            f.close()
            
            return True
        
        except:
            
            return False
    else:
        
        return False

def del_user(username):
    
    try:
    
        user_check=pwd.getpwnam(username)
        
        # Delete user home
        
        if subprocess.call("sudo userdel -r "+username,  shell=True) > 0:
            return False
        else:
            return True

    except:
        
        return False

def add_user(username, home_base='/home', clean_user=True):

    user_folder=home_base+"/"+username

    try:

        user_check=pwd.getpwnam(username)

        if clean_user==True:
            return False
        else:
            return True

    except KeyError:

        if not os.path.isdir(home_base):
            os.mkdir(home_base, 0o755)
            
        if not os.path.isdir(user_folder):
            os.mkdir(user_folder, 0o755)

        if subprocess.call("sudo useradd -M -d "+user_folder+" -s /usr/sbin/nologin "+username,  shell=True) > 0:
            return False
        else:
            
            shutil.chown(user_folder, username, username)
            
            return True

#Save quota in mb

def set_quota_grp(group, quota, filesystem):
    
    #quotatool -u johan -b -q 50G -l 50G /home
    #Check 
    
    #print("sudo quotatool -g "+group+" -b -q "+str(quota)+"M -l "+str(quota+2048)+"M "+filesystem)
    #Check where is the /home directory
    
    # df -h .
    
    quota_hard=quota+2
    
    if subprocess.call("sudo quotatool -g "+group+" -b -q "+str(quota)+"M -l "+str(quota_hard)+"M "+filesystem,  shell=True) > 0:
        return False
    else:
        return True

def obtain_mountpoint_file(file_path):

    df = subprocess.Popen(["df", file_path], stdout=subprocess.PIPE)
    output = df.communicate()[0]
    """
    for line in df.stdout:
        pass
    print(line.decode('utf-8'))
    exit(0)
    """
    
    device, size, used, available, percent, mountpoint = output.decode('utf-8').split("\n")[1].split()
    
    return  mountpoint
