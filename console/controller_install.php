<?php

use PhangoApp\PhaModels\Webmodel;
use PhangoApp\Leviathan;
use PhangoApp\Leviathan\ConfigTask;
use PhangoApp\PhaUtils\Utils;
use PhangoApp\PhaRouter\Routes;

Webmodel::load_model('vendor/phangoapp/leviathan/models/tasks');
Webmodel::load_model('vendor/phangoapp/leviathan/models/servers');

function InstallConsole()
{

    //Create ssh keys
    
    mkdir('ssh/', 0755, true);
    
    $ssh_pass=Utils::generate_random_password(20);
    
    //exec_command("ssh-keygen -t rsa -P \"".str_replace('$', '\$', $ssh_pass)."\" -f \"".ConfigTask::$ssh_key_priv[0]."\"", 'Error:cannot create the ssh key');
    system("ssh-keygen -t rsa -P \"".str_replace('$', '\$', $ssh_pass)."\" -f \"".ConfigTask::$ssh_key_priv[0]."\"");

    copy('vendor/phangoapp/leviathan/settings/config.php.sample', 'vendor/phangoapp/leviathan/settings/config.php');
    
    $config=file_get_contents('vendor/phangoapp/leviathan/settings/config.php');
    
    $config=str_replace('ConfigTask::$ssh_key_password=[\'password\'];', 'ConfigTask::$ssh_key_password=[\''.$ssh_pass.'\'];', $config);
    
    $config=str_replace('ConfigTask::$url_monit=\'http://host/index.php/leviathan/monit\';', 'ConfigTask::$url_monit=\'http://'.gethostname().Routes::$root_url.'index.php/leviathan/monit\';', $config);
    
    $mail=readline('Email to send notifications: ');
    
    file_put_contents('vendor/phangoapp/leviathan/settings/config.php', $config);

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
