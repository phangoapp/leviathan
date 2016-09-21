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
    
    pcntl_exec("ssh-keygen -t rsa -P \"".$ssh_pass."\" -f \"".ConfigTask::$ssh_key_priv[0]."\"");

    echo "Leviathan module installed...\n";

}

?>
