<?php

use PhangoApp\PhaLibs\SimpleList;
use PhangoApp\PhaModels\Webmodel;
use PhangoApp\PhaView\View;
use PhangoApp\PhaI18n\I18n;
use PhangoApp\PhaModels\ModelForm;
use PhangoApp\Leviathan\ConfigTask;

Webmodel::load_model('vendor/phangoapp/leviathan/models/servers');
Webmodel::load_model('vendor/phangoapp/leviathan/models/tasks');

function ShowProgressAdmin()
{
    settype($_GET['op'], 'integer');
    settype($_GET['task_id'], 'integer');
    settype($_GET['server'], 'string');
    settype($_GET['position'], 'integer');
    
    $_GET['server']=PhangoApp\PhaUtils\Utils::form_text($_GET['server']);
    
    $s=new Server();
    $g=new ServerGroup();
    $t=new Task();
    $logtask=new LogTask();
    $os=new OsServer();
    
    # Need check the server
            
    $arr_task=$t->select_a_row($_GET['task_id']);
            
    if($arr_task)
    {
        
                    
        switch($_GET['op'])
        {
            default:
                
                echo View::load_view([$arr_task['name_task'], $arr_task['hostname'], $arr_task['description_task'], $_GET['task_id'], $_GET['server']] , 'leviathan/progress', 'phangoapp/leviathan');
            
            break;
        
            case 1:
            
                PhangoApp\PhaLibs\AdminUtils::$show_admin_view=false;
                    
                header('Content-type: text/plain');
                
                $logtask->reset_conditions=0;
                
                $logtask->set_limit([$_GET['position'], 20]);
            
                $logtask->set_order(['IdLogtask' => 0]);
                
                $logtask->set_conditions(['WHERE task_id=? and server=?', [$_GET['task_id'], $_GET['server']]]);
                
                $c=$logtask->select_count('IdLogtask', [], false);
                
                if($c==0)
                {
                    
                    //Select last row, if status=1 go to ending
                    
                    echo json_encode(['wait'=> 1]);
                    
                }
                else
                {
                    
                    $arr_rows=$logtask->select_to_list([], true);
                    
                    echo json_encode($arr_rows);
                    
                }
            
            break;
        
        }
            
    }
}

?>
