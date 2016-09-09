<?php

use PhangoApp\PhaLibs\SimpleList;
use PhangoApp\PhaModels\Webmodel;
use PhangoApp\PhaView\View;
use PhangoApp\PhaI18n\I18n;
use PhangoApp\PhaModels\ModelForm;
use PhangoApp\Leviathan\ConfigTask;

Webmodel::load_model('vendor/phangoapp/leviathan/models/servers');
Webmodel::load_model('vendor/phangoapp/leviathan/models/tasks');

function ShowMultiProgressAdmin()
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
                
                $num_servers=$s->where($arr_task['where_sql_server'])->select_count();
                
                echo View::load_view([$arr_task['name_task'], $arr_task['description_task'], $_GET['task_id'], $num_servers] , 'leviathan/multiprogress', 'phangoapp/leviathan');
            
            break;
        
            case 1:
                
                //Get servers
                PhangoApp\PhaLibs\AdminUtils::$show_admin_view=false;
                
                $s->set_conditions(['WHERE ip IN (select DISTINCT server from logtask where task_id=?)', [$arr_task['IdTask']]]);
        
                $s->set_limit([$_GET['position'], ConfigTask::$num_forks]);
                
                $arr_server=$s->select_to_list(['hostname', 'ip']);
                
                if($arr_server)
                {
                    
                    echo json_encode(['servers'=> $arr_server, 'error'=> 0]);
                    die;
                }
                else
                {
                    
                    $logtask->set_conditions(['where task_id=? and server=?', [$arr_task['IdTask'], '']]);
                    
                    $logtask->set_order(['id' => 1]);
                    
                    $arr_tasklog=$logtask->select_a_row_where([], true);
                    
                    if($arr_tasklog)
                    {
                        
                        if($arr_tasklog['error']==1)
                        {
                            echo json_encode($arr_tasklog);
                            die;
                        }    
                        else
                        {
                            
                            echo json_encode(['error'=> 0, 'servers'=> []]);
                            die;
                            
                        }
                    }
                    else
                    {
                        echo json_encode(['error'=> 0, 'servers'=> []]);
                        die;
                        
                    }
                }
            
            break;
            
            case 2:
            
                //get progress
                
                PhangoApp\PhaLibs\AdminUtils::$show_admin_view=false;
                
                settype($_POST['servers'], 'string');
                
                $servers=json_decode($_POST['servers']);
                
                if(!$servers)
                {
                    
                    $servers=[];
                    
                }
                
                #for ip in servers:
                
                if(count($servers)>0)
                {
                
                    $logtask->set_order(['id' => 1]);
                    
                    $logtask->set_conditions(['WHERE task_id=? and status=? and error=? and server=?', [$arr_task['IdTask'], 1, 1, '']]);
                    
                    $c_error=$logtask->select_count();
                    
                    if($c_error==0)
                    {
                    
                        $logtask->set_order(['id' => 1]);
                        
                        $logtask->set_conditions(['WHERE task_id=? and status=? and server IN ? and server!=?', [$arr_task['IdTask'], 1, $servers, '']]);
                        
                        $arr_log=$logtask->select_to_array(['status', 'error', 'server']);
                        
                        $logtask->set_order(['id' => 1]);
                        
                        $logtask->set_conditions(['WHERE task_id=? and status=? and server NOT IN ? and server!=?', [$arr_task['IdTask'], 0, $servers, '']]);
                        
                        $arr_log2=$logtask->select_to_array(['status', 'error', 'server']);
                        
                        $arr_log=array_merge($arr_log2, $arr_log);
                        
                    }
                    else
                    {
                        
                        $arr_log=[];
                        
                        foreach($servers as $server)
                        {
                            
                            $arr_log[]=['status'=> 1, 'error'=> 1, 'server'=> $server];
                            
                        }
                            
                    }
                    
                    echo json_encode($arr_log);
                    die;
                
                }
                
                $arr_log=[];
                
                echo json_encode($arr_log);
                
                
            break;
        
        }
            
    }
    else
    {
        
        PhangoApp\PhaLibs\AdminUtils::$show_admin_view=false;
        
    }
}

?>
