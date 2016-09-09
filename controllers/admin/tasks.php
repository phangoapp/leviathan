<?php

use PhangoApp\PhaLibs\SimpleList;
use PhangoApp\PhaLibs\GenerateAdminClass;
use PhangoApp\PhaModels\Webmodel;
use PhangoApp\PhaView\View;
use PhangoApp\PhaI18n\I18n;
use PhangoApp\PhaLibs\AdminUtils;
use PhangoApp\PhaLibs\ParentLinks;
use PhangoApp\PhaUtils\MenuSelected;

Webmodel::load_model('vendor/phangoapp/leviathan/models/servers');
Webmodel::load_model('vendor/phangoapp/leviathan/models/tasks');

function TasksAdmin()
{
    
    settype($_GET['op'], 'integer');
    settype($_GET['server_id'], 'integer');
    settype($_GET['task_id'], 'integer');
    
    $s=new Server();
    
    $arr_server=$s->select_a_row($_GET['server_id']);
    
    if($arr_server)
    {
        
        switch($_GET['op'])
        {
            
            default:
            
                ?>
                <p><a href="<?php echo AdminUtils::set_admin_link('leviathan/servers'); ?>"><?php echo I18n::lang('phangoapp/leviathan', 'servers', 'Servers'); ?></a> &gt;&gt; Tasks - <?php echo $arr_server['hostname']; ?></p>
                <?php
            
                $t=new Task();
            
                $list=new SimpleList($t, '');
                
                $list->arr_fields_showed=['name_task', 'description_task'];
                
                $list->order_by=['IdTask' => 1];
                
                $list->options_func='options_task';
                
                $list->show();
            
            break;
            
            case 1:
                
                ?>
                <p><a href="<?php echo AdminUtils::set_admin_link('leviathan/servers'); ?>"><?php echo I18n::lang('phangoapp/leviathan', 'servers', 'Servers'); ?></a> &gt;&gt; <a href="<?php echo AdminUtils::set_admin_link('leviathan/tasks', ['server_id' => $_GET['server_id']]); ?>">Tasks - <?php echo $arr_server['hostname']; ?></a></p>
                <?php
                
                $l=new LogTask();
            
                $list=new SimpleList($l);
                
                $list->num_by_page=100;
                
                $list->where_sql=['where task_id=?', [$_GET['task_id']]];
                
                $list->url_options=AdminUtils::set_admin_link('leviathan/tasks', ['op' => 2, 'task_id' => $_GET['task_id'], 'server_id' => $_GET['server_id']]);
                
                $list->arr_fields_showed=['task_id', 'message', 'status', 'error'];
                
                $list->order_by=['IdLogtask' => 1];
                
                $list->show();
                
                //$list->options_func='options_task';
            
            break;
            
        }
        
    }
}

function options_task($url_options, $model_name, $id, $arr_row)
{
    
    $arr_options=['<a href="'.AdminUtils::set_admin_link('leviathan/tasks', ['op' => 1, 'task_id' => $id, 'server_id' => $_GET['server_id']]).'">'.I18n::lang('phangoapp/leviathan', 'view_log', 'View log').'</a>'];
    
    $arr_options[]='<a target="_blank" href="'.AdminUtils::set_admin_link('leviathan/showprogress', ['task_id' => $id, 'server' => $arr_row['server']]).'">'.I18n::lang('phangoapp/leviathan', 'view_progress', 'View progress').'</a>';
    
    return $arr_options;
    
}

?>
