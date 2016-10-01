<?php

use PhangoApp\PhaLibs\SimpleList;
use PhangoApp\PhaLibs\GenerateAdminClass;
use PhangoApp\PhaModels\Webmodel;
use PhangoApp\PhaView\View;
use PhangoApp\PhaI18n\I18n;
use PhangoApp\PhaLibs\AdminUtils;
use PhangoApp\PhaLibs\ParentLinks;
use PhangoApp\Leviathan\ConfigTask;

Webmodel::load_model('vendor/phangoapp/leviathan/models/servers');
Webmodel::load_model('vendor/phangoapp/leviathan/models/tasks');

function UpdatesAdmin()
{
    
    PhangoApp\PhaLibs\AdminUtils::$show_admin_view=false;
    
    settype($_POST['group_id'], 'integer');
    settype($_POST['all_servers'], 'integer');
    
    $post=['name_task' => I18n::lang('phangoapp/leviathan', 'update_servers', 'Update servers'), 'description_task' => I18n::lang('phangoapp/leviathan', 'update_servers_os', 'Update server using the native package manager'), 'codename_task' => 'update_server', 'url_return' => AdminUtils::set_admin_link('leviathan/servers'), 'path' => 'vendor/phangoapp/leviathan/tasks/system/updates'];
    
    $post['where_sql_server']='where 1=1';
    
    if($_POST['all_servers']==0)
    {
        
        if(isset($_POST['server'])>0)
        {
        
            $server=$_POST['server'];
        
            array_walk($server, function (&$item, $key) {
                
                settype($item, 'integer');
                
            });
            
            $post['where_sql_server'].=' AND IdServer IN ('.implode(', ', $server).')';
            
        }
        
    }
    
    if($_POST['group_id']>0)
    {
        
        $post['where_sql_server'].=' AND IdServer IN (select server_id from servergroupitem where group_id='.$_POST['group_id'].')';
        
    }
    
    $post['where_sql_server'].=' AND num_updates>0';
    
    $t=new Task();
    
    $t->create_forms();
    
    if($t->insert($post))
    {
        $id=$t->insert_id();
        
        //Send guzzle 
        $client=new GuzzleHttp\Client();
                    
        $client->request('GET', ConfigTask::$url_server, [
            'query' => ['task_id' => $id, 'api_key' => ConfigTask::$api_key]
        ]);
        
        die(header('Location: '.AdminUtils::set_admin_link('leviathan/showmultiprogress', ['task_id' => $id])));
        
    }
    else
    {
        
        PhangoApp\PhaLibs\AdminUtils::$show_admin_view=true;
        
        echo 'Error: cannot set the task';
        
    }
    
}

?>
