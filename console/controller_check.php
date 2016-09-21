<?php

use PhangoApp\PhaModels\Webmodel;
use PhangoApp\Leviathan;
use PhangoApp\Leviathan\ConfigTask;
use PhangoApp\PhaUtils\Utils;
use PhangoApp\PhaRouter\Routes;
use PhangoApp\PhaTime\DateTime;
use PhangoApp\PhaLibs\SendMail;
use PhangoApp\PhaLibs\AdminUtils;
use PhangoApp\PhaI18n\I18n;

gc_enable();
gc_collect_cycles();

Webmodel::load_model('vendor/phangoapp/leviathan/models/tasks');
Webmodel::load_model('vendor/phangoapp/leviathan/models/servers');

function checkConsole()
{

    $server=new Server();

    $now=DateTime::now();
            
    $timestamp_now=DateTime::obtain_timestamp($now);

    $five_minutes=$timestamp_now-300;

    $five_minutes_date=DateTime::format_timestamp($five_minutes);

    $arr_server=[];

    $query=$server->where(['where date<?', [$five_minutes_date]])->select(['hostname']);
    
    while($s=$server->fetch_array($query))
    {
        
        $arr_server[]=$s['hostname'];
        
    }

    if(!defined('EMAIL_NOTIFICATION_PORT'))
    {
        
        define('EMAIL_NOTIFICATION_PORT', 25);
        
    }
    
    if(!defined('EMAIL_NOTIFICATION_ENCRYPTION'))
    {
        
        define('EMAIL_NOTIFICATION_ENCRYPTION', '');
        
    }

    if(count($arr_server)>0)
    {
        
        $send=new SendMail(EMAIL_NOTIFICATION_SENDER, EMAIL_NOTIFICATION_HOST, EMAIL_NOTIFICATION_USER, EMAIL_NOTIFICATION_PASS, EMAIL_NOTIFICATION_PORT, EMAIL_NOTIFICATION_ENCRYPTION);
                    
        $content_mail="THE NEXT SERVERS ARE DOWN: ".implode(',', $arr_server)."\n\n";
        
        //http://localhost/leviathan/index.php/admin/leviathan/servers/get/op/0/group_id/0/type/down
        
        $content_mail.='Please, click in this link for view the servers down: '.AdminUtils::set_admin_link('leviathan/servers', ['op' => 0, 'group_id' => 0, 'type' => 'down']);
        //($email, $subject, $message, $content_type='plain', $arr_bcc=array(), $attachments=array())
        if($send->send(EMAIL_NOTIFICATION, I18n::lang('phangoapp/leviathan', 'servers_down', 'WARNING:  SERVERS ARE DOWN!'), $content_mail))
        {
           echo "Sended email with notification\n";
        }
            
    }

}


?>
