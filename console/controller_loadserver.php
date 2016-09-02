<?php

use Phastafari\ServerTask\ConfigTask;

gc_enable();
gc_collect_cycles();

function LoadServerConsole()
{
    
    $descriptorspec = array(
       0 => array("pipe", "r")  // stdin is a pipe that the child will read from
       //1 => array("pipe", "w")  // stdout is a pipe that the child will write to
       //2 => array("file", "/tmp/error-output.txt", "a") // stderr is a file to write to
    );

    //$cwd = '/tmp';
    //$env = array('some_option' => 'aeiou');

    $arr_process=[];
    $arr_pipes=[];
    
    foreach(ConfigTask::$servers as $port)
    {
        
        $command=ConfigTask::$php_path.' console.php -m=phastafari/servertask -c=servertask --port='.$port;
        
        $arr_process[$port]=proc_open($command, $descriptorspec, $arr_pipes[$port]);
        
        if(!$arr_process[$port])
        {
            
            echo "Error: cannot load the process. Check permissions";
            
            exit(1);
        }

    }
    
    while(true)
    {
        
        foreach(ConfigTask::$servers as $port)
        {
        
            $text=fread($arr_pipes[$port][0], 4096);
            
            echo $text;
            
            $status=proc_get_status($arr_process[$port]);
            
            if(!$status['running'])
            {
                
                fclose($arr_pipes[$port][0]);
                
                //Close 
                proc_close($arr_process[$port]);
                
                $arr_pipes[$port]=[];
                
                //Restart process..
                
                $arr_process[$port]=proc_open($command, $descriptorspec, $arr_pipes[$port]);
                
                echo "Restarting process died...\n";
        
                if(!$arr_process[$port])
                {
                    
                    echo "Error: cannot load the process. Check permissions\n";
                    
                    exit(1);
                }
                
            }
            
        }
    
        $last_time1=filemtime('./vendor/phastafari/servertask/console/controller_servertask.php');
            
        clearstatcache (true, './vendor/phastafari/servertask/console/controller_servertask.php');
        
        sleep(1);
        
        $last_time2=filemtime('./vendor/phastafari/servertask/console/controller_servertask.php');
        
        if($last_time1<$last_time2)
        {
            
            foreach(ConfigTask::$servers as $port)
            {
                
                //proc 
                
                $status=proc_get_status($arr_process[$port]);
                
                posix_kill($status['pid'], SIGINT);
                
                pcntl_waitpid($status['pid'], $status_killed);
                
                fclose($arr_pipes[$port][0]);
                
                //Close 
                proc_close($arr_process[$port]);
                
                $arr_pipes[$port]=[];
                
                //Restart process..
                
                $arr_process[$port]=proc_open($command, $descriptorspec, $arr_pipes[$port]);
        
                if(!$arr_process[$port])
                {
                    
                    echo "Error: cannot load the process. Check permissions";
                    
                    exit(1);
                }
                
                echo "Changed servertask. Restarting processes...\n";

            
            }

        }
        
        /*$memory = memory_get_usage() / 1024;
        $formatted = number_format($memory, 3).'K';
        echo "Current memory usage frontend: {$formatted}\n";*/

    }
    
}

?>
