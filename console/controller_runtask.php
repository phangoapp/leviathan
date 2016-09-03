<?php

use PhangoApp\PhaModels\Webmodel;
use PhangoApp\PhaRouter\Routes;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Phastafari\ServerTask\ConfigTask;
use Phastafari\ServerTask\Task;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

gc_enable();
gc_collect_cycles();

Webmodel::load_model('vendor/phangoapp/leviathan/models/tasks');

$process=[];
$pipes=[];

function RunTaskConsole($host='127.0.0.1', $port=1337, $debug=0)
{

    global $process, $pipes;

    settype($port, 'integer');

    $loop = React\EventLoop\Factory::create(); 
    /*$log = new Logger('name');
    $log->pushHandler(new StreamHandler('path/to/your.log', Logger::WARNING));*/

    $m=Webmodel::$m;

    $app = function ($request, $response) use($loop, $m) {
        
        global $process, $pipes;
        
        $get=$request->getQuery();
        
        settype($get['task_id'], 'integer');
        settype($get['api_key'], 'string');
        
        $uuid4 = Uuid::uuid4();
        
        $z=$uuid4->toString();
        
        if(ConfigTask::$api_key===$get['api_key'])
        {
         
            $descriptorspec = array(
               0 => array("pipe", "r"),  // stdin es una tubería usada por el hijo para lectura
               1 => array("pipe", "w"),  // stdout es una tubería usada por el hijo para escritura
               2 => array("pipe", "w") // stderr es un fichero para escritura
            );
            
            $pipes[$z]=[];

            $process[$z] = proc_open('php console.php -m=phangoapp/leviathan -c=task --task_id='.$get['task_id'], $descriptorspec, $pipes[$z], Routes::$base_path);
            
            if(is_resource($process[$z]))
            {
            
                $arr_answer=['progress' => 0, 'error' => 0, 'message' => 'Begin tasks'];
                
                $response->writeHead(200, array('Content-Type' => 'text/plain'));
                $response->end(json_encode($arr_answer));
                
            }
            else
            {
                
                $arr_answer=['progress' => 100, 'error' => 1, 'message' => 'Error: task executable doesnt work'];
            
                $response->writeHead(200, array('Content-Type' => 'text/plain'));
                $response->end(json_encode($arr_answer));
                
            }
            
        }
        else
        {
            
            $arr_answer=['progress' => 100, 'error' => 1, 'message' => 'Error: wrong request'];
            
            $response->writeHead(200, array('Content-Type' => 'text/plain'));
            $response->end(json_encode($arr_answer));
            
        } 
                    
            
        unset($m);
    };
 
    $loop->addPeriodicTimer(5, function ()  {
    
        global $process, $pipes;
        
        //echo count($process)."\n";
        
        $arr_k=array_keys($process);
        
        foreach($arr_k as $k)
        {
            
            $status=proc_get_status($process[$k]);
            
            if(!$status['running'])
            {
                
                fclose($pipes[$k][0]);
                fclose($pipes[$k][1]);
                fclose($pipes[$k][2]);
                
                $return_value = proc_close($process[$k]);
                
                unset($process[$k]);
                unset($pipes[$k]);
                
            }
            
            
        }
        
        $memory = memory_get_usage() / 1024;
        $formatted = number_format($memory, 3).'K';
        echo "Current memory usage: {$formatted}\n";
            
    });
        
    
    
    $socket = new React\Socket\Server($loop);
    $http = new React\Http\Server($socket, $loop);

    $http->on('request', $app);
    echo "Server running at http://127.0.0.1:".$port."\n";

    $socket->listen($port);
    $loop->run();


}

?>
