<?php

use PhangoApp\PhaModels\Webmodel;
use PhangoApp\Leviathan;
use PhangoApp\Leviathan\ConfigTask;
use PhangoApp\PhaUtils\Utils;
use PhangoApp\PhaRouter\Routes;

Webmodel::load_model('vendor/phangoapp/leviathan/models/tasks');
Webmodel::load_model('vendor/phangoapp/leviathan/models/servers');

//Create ssh keys

mkdir('ssh/', 0755, true);

$ssh_pass=Utils::generate_random_password(20);

exec_command("ssh-keygen -t rsa -P \"".str_replace('$', '\$', $ssh_pass)."\" -f \"".ConfigTask::$ssh_key_priv[0]."\"", 'Error:cannot create the ssh key');

copy('vendor/phangoapp/leviathan/settings/config.php.sample', 'vendor/phangoapp/leviathan/settings/config.php');

$config=file_get_contents('vendor/phangoapp/leviathan/settings/config.php');

$config=str_replace('ConfigTask::$ssh_key_password=[\'password\'];', 'ConfigTask::$ssh_key_password=[\''.$ssh_pass.'\'];', $config);

$config=str_replace('ConfigTask::$url_monit=\'http://host/index.php/leviathan/monit\';', 'ConfigTask::$url_monit=\'http://'.gethostname().Routes::$root_url.'index.php/leviathan/monit\';', $config);

file_put_contents('vendor/phangoapp/leviathan/settings/config.php', $config);

echo "Leviathan module installed...\n";

?>
