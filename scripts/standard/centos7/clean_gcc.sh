#!/bin/bash

sudo yum -y remove gcc 

if [ $? -eq 0 ]; then
    echo "Cleaned gcc sucessfully"
else
    echo "Error dropping gcc..."
    exit;
fi
