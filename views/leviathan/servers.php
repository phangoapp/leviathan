<?php

use PhangoApp\PhaLibs\AdminUtils;
use PhangoApp\PhaI18n\I18n;

function ServersView($list)
{

    ?>
    <p><a href="<?php echo AdminUtils::set_admin_link('leviathan/servers/get/op/1'); ?>"><?php echo I18n::lang('phangoapp/leviathan', 'leviathan', 'Add new server' ); ?></a></p>
    <?php

    $list->show();

}

?>
