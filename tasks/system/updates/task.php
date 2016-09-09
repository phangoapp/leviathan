<?php

use PhangoApp\Leviathan\Task;

class ServerTask extends Task {
    
    public function define()
    {
        $this->commands_to_execute=[['bin/upgrade.sh', '']];
                
        $this->name_task='Update servers';
        
        $this->description_task='Upgrade server using native package manager';
        
        $this->codename_task='update_server';
        
        $this->one_time=0;
        
        $this->version=__DIR__.'/version';
        
    }
    
}

?>
