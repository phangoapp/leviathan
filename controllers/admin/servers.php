<?php

use PhangoApp\PhaLibs\SimpleList;
use PhangoApp\PhaModels\Webmodel;
use PhangoApp\PhaView\View;
use PhangoApp\PhaI18n\I18n;
use PhangoApp\PhaModels\ModelForm;
use PhangoApp\Leviathan\ConfigTask;

Webmodel::load_model('vendor/phangoapp/leviathan/models/servers');
Webmodel::load_model('vendor/phangoapp/leviathan/models/tasks');

function ServersAdmin()
{
    
    settype($_GET['group_id'], 'integer');
    
    $s=new Server();
    $g=new ServerGroup();
    $t=new Task();
    $os=new OsServer();
    
    if(PhangoApp\PhaRouter\Routes::$request_method=='GET')
    {
        
        settype($_GET['group_id'], 'integer');
        
        settype($_GET['op'], 'integer');
    
        switch($_GET['op'])
        {
            
            default:
    
                $list=new SimpleList($s);
                
                $list->arr_fields_showed=['hostname', 'date'];

                echo View::load_view(['list' => $list], 'leviathan/servers', 'phangoapp/leviathan');
            
            break;
            
            case 1:
            
                /*
                $s->create_forms(['hostname', 'ip', 'os_codename', 'password']);
            
                $s->forms['password']=new PhangoApp\PhaModels\Forms\PasswordForm('password', '');
                $s->forms['password']->required=true;
            
                $s->forms['disable_root_password']=new PhangoApp\PhaModels\Forms\SelectForm('disable_yes_password', '', [0 => I18n::lang('common', 'no', 'No'), 1 => I18n::lang('common', 'yes', 'Yes')]);
                $s->forms['disable_root_password']->default_value=1;
                
                $s->forms['password']->label=I18n::lang('common', 'password', 'Password');
    
                $s->forms['disable_root_password']->label=I18n::lang('phangoapp/leviathan', 'disable_root_password', 'Disable root password');
                
                $s->forms['group_id']=new PhangoApp\PhaModels\Forms\SelectModelForm('group_id',   $_GET['group_id'],   $g,   'name',   $g->idmodel) ;
                
                $s->forms['group_id']->required=true;
                
                $s->forms['group_id']->label=I18n::lang('phangoapp/leviathan', 'principal_group', 'Principal group of server');
            
                $form=PhangoApp\PhaModels\ModelForm::show_form($s->forms, []);
                
                echo View::load_view(['form' => $form], 'leviathan/add_server', 'phangoapp/leviathan');*/
                
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
            if($t->insert(['name_task' => 'Add server', 'description_task' => 'Task for add a server to leviathan network', 'codename_task' => 'add_server', 'path' => 'tasks/system/add_server', 'server' => $post['ip'], 'user' => 'root', 'password' => $_POST['password']]))
            {
                
                //Guzzle
                
                //$client=new GuzzleHttp\Client(['base_uri' => $url_server]);
                
                
                
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
    
    $s->forms['password']=new PhangoApp\PhaModels\Forms\PasswordForm('password', '');
    $s->forms['password']->required=true;

    $s->forms['disable_root_password']=new PhangoApp\PhaModels\Forms\SelectForm('disable_root_password', '', [0 => I18n::lang('common', 'no', 'No'), 1 => I18n::lang('common', 'yes', 'Yes')]);
    $s->forms['disable_root_password']->default_value=1;
    
    $s->forms['password']->label=I18n::lang('common', 'password', 'Password');

    $s->forms['disable_root_password']->label=I18n::lang('phangoapp/leviathan', 'disable_root_password', 'Disable root password');
    
    $s->forms['group_id']=new PhangoApp\PhaModels\Forms\SelectModelForm('group_id',   $_GET['group_id'],   $g,   'name',   $g->idmodel) ;
    
    $s->forms['group_id']->required=true;
    
    $s->forms['group_id']->label=I18n::lang('phangoapp/leviathan', 'principal_group', 'Principal group of server');

    if(count($post)>0)
    {
        
        list($s->forms, $check_post)=ModelForm::check_form($s->forms, $post);
        
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

?>
