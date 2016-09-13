<?php

use PhangoApp\PhaModels\Webmodel;
use PhangoApp\Leviathan;
use PhangoApp\Leviathan\ConfigTask;

gc_enable();
gc_collect_cycles();

Webmodel::load_model('vendor/phangoapp/leviathan/models/tasks');
Webmodel::load_model('vendor/phangoapp/leviathan/models/servers');

function TaskConsole($task_id)
{
    
    settype($task_id, 'integer');
    
    $taskmodel=new \Task();
    
    $logtask=new \LogTask();
    
    $server=new \Server();
    
    $arr_task=$taskmodel->select_a_row($task_id);
    
    if(!$arr_task)
    {
        
        //$logtask->log(['task_id' => $task_id, 'error' => 1, 'progress' => 100, 'message' =>  'Task doesn\'t exists', 'server' => '']);
        echo json_encode(['error' => 1, 'status' => 1, 'message' => 'Error: no exists task']);
        exit(1);
        
    }
    
    //Delete data and password
    
    if($arr_task['password']!='')
    {
        
        //Delete password from task
        
        $taskmodel->fields_to_update=['password'];
        
        $taskmodel->reset_require();
        
        $taskmodel->set_conditions(['where IdTask=?', [$task_id]]);
        
        //$taskmodel->update(['password' => '']);
        
        $taskmodel->reload_require();
    }
    
    
    if($arr_task['path']=='')
    {
        
        $logtask->log(['task_id' => $task_id, 'error' => 1, 'status' => 1, 'progress' => 100, 'message' =>  'Cannot load any task from a php file', 'server' => $arr_task['server']]);
        exit(1);
        
    }
    
    //Filter task
    
    $arr_path=explode('/', $arr_task['path']);
    
    array_walk($arr_path, function (&$item, $key) {
        
        $item=str_replace('.', '_', $item);
        $item=str_replace("\0", '', $item);
        $item=basename($item);
        
    });
    
    $final_path=implode('/',$arr_path);
    
    $info_path=$final_path.'/info.php';
    
    $task_path=$final_path.'/task.php';
    
    if(file_exists($task_path))
    {
        
        require($task_path);
        
    }
    else
    {

        $old_task_path=$task_path;

        $task_path='vendor/phangoapp/leviathan/'.$task_path;
        
        if(file_exists($task_path))
        {
            
            require($task_path);
            
        }        
        else
        {
            
            $logtask->log(['task_id' => $task_id, 'error' => 1, 'status' => 1, 'progress' => 100, 'message' =>  'Cannot load any task from a php file: '.$old_task_path.' and '.$task_path, 'server' => $arr_task['server']]);
            exit(1);
            
        }
        
    }
    
    $task=new ServerTask();
        
    if($arr_task['user']!='')
    {
        
        $task->user=$arr_task['user'];
        
    }
    
    if($arr_task['password']!='')
    {
    
        $task->password=$arr_task['password'];
        
    }
    
    if($arr_task['os_codename']!='')
    {
        
        $task->os_server=$arr_task['os_codename'];
        
    }
    
    if($arr_task['data']!=='')
    {
        
        $arr_task['data']=json_decode($arr_task['data'], true);
        
        $taskmodel->fields_to_update=['data'];
        
        $taskmodel->reset_require();
        
        $taskmodel->set_conditions(['where IdTask=?', [$task_id]]);
        
        //$taskmodel->update(['password' => '']);
        
        $taskmodel->reload_require();
        
    }
    else
    {
        
        $arr_task['data']=[];
        
    }
    
    if($arr_task['user_path']!=='')
    {
        
        Leviathan\ConfigTask::$ssh_path=$arr_task['user_path'];
        
    }
    
    $task->data=$arr_task['data'];
    
    $task->define();
    
    $task->process_data();
    
    $yes_server=0;
    
    if($arr_task['server']!='')
    {
        $yes_server=1;
        
        $task->server=$arr_task['server'];
        
        $task->exec($task_id);
        
    }
    else
    if($arr_task['where_sql_server']!='')
    {
        
        $yes_server=1;
        
        $arr_servers=$server->where($arr_task['where_sql_server'])->select_to_list();
        
        $z=0;
        
        $arr_pid=[];
        
        $z=0;
        
        foreach($arr_servers as $arr_server)
        {
            
            $arr_pid[$z]=pcntl_fork();
            
            if($arr_pid[$z]==-1) 
            {
                
                $logtask->log(['task_id' => $task_id, 'error' => 1, 'status' => 1, 'progress' => 100, 'message' =>  'Cannot fork!!!', 'server' => '']);
                exit(1);
                
            } 
            else 
            if($arr_pid[$z]) 
            {
                ++$z;
                
                if($z>=ConfigTask::$num_forks)
                {
                     
                     pcntl_waitpid(0, $status);
                     
                     $z=0;
                     
                }
                
            } 
            else 
            {
                
                //Reconnect to sql database for the fork
                
                $taskmodel->connect_to_db();
                
                $task->server=$arr_server['ip'];
                
                $task->os_server=$arr_server['os_codename'];
            
                $task->exec($task_id);
                
                //Clean sql connection of the fork
                
                $taskmodel->close();
                
                //Finish the script fork
                
                exit(0);
                 
            }
            
        }
        
        pcntl_wait($status); //Protect against Zombie children

        
    }
    
    if($yes_server==0)
    {
        
        $logtask->log(['task_id' => $task_id, 'error' => 1, 'status' => 1, 'progress' => 100, 'message' =>  'No servers defined', 'server' => '']);
        exit(1);
        
    }
    
}

?>
