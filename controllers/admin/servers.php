<?php

use PhangoApp\PhaLibs\SimpleList;
use PhangoApp\PhaModels\Webmodel;
use PhangoApp\PhaView\View;
use PhangoApp\PhaI18n\I18n;
use PhangoApp\PhaLibs\AdminUtils;
use PhangoApp\PhaModels\ModelForm;
use PhangoApp\Leviathan\ConfigTask;
use PhangoApp\PhaTime\DateTime;

Webmodel::load_model('vendor/phangoapp/leviathan/models/servers');
Webmodel::load_model('vendor/phangoapp/leviathan/models/tasks');

function ServersAdmin()
{
    
    $s=new Server();
    $g=new ServerGroup();
    $i=new ServerGroupItem();
    $t=new Task();
    $os=new OsServer();
    
    settype($_GET['group_id'], 'integer');
    
    if(PhangoApp\PhaRouter\Routes::$request_method=='GET')
    {
        
        settype($_GET['op'], 'integer');
    
        switch($_GET['op'])
        {
            
            default:
            
                settype($_GET['group_id'], 'integer');
                settype($_GET['type'], 'string');
                
                $_GET['type']=PhangoApp\PhaUtils\Utils::form_text($_GET['type']);
    
                $list=new SimpleList($s);
                
                $list->arr_fields_showed=['hostname', 'ip', 'date', 'num_updates'];
                
                $list->where_sql=['where 1=1', []];
                
                $list->options_func='server_options';
                
                $list->num_by_page=100;
                
                $list->field_search='hostname';
                
                $list->order=1;
                
                $list->yes_search=1;
                
                $list->url_options=AdminUtils::set_admin_link('leviathan/servers');
                
                if($_GET['group_id']>0)
                {
                
                    $list->where_sql=['where IdServer IN (select server_id from servergroupitem where group_id=?)', [$_GET['group_id']]];
                    
                }
                
                $tasks_select=[];
                
                $yes_form=0;
                
                switch($_GET['type'])
                {
            
                    case 'down':
                        
                        $actual_timestamp=DateTime::obtain_timestamp(Datetime::now(false));
                    
                        $past_timestamp=$actual_timestamp-300;
                        
                        $actual_time=DateTime::format_timestamp($actual_timestamp);
                    
                        $past_time=DateTime::format_timestamp($past_timestamp);
                        
                        $list->where_sql[0].=' AND date<?';
                        $list->where_sql[1][]=$past_time;
                        
                    break;
                    
                    case 'heavy':
                        
                        $list->where_sql[0].=' AND actual_idle>?';
                        $list->where_sql[1][]=70;
                    
                    break;
                    
                    case 'disks':
                    
                        $list->where_sql[0].=' AND ip IN (select ip from statusdisk where percent>90)';
                    
                    break;
                    
                    case 'update_servers':
                    
                        $list->where_sql[0].=' AND num_updates>0';
                        
                        $list->arr_extra_fields=[I18n::lang('phangoapp/leviathan', 'choose_server', 'Choose server')];
                
                        $list->arr_extra_fields_func=['server_update_options'];
                        
                        $yes_form=1;
                    
                    break;
                    
                    case 'task_servers':
                        
                        $list->arr_extra_fields=[I18n::lang('phangoapp/leviathan', 'choose_server', 'Choose server')];
                
                        $list->arr_extra_fields_func=['server_update_options'];
                        
                        $yes_form=2;
                        
                        $tasks_select=search_tasks('vendor/phangoapp/leviathan/tasks');
                        
                        /*echo '<pre>';
                        
                        print_r($tasks_select);
                        
                        echo '</pre>';*/
                    
                    break;
                
                }

                $groups=new PhangoApp\PhaModels\Forms\SelectModelForm('group_id',   $_GET['group_id'],   $g,   'name',   'IdServergroup') ;

                echo View::load_view([$groups, 'list' => $list,  $_GET['op'], $_GET['group_id'], $yes_form, $_GET['type'], $tasks_select], 'leviathan/servers', 'phangoapp/leviathan');
            
            break;
            
            case 1:
                
                form_add($s, $g, $os);
            
            break;
            
        }
    }
    else
    if(PhangoApp\PhaRouter\Routes::$request_method=='POST')
    {
        
        $post=form_add($s, $g, $os, $_POST);
        
        //Insert task, go to guzzle
        //PhangoApp\PhaLibs\AdminUtils::$show_admin_view=false;
        
        $t->create_forms();
        
        if($post)
        {
            //, 'data' => $post
            if($t->insert(['name_task' => 'Add server', 'description_task' => 'Task for add a server to leviathan network', 'codename_task' => 'add_server', 'hostname' => $post['hostname'], 'path' => 'tasks/system/add_server', 'server' => $post['ip'], 'user' => 'root', 'password' => $_POST['password'], 'user_path' => '/root', 'os_codename' => $post['os_codename'], 'data' => ['ip' => $post['ip'], 'disable_root_password' => $post['disable_root_password'], 'clean_gcc' => $post['clean_gcc']]]))
            {
                $id=$t->insert_id();
                
                if($s->insert($post))
                {
                    
                    $server_id=$s->insert_id();
                    
                    $i->create_forms();
                    
                    $i->insert(['server_id' => $server_id, 'group_id' => $post['group_id']]);
                    
                    //Guzzle
                    PhangoApp\PhaLibs\AdminUtils::$show_admin_view=false;
                    
                    try {
                    
                        $client=new GuzzleHttp\Client();
                        
                        $response=$client->request('GET', ConfigTask::$url_server, [
                            'query' => ['task_id' => $id, 'api_key' => ConfigTask::$api_key]
                        ]);
                        
                        die(header('Location: '.AdminUtils::set_admin_link('leviathan/showprogress', ['task_id' => $id, 'server' => $post['ip']])));
                    
                    } catch (Exception $e) {
                        
                        PhangoApp\PhaLibs\AdminUtils::$show_admin_view=true;
                        
                        echo 'Cannot connect to task server: '.$e->getMessage();
                        
                        if(!$s->where(['where IdServer=?', [$server_id]])->delete())
                        {
                            echo '<p>Error: cannot delete the entrance for the new server failed in database</p>';
                        }
                        
                    }
                    
                
                }
                
                
            }
            else
            {
                
                echo "Cannot insert the task. Check your database";
                
            }
            
        }
        
    }
}

