<?php

use PhangoApp\Leviathan\Task;
use PhangoApp\Leviathan\ConfigTask;
use PhangoApp\PhaModels\Webmodel;

class ServerTask extends Task {
    
    public function define()
    {
        
        /*
        $this->files=[['vendor/phangoapp/leviathan/tests/script/alive.sh', 0755]];
        
        $this->commands_to_execute=[['/bin/bash', 'vendor/phangoapp/leviathan/tests/script/alive.sh', ''], ['sudo', 'vendor/phangoapp/leviathan/tests/script/alive.sh', '']];
        
        $this->delete_files=['vendor/phangoapp/leviathan/tests/script/alive.sh'];
        
        $this->delete_directories=['vendor/phangoapp/leviathan/tests'];*/
        
        $this->files=[['vendor/phangoapp/leviathan/scripts/standard/'.$this->os_server.'/install_python.sh', 0755]];
        $this->files[]=['vendor/phangoapp/leviathan/scripts/standard/'.$this->os_server.'/install_curl.sh', 0755];
        $this->files[]=['vendor/phangoapp/leviathan/scripts/standard/'.$this->os_server.'/install_psutil.sh', 0755];
        $this->files[]=['vendor/phangoapp/leviathan/scripts/standard/'.$this->os_server.'/delete_root_passwd.sh', 0755];
        $this->files[]=['vendor/phangoapp/leviathan/scripts/standard/'.$this->os_server.'/clean_gcc.sh', 0755];
        $this->files[]=['vendor/phangoapp/leviathan/scripts/standard/'.$this->os_server.'/upgrade.sh', 0755];
        $this->files[]=['vendor/phangoapp/leviathan/scripts/monit/'.$this->os_server.'/alive.py', 0755];
        $this->files[]=['vendor/phangoapp/leviathan/scripts/monit/'.$this->os_server.'/files/get_info.py', 0755];
        $this->files[]=['vendor/phangoapp/leviathan/scripts/monit/'.$this->os_server.'/files/get_updates.py', 0755];
        $this->files[]=['vendor/phangoapp/leviathan/scripts/monit/'.$this->os_server.'/files/crontab/alive', 0640];
        $this->files[]=['vendor/phangoapp/leviathan/scripts/monit/'.$this->os_server.'/files/sudoers.d/spanel', 0640];
        $this->files[]=[ConfigTask::$ssh_key_pub[0], 0600];
        
        $this->commands_to_execute=[];
        $this->commands_to_execute[]=['vendor/phangoapp/leviathan/scripts/standard/'.$this->os_server.'/install_curl.sh', ''];
        $this->commands_to_execute[]=['vendor/phangoapp/leviathan/scripts/standard/'.$this->os_server.'/install_python.sh', ''];
        $this->commands_to_execute[]=['vendor/phangoapp/leviathan/scripts/standard/'.$this->os_server.'/install_psutil.sh', ''];
        $this->commands_to_execute[]=['vendor/phangoapp/leviathan/scripts/monit/'.$this->os_server.'/alive.py', '--url='.ConfigTask::$url_monit.'/get/ip/'.$this->data['ip'].'/api_key/'.ConfigTask::$api_key.' --user='.ConfigTask::$ssh_user.' --pub_key='.ConfigTask::$ssh_key_pub[0]];
        
        if($this->data['disable_root_password'])
        {
            
            $this->commands_to_execute[]=['vendor/phangoapp/leviathan/scripts/standard/'.$this->os_server.'/delete_root_passwd.sh', ''];
            
        }
        
        if($this->data['clean_gcc'])
        {
            
            $this->commands_to_execute[]=['vendor/phangoapp/leviathan/scripts/standard/'.$this->os_server.'/clean_gcc.sh', ''];
            
        }
        
        $this->name_task='Add server';
        
        $this->description_task='Add server to leviathan network';
        
        $this->codename_task='add_server';
        
        $this->one_time=1;
        
        $this->version=__DIR__.'/version';
        
    }
    
    public function error_task()
    {
        
        Webmodel::load_model('vendor/phangoapp/leviathan/models/servers');
        
        $server=new Server();
        
        $server->set_conditions(['where ip=?', [$this->data['ip']]]);
        
        $server->delete();
        
    }
    
}

?>
