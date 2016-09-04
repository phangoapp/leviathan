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
    
    $s=new Server();
    $g=new ServerGroup();
    $t=new Task();
    $tasklog=new LogTask();
    $os=new OsServer();
    
    # Need check the server
            
    $arr_task=$t->select_a_row($_GET['task_id']);
            
    if($arr_task)
    {
        
        $s->set_conditions(['where ip=?', [$_GET['server']] ]);
                
        $arr_server=$s->select_a_row_where();
                
        if($arr_server)
        {
                    
            switch($_GET['op'])
            {
                default:
            
                    echo View::load_view([$arr_task['name_task'], $arr_server['hostname'], $arr_task['description_task'], $_GET['task_id'], $arr_server['ip']] , 'leviathan/progress', 'phangoapp/leviathan');
                
                break;
            
                case 1:
                
                    PhangoApp\PhaLibs\AdminUtils::$show_admin_view=false;
                        
                    header('Content-type: text/plain');
                    
                    $tasklog->set_limit([$_GET['position'], 20]);
                
                    $tasklog->set_order(['IdLogtask' => 0]);
                    
                    $tasklog->set_conditions=(['WHERE task_id=?', [$_GET['task_id']]]);
                    
                    $tasklog->reset_conditions=false;
                    
                    $c=$tasklog->select_count();
                    
                    if($c==0)
                    {
                        
                        //Select last row, if status=1 go to ending
                        
                        echo json_encode(['wait'=> 1]);
                        
                    }
                    else
                    {
                        
                        $arr_rows=$tasklog->select_to_array();
                        
                        echo '[]';
                        
                    }
                    
                    /*
                    if($_GET['server']!='')
                    {
                        $tasklog->set_conditions(['WHERE task_id=? and logtask.server=?', [$_GET['task_id'], $_GET['server']]]);
                    }
                        
                    #$tasklog->set_limit([position, 1])
                    
                    #arr_row=$tasklog->select_a_row_where([], 1, position)
                    
                    $tasklog->reset_conditions=false;
                    
                    $c=$tasklog->select_count();
                    
                    if($c>0)
                    {
                        
                        $arr_rows=[];
                        
                        $cursor=$tasklog->select([], true);
                        
                        while($arr_row=$tasklog->fetch_row($cursor))
                        {
                            $arr_rows[]=$arr_row;
                        }
                        
                        if(count($arr_rows)==0)
                        {
                            $tasklog->set_limit([1]);
                        
                            $tasklog->set_order(['IdLogtask' => 0]);
                            
                            $tasklog->set_conditions(['WHERE task_id=? and status=? and error=?  and server=?', [$_GET['task_id'], 1, 1, '']]);
                            
                            if($tasklog->select_count()==0)
                            {
                                
                                if($arr_task['status']=='0' || $arr_task['status']==0)
                                {
                                    return json_encode(['wait'=> 1]);
                                }
                                else
                                {
                                    return json_encode([]);
                                }
                            }
                            else 
                            {
                                
                                $tasklog->set_limit([1]);
                            
                                $tasklog->set_order(['IdLogtask' => 0]);
                                
                                $tasklog->set_conditions(['WHERE task_id=%s and status=1 and error=1  and server=""', [task_id]]);
                                
                                $arr_rows=$tasklog->select_to_array([], true);
                            }
                        }
                        #response.set_header('Content-type', 'text/plain')
                        
                        echo json_encode($arr_rows);
                    }    
                    else
                    {
                        echo json_encode(['wait'=> 1]);
                    }*/
                
                break;
            
            }
            
            
        }
    }
}

?>
