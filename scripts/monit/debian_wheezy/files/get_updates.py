#!/usr/bin/env python3

import subprocess
import re
import urllib.request
import urllib.parse

pattern=re.compile('^Inst (.*?)$')

if subprocess.call('sudo apt-get -y update',  shell=True) > 0:
    print('Error, cannot update apt-get')
    exit(1)
else:
    print('Your apt-get database is updated, checking if you have updates...')

with subprocess.Popen(["apt-get upgrade -s | grep \"^Inst \" | wc -l"], shell=True, stdout=subprocess.PIPE) as proc:
    
    num_updates=proc.stdout.read().decode("utf-8")

url="http://url/to/server/token/ip"

data = urllib.parse.urlencode({'num_updates': int(num_updates)})

data = data.encode('ascii')

content=urllib.request.urlopen(url, data)
