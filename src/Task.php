<?php

namespace Phastafari\ServerTask;
use PhangoApp\PhaModels\Webmodel;

Webmodel::load_model('vendor/phastafari/servertask/models/servertask');

//The task are subclasses of Task class


class Task {
    
    public $task;
    
    public $server;

    public function __construct($server)
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

    }
    
    public function prepare_connection()
    {
        
        if($this->password==='')
        {
            
            $key = new \phpseclib\Crypt\RSA();

            try {

                $key->setPassword(ConfigTask::$ssh_key_password);

                if(!($file_key=file_get_contents(ConfigTask::$ssh_key_priv)))
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

                $this->txt_error='Error: cannot login in the server';
            
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
                $file=$arr_file[0];
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
        
        $arr_task=['name_task' => 'live', 'server' => $this->server];
        
        $m->task->fields_to_update=['name_task', 'server', 'status', 'error'];
        
        $this->id=$id;
        
        if($this->id==0)
        {
        
            if(!$m->task->insert($arr_task))
            {
                
                $this->txt_error='Error: cannot insert the task in the database';
                
                return false;
                
            }
                
            $this->id=$m->task->insert_id();
    
        }
        
        try {
    
            $ssh=$this->prepare_connection($m);
            
            if($ssh)
            {
                
                if($this->upload_files($ssh))
                {
                    //$ssh->enablePTY();
                    //Five minutes of timeout 
                    $ssh->setTimeout(300);
                    //Exec task
                    
                    $error=0;
                    
                    foreach($this->commands_to_execute as $key => $exec_command)
                    {
                        
                        $this->command=$exec_command;
                        
                        $command=ConfigTask::$ssh_path.'/'.trim(implode(' ', $exec_command));
                    
                        $ssh->exec($command, $this);
                        
                        if($ssh->isTimeout())
                        {
                            
                            \LogTask::log(['task_id' => $this->id, 'error' => 1, 'progress' => 100, 'msg' => 'Error: the task show timeout...', 'no_progress' => 0, 'server' => $this->server]);
                            
                            $error=1;
                            break;
                            
                        }
                        
                        if($ssh->getExitStatus()!=0)
                        {
                            
                            \LogTask::log(['task_id' => $this->id, 'error' => 1, 'progress' => 100, 'msg' => 'Error: the task show error...', 'no_progress' => 0, 'server' => $this->server]);
                            
                            $error=1;
                            break;
                            
                        }
                        
                    }
                    
                    //Delete files
                    
                    if(!$this->clean_files($ssh))
                    {
                        $ssh->disconnect();
                        
                        $m->task->reset_require();
                    
                        $m->task->set_conditions(['where IdTask=?', [$this->id]]);
                        
                        $m->task->update(['error' => 1, 'status' => 1]);
                        
                        \LogTask::log(['task_id' => $this->id, 'error' => 1, 'progress' => 100, 'msg' => $this->txt_error,'server' => $this->server]);
                        
                        return false;
                        
                    }
                    
                    //Disconnect from server
                    
                    $ssh->disconnect();
                    
                    //Check task how done
                    
                    $m->task->reset_require();
                    
                    $m->task->set_conditions(['where IdTask=?', [$this->id]]);
                    
                    $m->task->update(['status' => 1]);
                    
                    if($error==0)
                    {
                        
                        //$m->task->update(['status' => 1]);
                        
                        \LogTask::log(['task_id' => $this->id, 'error' => 0, 'status'=> 1, 'progress' => 100, 'msg' => 'Task done!!','server' => $this->server]);
                    
                        return true;

                    }
                    else
                    {
                        
                        //$m->task->update(['status' => 1, 'error' => 1]);
                        
                        \LogTask::log(['task_id' => $this->id, 'error' => 1, 'progress' => 100, 'msg' => 'Task show error: '.$this->txt_error, 'server' => $this->server]);
                        
                        return false;
                        
                    }
                

                }
                else
                {
                    
                    $ssh->disconnect();
                    
                    $m->task->reset_require();
                    
                    $m->task->set_conditions(['where IdTask=?', [$this->id]]);
                    
                    \LogTask::log(['task_id' => $this->id, 'error' => 1, 'progress' => 100, 'msg' => $this->txt_error, 'server' => $this->server]);
                    
                    $m->task->update(['error' => 1, 'status' => 1]);
                    
                    return false;
                    
                }
                
            }
            else
            {
                
                $m->task->reset_require();
                    
                $m->task->set_conditions(['where IdTask=?', [$this->id]]);
                
                $m->task->update(['error' => 1, 'status' => 1]);
                
                \LogTask::log(['task_id' => $this->id, 'error' => 1, 'progress' => 100, 'msg' => $this->txt_error, 'server' => $this->server]);
                
                return false;
                
            }
            
        }
        catch(Exception $e) {
            
            $this->txt_error=$e->getMessage();
        
            return false;
            
        }
        
        
        //If have files to upload, upload then.
        
        //Get key

        //Execute the task
        
        
        
    }
    
    public function __invoke($msg)
    {
        
        $arr_msg=json_decode($msg, true);
        
        if($arr_msg)
        {
        
            $arr_msg['task_id']=$this->id;
            
            $arr_msg['server']=$this->server;
            
            \LogTask::log($arr_msg);
        
        }
        else
        {
            
            if(trim($msg)!='')
            {
                
                \LogTask::log(['task_id' => $this->id, 'error' => 0, 'progress' => 0, 'msg' => $msg, 'no_progress' => 1, 'server' => $this->server]);
            }
            /*$this->txt_error='Error executing the script. See the database log for more details.';
            
            return false;*/
            
        }
        
    }

}