function form_add($s, $g, $os, $post=[])
{
    
    $s->create_forms(['hostname', 'ip', 'os_codename', 'password']);
    
    $s->forms['os_codename']=new PhangoApp\PhaModels\Forms\SelectModelForm('os_codename',   '',   $os,   'name',   'codename') ;
    $s->forms['os_codename']->required=true;
    $s->forms['os_codename']->label=I18n::lang('phangoapp/leviathan', 'os_codename', 'Operating system');
    
    $s->forms['password']=new PhangoApp\PhaModels\Forms\PasswordForm('password', '');
    $s->forms['password']->required=true;

    $s->forms['disable_root_password']=new PhangoApp\PhaModels\Forms\SelectForm('disable_root_password', '', [0 => I18n::lang('common', 'no', 'No'), 1 => I18n::lang('common', 'yes', 'Yes')]);
    $s->forms['disable_root_password']->default_value=1;
    
    $s->forms['disable_root_password']->label=I18n::lang('phangoapp/leviathan', 'disable_root_password', 'Disable root password');
    
    $s->forms['clean_gcc']=new PhangoApp\PhaModels\Forms\SelectForm('clean_gcc', '', [0 => I18n::lang('common', 'no', 'No'), 1 => I18n::lang('common', 'yes', 'Yes')]);
    $s->forms['clean_gcc']->default_value=1;
    
    $s->forms['clean_gcc']->label=I18n::lang('phangoapp/leviathan', 'clean_gcc', 'Clean build dependencies? By default clean compilers and others tools that normally you don\'t you need in your server');
    
    $s->forms['password']->label=I18n::lang('common', 'password', 'Password');
    
    if(!isset($post['group_id']))
    {
     
        $post['group_id']=$_GET['group_id']; 
        
    }
    
    $s->forms['group_id']=new PhangoApp\PhaModels\Forms\SelectModelForm('group_id',   $post['group_id'],   $g,   'name',   $g->idmodel) ;
    
    $s->forms['group_id']->required=true;
    
    $s->forms['group_id']->label=I18n::lang('phangoapp/leviathan', 'principal_group', 'Principal group of server');
    
    if(count($post)>1)
    {
        
        list($s->forms, $check_post)=ModelForm::check_form($s->forms, $post);
        
        if(isset($check_post['ip']))
        {
            
            if($s->where(['where ip=?', [$check_post['ip']]])->select_count()>0)
            {
                
                $check_post=false;
                $s->forms['ip']->error=1;
                $s->forms['ip']->std_error='Error: exists a server with this ip';
                
            }
            
        }
        
        if(!$check_post)
        {
            
            $form=PhangoApp\PhaModels\ModelForm::show_form($s->forms, $post, true);
        
            echo View::load_view(['form' => $form], 'leviathan/add_server', 'phangoapp/leviathan');
            
            return false;
            
        }
        else
        {
            
            return $check_post;
            
        }
        
    }
    else
    {

        $form=PhangoApp\PhaModels\ModelForm::show_form($s->forms, $post);
        
        echo View::load_view(['form' => $form], 'leviathan/add_server', 'phangoapp/leviathan');
    
    }
}

