<?php

use PhangoApp\PhaLibs\SimpleList;
use PhangoApp\PhaLibs\GenerateAdminClass;
use PhangoApp\PhaModels\Webmodel;
use PhangoApp\PhaView\View;
use PhangoApp\PhaI18n\I18n;
use PhangoApp\PhaLibs\AdminUtils;
use PhangoApp\PhaLibs\ParentLinks;

Webmodel::load_model('vendor/phangoapp/leviathan/models/servers');

function OsAdmin()
{
    
    $os=new OsServer();
    
    //$os->create_forms();
    
    $g=new GenerateAdminClass($os, AdminUtils::set_admin_link('leviathan/os'));
    
    $g->list->arr_fields_showed=['name', 'codename'];
    
    $g->show();
    
}

?>
