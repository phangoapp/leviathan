<?php

use PhangoApp\PhaModels\Webmodel;
use PhangoApp\Leviathan;
use PhangoApp\Leviathan\ConfigTask;

gc_enable();
gc_collect_cycles();

Webmodel::load_model('vendor/phangoapp/leviathan/models/tasks');
Webmodel::load_model('vendor/phangoapp/leviathan/models/servers');

function InstallConsole()
{

    echo "All installed...";

}

?>
