<?php

use PhangoApp\PhaModels\Webmodel;
use PhangoApp\Leviathan;
use PhangoApp\Leviathan\ConfigTask;
use PhangoApp\PhaUtils\Utils;

gc_enable();
gc_collect_cycles();

Webmodel::load_model('vendor/phangoapp/leviathan/models/tasks');
Webmodel::load_model('vendor/phangoapp/leviathan/models/servers');

function InstallConsole()
{

    //Create ssh keys
    
    mkdir('ssh/', 0755, true);
    
    $ssh_pass=Utils::generate_random_password(20);
    
    exec_command("ssh-keygen -t rsa -P \"".$ssh_pass."\" -f \"".ConfigTask::$ssh_key_priv[0]."\"", 'Error:cannot create the ssh key');

    echo "Leviathan module installed...\n";

}

function exec_command($command, $error_text)
{
    $descriptorspec = [
        0 => array("pipe", "r")  
    ];
    
    $process = proc_open($command, $descriptorspec, $pipes);

    if(is_resource($process)) 
    {
        
        while($text=fread($pipes[0], 4096))
        {
            
            echo $text;
            
        }
        
    }
    else
    {
        
        echo $error_text."\n";
        exit(1);
        
    }

    proc_close($process);

    
}


?>
