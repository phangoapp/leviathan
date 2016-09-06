<?php

use PhangoApp\PhaLibs\AdminUtils;
use PhangoApp\PhaUtils\MenuSelected;
use PhangoApp\PhaI18n\I18n;

function ServersView($groups, $list, $op, $group_id, $yes_form, $type)
{
    
    ?>
    <p>
    <?php
    echo $groups->form();
    ?>
    <script>
        $('#group_id_field_form').change( function () {
           
            location.href='<?php echo AdminUtils::set_admin_link('leviathan/servers'); ?>/get/op/<?php echo $op; ?>/type/<?php echo $type; ?>/group_id/'+$('#group_id_field_form').val();
            
        });
    </script>
    </p>
    <p><a href="<?php echo AdminUtils::set_admin_link('leviathan/servers', ['op' => 1, 'group_id' => $group_id]); ?>"><?php echo I18n::lang('phangoapp/leviathan', 'leviathan', 'Add new server' ); ?></a></p>
    <?php
    
    $arr_op['']=['link' => AdminUtils::set_admin_link('leviathan/servers', ['op' => $op, 'group_id' => $group_id, 'type' => '']), 'text' =>  I18n::lang('phangoapp/leviathan', 'all_servers', 'All servers' )];
    
    $arr_op['down']=['link' => AdminUtils::set_admin_link('leviathan/servers', ['op' => $op, 'group_id' => $group_id, 'type' => 'down']), 'text' =>  I18n::lang('phangoapp/leviathan', 'servers_down', 'Servers down' )];
    
    $arr_op['heavy']=['link' => AdminUtils::set_admin_link('leviathan/servers', ['op' => $op, 'group_id' => $group_id, 'type' => 'heavy']), 'text' =>  I18n::lang('phangoapp/leviathan', 'heavy_loaded', 'Servers heavily loaded' )];
    
    $arr_op['disks']=['link' => AdminUtils::set_admin_link('leviathan/servers', ['op' => $op, 'group_id' => $group_id, 'type' => 'disks']), 'text' =>  I18n::lang('phangoapp/leviathan', 'full_disks', 'Full disks' )];
    
    $arr_op['update_servers']=['link' => AdminUtils::set_admin_link('leviathan/servers', ['op' => $op, 'group_id' => $group_id, 'type' => 'update_servers']), 'text' =>  I18n::lang('phangoapp/leviathan', 'update_servers', 'Update servers' )];
    
    $arr_op['task_servers']=['link' => AdminUtils::set_admin_link('leviathan/servers', ['op' => $op, 'group_id' => $group_id, 'type' => 'task_servers']), 'text' =>  I18n::lang('phangoapp/leviathan', 'task_servers', 'Make task in servers' )];
    
    MenuSelected::menu_selected($type, $arr_op, 1);

    $close_form='';

    switch($yes_form)
    {
        
        case 1:
        
            ?>
            <form method="post" action="<?php echo AdminUtils::set_admin_link('leviathan/updates'); ?>">
            <?php
        
            $close_form='<p><input type="button" value="'.I18n::lang('phangoapp/leviathan', 'all_servers', 'Make task in all servers').'" /> <input type="submit" value="'.I18n::lang('phangoapp/leviathan', 'update_servers', 'Update all servers').'" /></p></form>';
        
        break;
        
        case 2:
        
            ?>
            <form method="post" action="<?php echo AdminUtils::set_admin_link('leviathan/maketask'); ?>">
            <?php
        
            $close_form='<p><input type="button" value="'.I18n::lang('phangoapp/leviathan', 'all_servers', 'Make task in all servers').'" /> <input type="submit" value="'.I18n::lang('phangoapp/leviathan', 'update_servers', 'Update all servers').'" /></p></form>';
        
        break;
        
    }

    $list->show();
    
    echo $close_form;

}

?>
