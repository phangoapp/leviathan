<?php

use PhangoApp\PhaLibs\AdminUtils;

function makeTaskFormView($task_id, $form)
{

    ?>
    <form method="post" action="<?php AdminUtils::set_admin_link('leviathan/maketask', ['op' => 1, 'task_id' => $task_id]); ?>">
    <?php echo $form; ?>
    </form>
    <?php

}

?>
