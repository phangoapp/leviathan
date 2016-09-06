<?php

use PhangoApp\PhaRouter\Controller;
use PhangoApp\Leviathan\ConfigTask;
use PhangoApp\PhaModels\Webmodel;
use PhangoApp\PhaTime\DateTime;

class MonitController extends Controller {

	public function home()
	{
        
        Webmodel::load_model('vendor/phangoapp/leviathan/models/servers');
        Webmodel::load_model('vendor/phangoapp/leviathan/models/tasks');
        
        if(ConfigTask::$api_key===$_GET['api_key'])
        {
            
            settype($_GET['ip'], 'string');
            settype($_POST['data_json'], 'string');
            
            $now=DateTime::now();
            
            $server=new Server();
            
            $data_server= new DataServer();
            
            $ipcheck=new PhangoApp\PhaModels\CoreFields\IpField('', '');
            
            $ip=$ipcheck->check($_GET['ip']);
            
            $c=$server->where(['WHERE ip=?', [$ip]])->select_count();
            
            if($ipcheck->error==false && $c>0)
            {
                $status_disk=new StatusDisk();
            
                $status_net=new StatusNet();
            
                $status_cpu=new StatusCpu();           
            
                $status_mem=new StatusMemory();
                
                $arr_server=$server->where(['where ip=?', [$ip]])->select_a_row_where(['IdServer']);
                
                if($arr_server) 
                {
                    
                    $arr_update=['status' => 1, 'monitoring' => 1, 'date' => DateTime::now()];
                    
                    $arr_info=json_decode($_POST['data_json'], true);
                    
                    if(!$arr_info)
                    {
                        
                        echo 'Ouch!';
                        exit(1);
                        
                    }
                    
                    $server_id=$arr_server['IdServer'];
                
                    $net_id=false;
                        
                    $memory_id=false;
                        
                    $cpu_id=false;
                        
                    $arr_disk_id=[];
                    
                }
                
                if(isset($arr_info['net_info']))
                {
                    
                    $net_info=$arr_info['net_info'];
                    
                    if(gettype($net_info)=='array')
                    {
                        
                        $post=['bytes_sent'=> $net_info[0], 'bytes_recv'=> $net_info[1], 'errin'=> $net_info[2], 'errout'=> $net_info[3], 'dropin'=> $net_info[4], 'dropout'=> $net_info[5], 'date'=> $now, 'ip'=> $ip, 'last_updated'=> 1, 'server_id' => $arr_server['IdServer']];
                        
                        $status_net->reset_require();
                                
                        $status_net->create_forms();
                        
                        $status_net->set_order(['IdStatusnet' => 1]);
                        
                        $status_net->set_limit([1]);
                        
                        $status_net->set_conditions(['WHERE ip=?', [$ip]]);
                        
                        $status_net->update(['last_updated'=> 0]);
                        
                        $status_net->insert($post);
                        
                        $net_id=$status_net->insert_id();
                    }
                    
                }
                
                if(isset($arr_info['mem_info'])) 
                {
                    
                    $mem_info=$arr_info['mem_info'];
                    
                    if(gettype($mem_info)=='array')
                    {
                        
                        #svmem(total=518418432, available=413130752, percent=20.3, used=208052224, free=310366208, active=137457664, inactive=40919040, buffers=20692992, cached=82071552, shared=4820992)
                        
                        $post=['total'=> $mem_info[0], 'available'=> $mem_info[1], 'percent'=> $mem_info[2], 'used'=> $mem_info[3], 'free'=> $mem_info[4], 'active'=> $mem_info[5], 'inactive'=> $mem_info[6], 'buffers'=> $mem_info[7], 'cached'=> $mem_info[8], 'shared'=> $mem_info[9], 'date'=> $now, 'ip'=> $ip, 'last_updated'=> 1, 'server_id' => $arr_server['IdServer']];
                        
                        $status_mem->reset_require();
                                
                        $status_mem->create_forms();
                        
                        $status_mem->set_order(['IdStatusmem' => 1]);
                        
                        $status_mem->set_limit([1]);
                        
                        $status_mem->set_conditions(['WHERE ip=?', [$ip]]);
                        
                        $status_mem->update(['last_updated'=> 0]);
                        
                        $status_mem->insert($post);
                        
                        $memory_id=$status_mem->insert_id();
                        
                    }
                }
                
                if(isset($arr_info['cpu_idle'])) 
                {
                    
                    $status_cpu->reset_require();
                                
                    $status_cpu->create_forms();
                    
                    $status_cpu->set_order(['IdStatuscpu'=> 0]);
                    
                    $status_cpu->set_limit([1]);
                    
                    $status_cpu->set_conditions(['WHERE ip=?', [$ip]]);
                    
                    $status_cpu->update(['last_updated' => 0]);
                            
                    $status_cpu->insert(['ip'=> $ip, 'idle'=> $arr_info['cpu_idle'], 'date'=> $now, 'last_updated'=> 1, 'num_cpu'=> $arr_info['cpu_number'], 'server_id' => $arr_server['IdServer']]);
                            
                    $arr_update['actual_idle']=$arr_info['cpu_idle'];
                    
                    $cpu_id=$status_cpu->insert_id();
                    
                }
                
                # Need optimitation
                
                if(isset($arr_info['disks_info'])) 
                {
                    
                    $status_disk->create_forms();
                            
                    $status_disk->set_conditions(['WHERE ip=?', [$ip]]);
                    
                    $method_update='insert';
                    
                    if($status_disk->select_count()>0)
                    {
                        
                        $method_update='update';
                        
                    }
                    
                    foreach($arr_info['disks_info'] as $disk=> $data)
                    {
                        
                        $status_disk->set_conditions(['where ip=? and disk=?', [$ip, $disk]]);
                        
                        $status_disk->$method_update(['ip' => $ip, 'disk' => $disk, 'date' => $now, 'size' => $data[0], 'used' => $data[1], 'free' => $data[2], 'percent' => $data[3], 'server_id' => $arr_server['IdServer']]);
                        
                    }
                     
                    $status_disk->set_conditions(['where ip=? and disk=?', [$ip, $disk]]);
                    
                    $arr_disk_id=$status_disk->select_to_array(['id'], true);
                        
                }
                
                #Save status
            
                $server->reset_require();
                
                $server->create_forms();
                
                $server->update($arr_update);
                
                # Save middle table for all statuses of a server
                
                $data_server->create_forms();
                
                $post=['server_id'=> $server_id, 'net_id'=> $net_id, 'memory_id'=> $memory_id, 'cpu_id'=> $cpu_id, 'ip'=> $ip, 'date'=> $now , 'server_id' => $arr_server['IdServer']];
                
                $z=0;
                
                foreach($arr_disk_id as $disk_id)
                {
                    
                    $post['disk'.$z.'_id']=$disk_id['IdStatusdisk'];
                    
                    $z++;
                    
                }
                
                $e=$z-1;
                    
                for($x=$z;$x<6;$x++)
                {
                    
                    $post['disk'.$x.'_id']=$post['disk'.$e.'_id'];
                    
                }
                
                $data_server->reset_conditions=false;
                
                $method_data_update='insert';
                
                $data_server->set_conditions(['where server_id=?', [$server_id]]);
                
                if($data_server->select_count()>0)
                {
                    
                    $method_data_update='update';
                    
                }
                
                $data_server->$method_data_update($post);
                
                echo 'Ok';

            }
        }
                
    }
            
}

?>