function server_update_options($arr_row)
{
    
    return '<input type="checkbox" name="server[]" value="'.$arr_row['IdServer'].'pe"/>';
    
}

function server_options($url_options, $model_name, $id, $arr_row)
{
    
    $arr_options=[];
    
    $arr_options[]='<a href="'.AdminUtils::set_admin_link('leviathan/graphs', ['server_id' => $id]).'">'.I18n::lang('phangoapp/leviathan', 'graphs', 'Graphs').'</a>';
    $arr_options[]='<a href="'.AdminUtils::set_admin_link('leviathan/tasks', ['server_id' => $id]).'">'.I18n::lang('phangoapp/leviathan', 'tasks', 'Tasks').'</a>';
    $arr_options[]='<a href="'.AdminUtils::set_admin_link('leviathan/servers', ['server_id' => $id, 'op' => 2]).'">'.I18n::lang('phangoapp/leviathan', 'delete_server', 'Delete server').'</a>';
    
    return $arr_options;
    
}

function search_tasks($dir)
{
    
    $search_dir=scandir($dir);
                        
    foreach($search_dir as $d)
    {
        
        if($d!='.' && $d!='..' && $d!='system')
        {
            
            
            $path_new=$dir.'/'.$d;
            
            if($d=='info.json')
            {
                $info_json=json_decode(file_get_contents($path_new), true);
                $arr_dir[$dir]['name']=$info_json['name'];
                
                if(isset($info_json['path']))
                {
                    
                    $arr_dir[$dir]['path']=$info_json['path'];
                    
                }
            }
            
            if(is_dir($path_new))
            {
                
                $arr_dir[$dir]['dir'][]=search_tasks($path_new);
                
            }
            
            /*
            if(is_dir($path_new))
            {
                
                
                $arr_dir[$dir][]['dir']=search_tasks($path_new);
                
            }
            
            if($d=='info.json')
            {
                $info_json=json_decode(file_get_contents($path_new), true);
                $arr_dir[$dir]['name']=$info_json['name'];
                
                
                if(isset($info_json['path']))
                {
                    
                    $arr_dir[$dir]['path']=$info_json['path'];
                    
                }
            }*/
            
            /*$path_new=$dir.'/'.$d;
            
            if(is_dir($path_new))
            {
                
                $arr_dir[$path_new]=search_tasks($path_new, $arr_dir);
                
            }
            
            if($d=='info.json')
            {
                
                $info_json=json_decode(file_get_contents($path_new), true);
                
                $arr_dir[$dir]['name']=$info_json['name'];
                
                if(isset($info_json['path']))
                {
                    
                    $arr_dir[$dir]['path']=$info_json['path'];
                    
                }
                
            }*/
            
            //$arr_dir[$dir][]=
            
        }
        
    }
    
    return $arr_dir;
    
}

?>
