<?php

use PhangoApp\PhaModels\Webmodel;
use PhangoApp\Leviathan;
use PhangoApp\Leviathan\ConfigTask;
use PhangoApp\PhaUtils\

gc_enable();
gc_collect_cycles();

Webmodel::load_model('vendor/phangoapp/leviathan/models/tasks');
Webmodel::load_model('vendor/phangoapp/leviathan/models/servers');

function InstallConsole()
{

    //Create ssh keys
    
    ConfigTask::$ssh_key_priv
    
    $ssh_pass=generate_random_password(20);
    
    $keygen=`ssh-keygen -t rsa -P "${ssh_pass}" -f "${ConfigTask::$ssh_key_priv[0]}"`:

    echo "Leviathan module installed...\n";

}

?>
