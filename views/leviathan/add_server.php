<?php

use PhangoApp\PhaLibs\AdminUtils;
use PhangoApp\PhaI18n\I18n;
use PhangoApp\PhaView\View;

function Add_ServerView($form)
{

    ?>
    <h2><?php echo I18n::lang('phangoapp/leviathan', 'add_server', 'Add server'); ?></h2>
    <p><a href="<?php echo AdminUtils::set_admin_link('leviathan/servers'); ?>"><?php echo I18n::lang('phangoapp/leviathan', 'servers', 'Servers'); ?></a> &gt; <?php echo I18n::lang('phangoapp/leviathan', 'add_server', 'Add server'); ?></p>
    <?php
    
    echo View::load_view([I18n::lang('phangoapp/leviathan', 'info', 'Info'), 'Remember, you need access to server with the root password. Leviathan will create all requirements for add the server to the network. '], 'admin/content');

    ?>
    <form method="post" action="<?php echo AdminUtils::set_admin_link('leviathan/servers'); ?>">
    <?php
    echo $form;
    ?>
    <p><input type="submit" value="<?php echo I18n::lang('phangoapp/leviathan', 'add_server', 'Add server'); ?>" /></p>
    </form>
    <?php

}

?>
