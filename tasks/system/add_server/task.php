<?php

use PhangoApp\Leviathan\Task;
use PhangoApp\Leviathan\ConfigTask;

class ServerTask extends Task {
    
    public function define($data)
    {
        
        $this->data=$data;
        
        /*
        $this->files=[['vendor/phangoapp/leviathan/tests/script/alive.sh', 0755]];
        
        $this->commands_to_execute=[['/bin/bash', 'vendor/phangoapp/leviathan/tests/script/alive.sh', ''], ['sudo', 'vendor/phangoapp/leviathan/tests/script/alive.sh', '']];
        
        $this->delete_files=['vendor/phangoapp/leviathan/tests/script/alive.sh'];
        
        $this->delete_directories=['vendor/phangoapp/leviathan/tests'];*/
        
        $this->files=[['vendor/phangoapp/leviathan/scripts/standard/'.$this->os_server.'/install_python.sh', 0755]];
        $this->files[]=['vendor/phangoapp/leviathan/scripts/standard/'.$this->os_server.'/install_curl.sh', 0755];
        $this->files[]=['vendor/phangoapp/leviathan/scripts/standard/'.$this->os_server.'/install_psutil.sh', 0755];
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
        
        /*files.append(['modules/pastafari/scripts/standard/'+os_server+'/install_python.sh', 0o750])
        files.append(['modules/pastafari/scripts/standard/'+os_server+'/install_curl.sh', 0o750])
        files.append(['modules/pastafari/scripts/standard/'+os_server+'/install_psutil.sh', 0o750])
        files.append(['modules/pastafari/scripts/standard/'+os_server+'/upgrade.sh', 0o750])
        files.append(['modules/pastafari/scripts/monit/'+os_server+'/alive.py', 0o750])
        #files.append(['monit/'+os_server+'/files/alive.sh', 0o750];
        files.append(['modules/pastafari/scripts/monit/'+os_server+'/files/get_info.py', 0o750])
        files.append(['modules/pastafari/scripts/monit/'+os_server+'/files/get_updates.py', 0o750])
        files.append(['modules/pastafari/scripts/monit/'+os_server+'/files/crontab/alive', 0o640])
        files.append(['modules/pastafari/scripts/monit/'+os_server+'/files/sudoers.d/spanel', 0o640])
        files.append([config_task.public_key, 0o600])

        commands_to_execute=[]
        
        commands_to_execute.append(['modules/pastafari/scripts/standard/'+os_server+'/install_curl.sh', ''])
        commands_to_execute.append(['modules/pastafari/scripts/standard/'+os_server+'/install_python.sh', ''])
        commands_to_execute.append(['modules/pastafari/scripts/standard/'+os_server+'/install_psutil.sh', ''])
        commands_to_execute.append(['modules/pastafari/scripts/monit/'+os_server+'/alive.py', '--url='+config_task.url_monit+'/'+ip+'/'+config_task.api_key+' --user='+config_task.remote_user+' --pub_key='+config_task.public_key])
        
        delete_files=[]
        
        delete_files.append('modules/pastafari/scripts/standard/'+os_server+'/install_python.sh')
        delete_files.append('modules/pastafari/scripts/standard/'+os_server+'/install_curl.sh')
        delete_files.append('modules/pastafari/scripts/standard/'+os_server+'/install_psutil.sh')
        delete_files.append(config_task.public_key)
        
        delete_directories=['modules/pastafari']*/
        
        $this->name_task='Add server';
        
        $this->description_task='Add server to leviathan network';
        
        $this->codename_task='add_server';
        
        $this->one_time=1;
        
        $this->version=__DIR__.'/version';
        
    }
    
}

?>
