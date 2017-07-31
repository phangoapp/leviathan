<?php

use PhangoApp\PhaLibs\AdminUtils;
use PhangoApp\PhaUtils\MenuSelected;
use PhangoApp\PhaI18n\I18n;
use PhangoApp\PhaView\View;
use PhangoApp\PhaUtils\Utils;

function ServersView($groups, $list, $op, $group_id, $yes_form, $type, $tasks_select)
{
    
    ob_start();
    
    ?>
    <style>
       .secundary_task { display:none; }
    </style>
    <?php
    
    View::$header[]=ob_get_contents();
    
    ob_end_clean();
    
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
    <p><a href="<?php echo AdminUtils::set_admin_link('leviathan/servers', ['op' => 1, 'group_id' => $group_id]); ?>"><?php echo I18n::lang('phangoapp/leviathan', 'add_new_server', 'Add new server' ); ?></a></p>
    <?php
    
    $arr_op['']=['link' => AdminUtils::set_admin_link('leviathan/servers', ['op' => $op, 'group_id' => $group_id, 'type' => '']), 'text' =>  I18n::lang('phangoapp/leviathan', 'all_servers', 'All servers' )];
    
    $arr_op['down']=['link' => AdminUtils::set_admin_link('leviathan/servers', ['op' => $op, 'group_id' => $group_id, 'type' => 'down']), 'text' =>  I18n::lang('phangoapp/leviathan', 'servers_view_down', 'Servers down' )];
    
    $arr_op['heavy']=['link' => AdminUtils::set_admin_link('leviathan/servers', ['op' => $op, 'group_id' => $group_id, 'type' => 'heavy']), 'text' =>  I18n::lang('phangoapp/leviathan', 'heavy_loaded', 'Servers heavily loaded' )];
    
    $arr_op['disks']=['link' => AdminUtils::set_admin_link('leviathan/servers', ['op' => $op, 'group_id' => $group_id, 'type' => 'disks']), 'text' =>  I18n::lang('phangoapp/leviathan', 'full_disks', 'Full disks' )];
    
    $arr_op['update_servers']=['link' => AdminUtils::set_admin_link('leviathan/servers', ['op' => $op, 'group_id' => $group_id, 'type' => 'update_servers']), 'text' =>  I18n::lang('phangoapp/leviathan', 'update_servers', 'Update servers' )];
    
    $arr_op['task_servers']=['link' => AdminUtils::set_admin_link('leviathan/servers', ['op' => $op, 'group_id' => $group_id, 'type' => 'task_servers']), 'text' =>  I18n::lang('phangoapp/leviathan', 'task_servers', 'Make task in servers' )];
    
    echo MenuSelected::menu_selected($type, $arr_op, 1);

    $close_form='';

    switch($yes_form)
    {
        
        case 1:
        
            ?>
            <form method="post" id="updates_form" action="<?php echo AdminUtils::set_admin_link('leviathan/updates'); ?>">
            <input type="hidden" name="group_id" value="<?php echo $group_id; ?>" />
            <input type="hidden" id="all_servers_choose" name="all_servers" value="0" />
            <input type="hidden" name="csrf_token" value="<?php echo Utils::generate_csrf_key($length_token=80); ?>" />
            <?php
        
            $close_form='<p><input type="button" id="all_servers_update" value="'.I18n::lang('phangoapp/leviathan', 'all_servers', 'Update all servers').'" /> <input type="submit" value="'.I18n::lang('phangoapp/leviathan', 'update_servers', 'Update selected servers').'" /></p></form>';
        
        break;
        
        case 2:
        
            ?>
            <form method="post" id="tasks_form" action="<?php echo AdminUtils::set_admin_link('leviathan/maketask'); ?>">
            <input type="hidden" name="group_id" value="<?php echo $group_id; ?>" />
            <input type="hidden" id="all_servers_choose" name="all_servers" value="0" />
            <input type="hidden" name="csrf_token" value="<?php echo Utils::generate_csrf_key($length_token=80); ?>" />
            <?php
            
            //Here generate select task form
            
            generate_task_select($tasks_select);
            
            ?>
            <script>
                $('.show_children').click( function () {
                    
                    //alert($(this).parent().children('ul').attr('class'));
                    $(this).parent().children('ul').show();
                    
                    return false;
                    
                });
            </script>
            <?php
        
            $close_form='<p><input type="button" id="all_servers_task" value="'.I18n::lang('phangoapp/leviathan', 'all_servers', 'Make task in all servers').'" /> <input type="submit" value="'.I18n::lang('phangoapp/leviathan', 'make_task_servers', 'Make task in selected servers').'" /></p></form>';
        
        break;
        
    }

    $list->show();
    
    echo $close_form;
    
    ?>
    <script>
        $('#all_servers_update').click(function () {
           
           $('#all_servers_choose').val(1);
           
           $('#updates_form').submit();
            
        });
        
        $('#all_servers_task').click(function () {
           
           $('#all_servers_choose').val(1);
           
           $('#tasks_form').submit();
            
        });
    </script>
    <?php

}

function generate_task_select($tasks_select, $class_element='first_task')
{
 
    foreach($tasks_select as $task_select)
    {
        
        echo '<ul class="'.$class_element.'">';
        if(isset($task_select['dir']))
        {
            echo '<li><a href="#" class="show_children">'.$task_select['name'].'</a>';
            
            foreach($task_select['dir'] as $d)
            {
            
                generate_task_select($d, 'secundary_task');
                
            }
            
        }
        else
        {
            
            echo '<li>'.$task_select['name'];
            
            if(isset($task_select['path']))
            {
            
                echo '<input type="radio" name="task" value="'.base64_encode($task_select['path']).'" />';
                
            }
            
        }
        echo '</li></ul>';
        
    }
    
}

?>
