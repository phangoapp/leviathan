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

$mail=trim(readline('Email where send notifies: '));

$config=str_replace("define('EMAIL_NOTIFICATION', '');", "define('EMAIL_NOTIFICATION', '${mail}');", $config);

$mail=trim(readline('Email notifies sender: '));

$config=str_replace("define('EMAIL_NOTIFICATION_SENDER', '');", "define('EMAIL_NOTIFICATION_SENDER', '${mail}');", $config);

$mail=trim(readline('Email sender host: '));

$config=str_replace("define('EMAIL_NOTIFICATION_HOST', '');", "define('EMAIL_NOTIFICATION_HOST', '${mail}');", $config);

$mail=trim(readline('Email sender user: '));

$config=str_replace("define('EMAIL_NOTIFICATION_USER', '');", "define('EMAIL_NOTIFICATION_USER', '${mail}');", $config);

$mail=trim(readline('Email sender pass: '));

$config=str_replace("define('EMAIL_NOTIFICATION_PASS', '');", "define('EMAIL_NOTIFICATION_PASS', '${mail}');", $config);

file_put_contents('vendor/phangoapp/leviathan/settings/config.php', $config);

//Load cron

echo "Installing crontab file...\n";

$cron="*/5 * * * * php ".PhangoApp\PhaRouter\Routes::$base_path."/console.php -m=phangoapp/leviathan -c=check";

exec_command('echo "'.$cron.'" | crontab', 'Cannot install crontab file for check servers status');

echo "Install os server database...\n";

$os_server=new OsServer();

$os_server->create_forms();

$os_server->insert(['name'=> 'Debian Jessie', 'codename'=> 'debian_jessie']);
$os_server->insert(['name'=> 'Centos 7', 'codename'=> 'centos7']);

echo "Leviathan module installed...\n";

?>
