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

function MakeTaskAdmin()
{
    
    settype($_GET['op'], 'integer');
    
    switch($_GET['op'])
    {
        
        default:
    
            PhangoApp\PhaLibs\AdminUtils::$show_admin_view=false;
            
            settype($_POST['group_id'], 'integer');
            settype($_POST['all_servers'], 'integer');
            
            //Include task
            
            if(isset($_POST['task']))
            {
                
                
                $task=base64_decode($_POST['task']);
                
                if(!$task)
                {
                    
                    die;
                    
                }

            }
            else
            {
                
                die;
                
            }
            
            //Check task
            
            $arr_path=explode('/', $task);
            
            array_walk($arr_path, function (&$item, $key) {
               
                $item=PhangoApp\PhaUtils\Utils::slugify($item, $respect_upper=0, $replace_space='-', $replace_dot=1, $replace_barr=0);
                
            });
            
            $task_path=implode('/', $arr_path);
            
            if(!is_file($task_path.'/task.php'))
            {
               $task_path='vendor/phangoapp/leviathan/'.$task_path;
                
            }
            
            if(is_file($task_path.'/task.php'))
            {   
                include($task_path.'/task.php');
                
                $taskmodel=new ServerTask();
                
                $taskmodel->define();
                
                $post=['name_task' => $taskmodel->name_task, 'description_task' => $taskmodel->description_task, 'codename_task' => $taskmodel->codename_task, 'path' => $task_path];
                
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
                
                $t=new Task();
                
                $t->create_forms();
                
                if($t->insert($post))
                {
                    $id=$t->insert_id();
                    
                    //Redirect to form
                    
                    if($taskmodel->yes_form)
                    {
                    
                        die(header('Location: '.AdminUtils::set_admin_link('leviathan/maketask', ['op' => 1, 'task_id' => $id])));
                        
                    }
                    else
                    {
                        
                        $client=new GuzzleHttp\Client();
                                
                        $client->request('GET', ConfigTask::$url_server, [
                            'query' => ['task_id' => $id, 'api_key' => ConfigTask::$api_key]
                        ]);
                        
                        die(header('Location: '.AdminUtils::set_admin_link('leviathan/showmultiprogress', ['task_id' => $id])));
                        
                    }
                    
                }
                else
                {
                    
                    PhangoApp\PhaLibs\AdminUtils::$show_admin_view=true;
                    
                    echo 'Error: cannot set the task';
                    
                }
            }
            else
            {
                
                echo 'Error: not exists task specification file';
                
            }
            /*$form=$taskmodel->form();
            
            if($form!='')
            {
                
                echo $form;
                
            }*/
            
            //insert task
            
            //Show form
            
            //Update task with new form
            
            
            
        break;
        
        case 1:
        
            settype($_GET['task_id'], 'integer');
        
            $t=new Task();
            
            $arr_task=$t->select_a_row($_GET['task_id']);
            
            //Check task
            
            $arr_path=explode('/', $arr_task['path']);
            
            array_walk($arr_path, function (&$item, $key) {
               
                $item=PhangoApp\PhaUtils\Utils::slugify($item, $respect_upper=0, $replace_space='-', $replace_dot=1, $replace_barr=0);
                
            });
            
            $task_path=implode('/', $arr_path);
            
            if(!is_file($task_path.'/task.php'))
            {
               $task_path='vendor/phangoapp/leviathan/'.$task_path;
                
            }
            
            if(is_file($task_path.'/task.php'))
            { 
                include($task_path.'/task.php');
                
                $taskmodel=new ServerTask();
                
                $taskmodel->define();
                
                echo $taskmodel->form();
            }
        
        break;
        
        
    }
    
}

?>
