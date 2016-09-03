<?php

namespace PhangoApp\Leviathan;
use PhangoApp\PhaModels\Webmodel;

Webmodel::load_model('vendor/phangoapp/leviathan/models/tasks');
Webmodel::load_model('vendor/phangoapp/leviathan/models/servers');

//The task are subclasses of Task class


class Task {
    
    public $task;
    
    public $server;

    public function __construct($server='')
    {
         
        $this->server=$server;
        
        //Description of the task
        
        $this->description='';

        $this->txt_error='';
        
        //If files, then upload, you can make tasks that simply prepare an scripts environment, other for tasks, and other for delete the script environment.
        //The files are located in the same path, into the $USER home
        //The files have to be deleted by the script or others
        
        $this->files=[];
        
        //Format first array element is command with the interpreter, the task is agnostic, the files in os directory. The commands are setted with 750 permission.
        //First element is the file, next elements are the arguments
        
        $this->commands_to_execute=[];
        
        //THe files to delete
        
        $this->delete_files=[];
        
        $this->delete_directories=[];
        
        //The id of the task in db
        
        $this->id=0;
        
        $this->user=ConfigTask::$ssh_user;
        
        $this->password='';
        
        $this->task=new \Task();
        
        $this->logtask=new \LogTask();
        
        $this->grouptask=new \ServerGroupTask();
        
        $this->name_task='';
        
        $this->description_task='';
        
        $this->codename_task='';
        
        $this->os_server='';
        
        $this->index_ssh_key=0;
        
        $this->enable_pty=false;
        
        $this->pre_task='';
        
        $this->post_task='';
        
        $this->error_task='';
        
        $this->data=[];
        
        $this->one_time=0;
        
        $this->version=__DIR__.'/version';
        
        $this->tmp_dir='/tmp';

    }
    
    public function prepare_connection()
    {
        
        if($this->password==='')
        {
            
            $key = new \phpseclib\Crypt\RSA();

            try {
                
                if(file_exists(ConfigTask::$ssh_key_priv[$this->index_ssh_key]))
                {
                    
                    $key->setPassword(ConfigTask::$ssh_key_password[$this->index_ssh_key]);

                    if(!($file_key=file_get_contents(ConfigTask::$ssh_key_priv[$this->index_ssh_key])))
                    {
                        
                        $this->txt_error='Error: wrong ssh key';
                        return false;

                    }


                    if(!$key->loadKey($file_key))
                    {

                        $this->txt_error='Error: cannot load ssh key';
                        return false;

                    }

                }
                else
                {
                    
                    $this->txt_error='Error: no exists ssh key file';
                    return false;
                    
                }
            

            }
            catch(Exception $e) {
                
                $this->txt_error='Error: cannot load ssh key:'.$e->getMessage();
                return false;
                
            }

        }
        else
        {
            
            $key=$this->password;
            
        }
        
        //Login in the server
        
        $sftp = new \phpseclib\Net\SFTP($this->server);
        
        try {
        
            if (!$sftp->login($this->user, $key)) 
            {

                $this->txt_error='Error: cannot login in the server. Check that server is up';
            
                return false;

            }
        
        }
        catch(\Exception $e) {
            
            $sftp->disconnect();
            
            $this->txt_error='Error: cannot to the the server. Check that server is up';
            
            return false;
            
        }
        
        
        return $sftp;
        
    }
    
    public function upload_files($sftp)
    {
        
        if(count($this->files)>0)
        {
            
            //Upload the files
            
            foreach($this->files as $arr_file)
            {
                $file=str_replace('${os_server}', $this->os_server, $arr_file[0]);
                $permissions=$arr_file[1];
                
                $upload=ConfigTask::$ssh_path.'/'.$file;
                
                $upload_dir=dirname($upload);
                
                if(!$sftp->is_dir($upload_dir))
                {
                    
                    $sftp->mkdir($upload_dir, -1, true);
                    
                }
                
                
                $return_trans=$sftp->put($upload, $file, \phpseclib\Net\SFTP::SOURCE_LOCAL_FILE);

                if(!$return_trans)
                {       
                        $this->txt_error='Error: cannot upload files to the server: '.$file;
                        return false;
                }
                
                $return_perm= $sftp->chmod($permissions, $upload, $recursive = false);
                
                if(!$return_perm)
                {
                        $this->txt_error='Error: cannot change permissions in server';
                        return false;
                }
                
            }
            
        }
        
        return true;
        
    }
    
