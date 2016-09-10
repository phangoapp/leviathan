<?php

use PhangoApp\Leviathan\Task;

class ServerTask extends Task {
    
    public function define()
    {
        $this->files=[['vendor/leviathan/scripts/servers/databases/mariadb/${os_server}/install_mariadb.py', 0700]];
        
        $this->commands_to_execute=[['vendor/leviathan/scripts/servers/mail/postfix/${os_server}/install_maridb.py', '']];
        
        #THe files to delete
        
        $this->delete_files=[];
        
        $this->delete_directories=['vendor/leviathan/scripts/servers/databases/mariadb'];
                
        $this->name_task='Install MariaDB standalone server';
        
        $this->description_task='Install standalone Mariadb server';
        
        $this->codename_task='install_mariadb_server';
        
        $this->one_time=1;
        
        $this->version=__DIR__.'/version';
        
        $this->yes_form=true;
        
    }
    
    //public function form()
    
}

?>
