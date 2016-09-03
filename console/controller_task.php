<?php

use PhangoApp\PhaModels\Webmodel;
use PhangoApp\Leviathan;

gc_enable();
gc_collect_cycles();

Webmodel::load_model('vendor/phangoapp/leviathan/models/tasks');

function TaskConsole($task_id)
{
    
    $taskmodel=new \Task();
    
    $logtask=new \LogTask();
    
    $arr_task=$taskmodel->select_a_row($task_id);
    
    if(!$arr_task)
    {
        
        //$logtask->log(['task_id' => $task_id, 'error' => 1, 'progress' => 100, 'message' =>  'Task doesn\'t exists', 'server' => '']);
        echo json_encode(['error' => 1, 'status' => 1, 'message' => 'Error: no exists task']);
        exit(1);
        
    }
    
    
    if($arr_task['path']=='')
    {
        
        $logtask->log(['task_id' => $task_id, 'error' => 1, 'progress' => 100, 'message' =>  'Cannot load any task from a php file', 'server' => '']);
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
            
            $logtask->log(['task_id' => $task_id, 'error' => 1, 'progress' => 100, 'message' =>  'Cannot load any task from a php file: '.$old_task_path.' and '.$task_path, 'server' => '']);
            exit(1);
            
        }
        
    }
    
    $task=new ServerTask();
    
    if($arr_task['data']!=='')
    {
        
        $task->data=json_decode($arr_task['data'], true);
        
    }
    
    
    if($arr_task['server']!='')
    {
        
        $task->server=$arr_task['server'];
        $yes_server=1;
        
        $task->exec();
        
        //Execute task
        
    }
    else
    if($arr_task['where_sql_server']!='')
    {
        
        //$yes_server=1;
        
        //Execute tasks
        
        
        
    }
    
    if($yes_server==0)
    {
        
        $logtask->log(['task_id' => $task_id, 'error' => 1, 'progress' => 100, 'message' =>  'No servers defined', 'server' => '']);
        exit(1);
        
    }
    
    /*foreach($arr_path as $k => $piece)
    {
        
        
        
    }*/
    
    /*
    $task=new Leviathan\Task();
    
    $task->name_task=$arr_task['name_task'];
    $task->description_task=$arr_task['description_task'];
    $task->codename_task=$arr_task['codename_task'];
    $task->data=json_decode($arr_task['data'], true);
    $task->path=*/
    
    /*
    $task->files=json_decode($arr_task['files'], true);
    $task->commands_to_execute=json_decode($arr_task['commands_to_execute'], true);
    $task->delete_files=json_decode($arr_task['delete_files'], true);
    $task->delete_directories=json_decode($arr_task['delete_directories'], true);
    $task->one_time=$arr_task['one_time'];
    $task->version=$arr_task['version'];
    
    if($arr_task['post_func']!='')
    {
        
        include($arr_task['post_func']);
        
    }*/
    
    /*
    | name_task           | varchar(255) | NO   |     |         |                |
    | description_task    | varchar(255) | NO   |     |         |                |
    | codename_task       | varchar(255) | NO   |     |         |                |
    | path_task           | varchar(255) | NO   |     |         |                |
    | url_return          | varchar(255) | NO   |     |         |                |
    | data                | text         | NO   |     | NULL    |                |
    | files               | text         | NO   |     | NULL    |                |
    | commands_to_execute | text         | NO   |     | NULL    |                |
    | delete_files        | text         | NO   |     | NULL    |                |
    | delete_directories  | text         | NO   |     | NULL    |                |
    | one_time            | int(1)       | NO   |     | 0       |                |
    | version             | varchar(255) | NO   |     |         |                |
    | post_func           | varchar(255) | NO   |     |         |                |
    | pre_func            | varchar(255) | NO   |     |         |                |
    | error_func          | varchar(255) | NO   |     |         |                |
    */
    
}

?>
