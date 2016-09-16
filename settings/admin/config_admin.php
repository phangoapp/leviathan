<?php

ModuleAdmin::$arr_modules_admin[]=[ 'leviathan', [
array('leviathan/servers', 'vendor/phangoapp/leviathan/controllers/admin/servers', PhangoApp\PhaI18n\I18n::lang('phangoapp/leviathan', 'servers', 'Servers') ), 
array('leviathan/groups', 'vendor/phangoapp/leviathan/controllers/admin/groups', PhangoApp\PhaI18n\I18n::lang('phangoapp/leviathan', 'groups_servers', 'Servers groups') ), 
array('leviathan/os', 'vendor/phangoapp/leviathan/controllers/admin/os', PhangoApp\PhaI18n\I18n::lang('phangoapp/leviathan', 'os_servers', 'Os Servers') ), 
array('leviathan/showprogress', 'vendor/phangoapp/leviathan/controllers/admin/showprogress', PhangoApp\PhaI18n\I18n::lang('phangoapp/leviathan', 'show_progress', 'Show progress of task'), '' ), 
array('leviathan/graphs', 'vendor/phangoapp/leviathan/controllers/admin/graphs', PhangoApp\PhaI18n\I18n::lang('phangoapp/leviathan', 'graphs', 'Graphs'), '' ), 
array('leviathan/tasks', 'vendor/phangoapp/leviathan/controllers/admin/tasks', PhangoApp\PhaI18n\I18n::lang('phangoapp/leviathan', 'tasks_log', 'Tasks log'), '' ), 
array('leviathan/updates', 'vendor/phangoapp/leviathan/controllers/admin/updates', PhangoApp\PhaI18n\I18n::lang('phangoapp/leviathan', 'update_servers', 'Update servers'), '' ), 
array('leviathan/showmultiprogress', 'vendor/phangoapp/leviathan/controllers/admin/showmultiprogress', PhangoApp\PhaI18n\I18n::lang('phangoapp/leviathan', 'show_multiprogress', 'Progress in servers'), '' ),
 array('leviathan/maketask', 'vendor/phangoapp/leviathan/controllers/admin/maketask', PhangoApp\PhaI18n\I18n::lang('phangoapp/leviathan', 'maketask', 'Make task in servers'), '' )], PhangoApp\PhaI18n\I18n::lang('phangoapp/leviathan', 'leviathan', 'Leviathan')
 ];

?>
