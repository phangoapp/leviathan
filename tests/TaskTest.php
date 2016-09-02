<?php

use PhangoApp\PhaUtils\Utils;
use PhangoApp\Leviathan\Task;
use PhangoApp\PhaModels\Webmodel;
use PhangoApp\PhaRouter\Routes;

include("vendor/autoload.php");

Utils::load_config('config_routes', 'settings');
Utils::load_config('config', 'settings');

class TaskTest extends PHPUnit_Framework_TestCase
{
    
    public function testTask()
	{
        
        //You need SERVER_REMOTE constant in config. The server need debian os installed.
        
        //Need also define the ssh keys and password in config.php
        
        //Insert first a task
        
        /*$arr_task=['name_task' => 'live', 'descripton_task' => 'Script for check if server is alive', 'arguments' => [], 'status' => 0, 'url_return' => '', 'server' => SERVER_REMOTE];
        
        $new_task=$m->task->insert($arr_task);
        
        $id=$m->task->insert_id();
        
        $this->assertNotFalse($new_task);
        
        $arr_task['IdTask']=$id;*/
        
        //Select task from db
        
        //Execute task
        
        $task=new Task(SERVER_REMOTE);
        
        $task->files=[['vendor/phangoapp/leviathan/tests/script/alive.sh', 0755]];
        
        $task->commands_to_execute=[['/bin/bash', 'vendor/phangoapp/leviathan/tests/script/alive.sh', ''], ['sudo', 'vendor/phangoapp/leviathan/tests/script/alive.sh', '']];
        
        $task->delete_files=['vendor/phangoapp/leviathan/tests/script/alive.sh'];
        
        $task->delete_directories=['vendor/phangoapp/leviathan/tests'];
        
        $task->name_task='Live';
        
        $task->description_task='Check if server is alive';
        
        $task->codename_task='live';
        
        $result=$task->exec();
        
        echo $task->txt_error;
        
        $this->assertTrue($result);
        
        
        
    }

}

?>
