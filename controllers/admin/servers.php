<?php

use PhangoApp\PhaLibs\SimpleList;
use PhangoApp\PhaModels\Webmodel;
use PhangoApp\PhaView\View;
use PhangoApp\PhaI18n\I18n;

Webmodel::load_model('vendor/phangoapp/leviathan/models/servers');

function ServersAdmin()
{
    
    settype($_GET['group_id'], 'integer');
    
    $s=new Server();
    $g=new ServerGroup();
    
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
                
                echo View::load_view(['form' => $form], 'leviathan/add_server', 'phangoapp/leviathan');
            
            break;
            
        }
    }
    else
    if(PhangoApp\PhaRouter\Routes::$request_method=='POST')
    {
        
        //Insert task, go to guzzle
        PhangoApp\PhaLibs\AdminUtils::$show_admin_view=false;
        
    }
}

?>
