<?php

namespace PhangoApp\Leviathan;

class ConfigTask {


    static public $ssh_user='spanel';
    
    static public $ssh_path='/home/spanel';
    
    static public $ssh_key_priv=[];
    
    static public $ssh_key_pub=[];
    
    static public $ssh_key_password=[];
    
    static public $ssh_user_password='';
    
    static public $url_server='http://localhost:1337';
    
    static public $url_monit='';
    
    static public $servers=[1337];
    
    static public $api_key='';
    
    static public $error_log='./log/error.log';
    
    static public $php_path='/usr/bin/php';
    
    static public $known_hosts_file='';
    
    static public $ssh_port=22;
    
    static public $num_forks=20;
    

}

ConfigTask::$ssh_key_priv=[\PhangoApp\PhaRouter\Routes::$base_path.'/ssh/id_rsa'];
    
ConfigTask::$ssh_key_pub=[\PhangoApp\PhaRouter\Routes::$base_path.'/ssh/id_rsa.pub'];

ConfigTask::$known_hosts_file=\PhangoApp\PhaRouter\Routes::$base_path.'/ssh/known_hosts';

?>
