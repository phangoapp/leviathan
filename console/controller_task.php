<?php

use PhangoApp\PhaModels\Webmodel;
use PhangoApp\Leviathan;

gc_enable();
gc_collect_cycles();

Webmodel::load_model('vendor/phangoapp/leviathan/models/tasks');

function TaskConsole($task_id)
{
    
    $taskmodel=new \Task();
    
    $arr_task=$taskmodel->select_a_row($task_id);
    
    $task=new Leviathan\Task();
    
    $task->name_task=$arr_task['name_task'];
    $task->description_task=$arr_task['description_task'];
    
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
    
    print_r($arr_task);
    
}

?>
