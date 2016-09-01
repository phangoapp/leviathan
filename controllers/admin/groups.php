<?php

use PhangoApp\PhaLibs\SimpleList;
use PhangoApp\PhaLibs\GenerateAdminClass;
use PhangoApp\PhaModels\Webmodel;
use PhangoApp\PhaView\View;
use PhangoApp\PhaI18n\I18n;
use PhangoApp\PhaLibs\AdminUtils;
use PhangoApp\PhaLibs\ParentLinks;

Webmodel::load_model('vendor/phangoapp/leviathan/models/servers');

function GroupsAdmin()
{
    
    settype($_GET['parent_id'], 'integer');
    
    $g=new ServerGroup();
    
    $g->create_forms();
    
    $g->forms['parent_id']->default_value=$_GET['parent_id'];
    
    $l=new ParentLinks(AdminUtils::set_admin_link('leviathan/groups', ['parent_id' => $_GET['parent_id']]), 'servergroup',   'parent_id',   'name',   $_GET['parent_id'],   false,   $arr_parameters = array(),   $arr_pretty_parameters = array());
    
    echo '<p>'.$l->show().'</p>';
    
    $a=new GenerateAdminClass($g, AdminUtils::set_admin_link('leviathan/groups', ['parent_id' => $_GET['parent_id']]));
    
    $a->list->arr_fields_showed=['name'];
    
    $a->list->options_func='more_options';
        
    $a->list->where_sql=['where parent_id=?', [$_GET['parent_id']]];
    
    $a->show();
    
}

function more_options($url_options, $model_name, $id, $arr_row)
{
    
    $arr_options=SimpleList::BasicOptionsListModel( $url_options,   $model_name,   $id);
    
    $arr_options[]='<a href="'.AdminUtils::set_admin_link('leviathan/groups', ['parent_id' => $id]).'">'.I18n::lang('phangoapp/leviathan', 'subgroups', 'Subgroups').'</a>';
    
    return $arr_options;
    
}

?>