    public function clean_files($sftp)
    {
        
        
        //Upload the files
        
        foreach($this->delete_files as $file)
        {
            
            $file_remote=ConfigTask::$ssh_path.'/'.$file;
            
            if(!$sftp->delete($file_remote))
            {
                $this->txt_error='Error: cannot clean the files...';
                return false;
                
            }
            
        }
        
        foreach($this->delete_directories as $dir)
        {
            
            $dir_remote=ConfigTask::$ssh_path.'/'.$dir;
            
            if(!$sftp->delete($dir_remote, true))
            {
                
                $this->txt_error='Error: cannot clean the directories...';
                return false;
                
            }
            
        }
        
        return true;
        
        
    }
    
    public function exec($id=0)
    {
        
        //Insert task
        
        $m=Webmodel::$m;
        
        $arr_task=['name_task' => $this->name_task, 'description_task' => $this->description_task, 'codename_task' => $this->codename_task];
        
        $this->task->fields_to_update=['name_task', 'description_task', 'codename_task'];
        
        $this->id=$id;
        
        if($this->id==0)
        {
        
            if(!$this->task->insert($arr_task))
            {
                
                $this->txt_error='Error: cannot insert the task in the database '.$this->task->std_error;
                
                return false;
                
            }
                
            $this->id=$this->task->insert_id();
    
        }
        
        try {
    
            $ssh=$this->prepare_connection($m);
            
            if($ssh)
            {
                //Check if one time file and if 
                
                if($this->one_time)
                {
                    
                    $task_check=ConfigTask::$ssh_path.'/tasks/'.$this->codename_task;
                    
                    $copy_task=$this->version;
                    
                    $version_task=trim(file_get_contents($this->version));
                    
                    if($ssh->stat($task_check) && $version_task)
                    {
                        
                        $version_installed=trim($ssh->get($task_check));
                        
                        if(version_compare($version_installed, $version_task)==0)
                        {
                            
                            $this->logtask->log(['task_id' => $this->id, 'error' => 0, 'progress' => 100, 'message' =>  'Task was executed sucessfully', 'no_progress' => 0, 'server' => $this->server]);
                            
                            return true;
                            
                        }
                        
                    }
                    
                }
                
                if($this->pre_task!=='')
                {
                    
                    $this->pre_task($this);
                    
                }
                
                if($this->upload_files($ssh))
                {
                    if($this->enable_pty)
                    {
                        $ssh->enablePTY();
                    }
                    //Ten minutes of timeout 
                    $ssh->setTimeout(600);
                    //Exec task
                    
                    $error=0;
                    
                    foreach($this->commands_to_execute as $key => $exec_command)
                    {
                        
                        //$this->logtask->log(['task_id' => $this->id, 'error' => 0, 'progress' => 0, 'message' =>  'Begin script...', 'no_progress' => 0, 'server' => $this->server]);
                        
                        $sudo='';
                        
                        if($exec_command[0]=='sudo')
                        {
                            
                            $sudo='sudo ';
                            
                        }
                        
                        unset($exec_command[0]);
                        
                        $this->command=$exec_command;
                        
                        $command=$sudo.ConfigTask::$ssh_path.'/'.trim(implode(' ', $exec_command));
                    
                        $ssh->exec($command, $this);
                        
                        if($ssh->isTimeout())
                        {
                            
                            $this->logtask->log(['task_id' => $this->id, 'error' => 1, 'progress' => 100, 'message' =>  'Error: the task show timeout...', 'no_progress' => 0, 'server' => $this->server]);
                            
                            $error=1;
                            #break;
                            return false;
                            
                        }
                        
                        if($ssh->getExitStatus()!=0)
                        {
                            
                            $this->logtask->log(['task_id' => $this->id, 'error' => 1, 'progress' => 100, 'message' =>  'Error: the task show error. Please, check the database log', 'no_progress' => 0, 'server' => $this->server]);
                            
                            /*$error=1;
                            break;*/
                            return false;
                            
                        }
                        
                    }
                    
                    //Delete files
                    
                    if(!$this->clean_files($ssh))
                    {
                        $ssh->disconnect();
                        
                        /*$this->task->reset_require();
                    
                        $this->task->set_conditions(['where IdTask=?', [$this->id]]);
                        
                        $this->task->update(['error' => 1, 'status' => 1]);*/
                        
                        $this->logtask->log(['task_id' => $this->id, 'error' => 1, 'progress' => 100, 'message' =>  $this->txt_error,'server' => $this->server]);
                        
                        return false;
                        
                    }
                    
                    
                    //Disconnect from server
                    
                    //Check task how done
                    
                    /*$this->task->reset_require();
                    
                    $this->task->set_conditions(['where IdTask=?', [$this->id]]);
                    
                    $this->task->update(['status' => 1]);*/
                    
                    if($error==0)
                    {
                        //Upload version if set
                        
                        if($this->one_time)
                        {
                            $task_check_dir=ConfigTask::$ssh_path.'/tasks/';
                            
                            if(!$ssh->is_dir($task_check_dir))
                            {
                                
                                $ssh->mkdir($task_check_dir, -1, true);
                                
                            }
                            
                            $task_check=ConfigTask::$ssh_path.'/tasks/'.$this->codename_task;
                            
                            $copy_task=$this->version;
                            
                            if(file_exists($copy_task))
                            {
                                
                                $return_trans=$ssh->put($task_check, $copy_task, \phpseclib\Net\SFTP::SOURCE_LOCAL_FILE);

                                if(!$return_trans)
                                {       
                                        $this->logtask->log(['task_id' => $this->id, 'error' => 0, 'status'=> 0, 'progress' => 100, 'message' =>  'Sorry, cannot upload: '.$copy_task,'server' => $this->server]);
                                }
                                
                            }
                            
                        }
                        
                        
                        
                        $ssh->disconnect();
                    
                        if($this->post_task!=='')
                        {
                            
                            $this->post_task($this);
                            
                        }
                        
                        $this->grouptask->create_forms();
                        
                        $this->grouptask->insert(['ip' => $this->server, 'name_task' => $this->codename_task]);
                        
                        //$this->task->update(['status' => 1]);
                        
                        $this->logtask->log(['task_id' => $this->id, 'error' => 0, 'status'=> 1, 'progress' => 100, 'message' =>  'Task done!!','server' => $this->server]);
                    
                        return true;

                    }
                    else
                    {
                        
                        $ssh->disconnect();
                        
                        //$this->task->update(['status' => 1, 'error' => 1]);
                        
                        $this->logtask->log(['task_id' => $this->id, 'error' => 1, 'progress' => 100, 'message' =>  'Task show error: '.$this->txt_error, 'server' => $this->server]);
                        
                        return false;
                        
                    }
                

                }
                else
                {
                    
                    $ssh->disconnect();
                    
                    if($this->error_task!=='')
                    {
                        
                        $this->error_task($this);
                        
                    }
                    
                    /*$this->task->reset_require();
                    
                    $this->task->set_conditions(['where IdTask=?', [$this->id]]);*/
                    
                    $this->logtask->log(['task_id' => $this->id, 'error' => 1, 'progress' => 100, 'message' =>  $this->txt_error, 'server' => $this->server]);
                    
                    //$this->task->update(['error' => 1, 'status' => 1]);
                    
                    return false;
                    
                }
                
            }
            else
            {
                /*
                $this->task->reset_require();
                    
                $this->task->set_conditions(['where IdTask=?', [$this->id]]);
                
                $this->task->update(['error' => 1, 'status' => 1]);*/
                
                $this->logtask->log(['task_id' => $this->id, 'error' => 1, 'progress' => 100, 'message' =>  $this->txt_error, 'server' => $this->server]);
                
                return false;
                
            }
            
        }
        catch(Exception $e) {
            
            $this->txt_error=$e->getMessage();
        
            return false;
            
        }
        
        //If

        //Execute the task
        
        $this->grouptask->create_forms();
        $this->grouptask->insert(['name_task' => $this->codename_task, 'ip' => $this->server]);
        
        return true;
        
    }
    
    public function __invoke($msg)
    {
        
        $arr_msg=json_decode($msg, true);
        
        if($arr_msg)
        {
        
            $arr_msg['task_id']=$this->id;
            
            $arr_msg['server']=$this->server;
            
            $this->logtask->log($arr_msg);
        
        }
        else
        {
            
            if(trim($msg)!='')
            {
                
                $this->logtask->log(['task_id' => $this->id, 'error' => 0, 'progress' => 0, 'message' =>  $msg, 'no_progress' => 1, 'server' => $this->server]);
            }
            /*$this->txt_error='Error executing the script. See the database log for more details.';
            
            return false;*/
            
        }
        
    }

}
