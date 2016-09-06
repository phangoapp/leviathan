<?php

use PhangoApp\PhaLibs\SimpleList;
use PhangoApp\PhaLibs\GenerateAdminClass;
use PhangoApp\PhaModels\Webmodel;
use PhangoApp\PhaView\View;
use PhangoApp\PhaI18n\I18n;
use PhangoApp\PhaLibs\AdminUtils;
use PhangoApp\PhaLibs\ParentLinks;
use PhangoApp\PhaTime\DateTime;

Webmodel::load_model('vendor/phangoapp/leviathan/models/servers');

function GraphsAdmin()
{
    settype($_GET['op'], 'integer');
    settype($_GET['server_id'], 'integer');
    
    $s=new Server();
    
    $server=$s->select_a_row($_GET['server_id']);
    
    if($server) 
    {
    
        switch($_GET['op'])
        {
        
            default:
        
                echo View::load_view([$server], 'leviathan/graphs', 'phangoapp/leviathan');
                
            break;
            
            case 1:
            
                PhangoApp\PhaLibs\AdminUtils::$show_admin_view=false;
            
                $ip=$server['ip'];
                    
                $now=DateTime::obtain_timestamp(DateTime::now(false));
                    
                $hours12=$now-21600;
                    
                $date_now=DateTime::format_timestamp($now);
                    
                $date_hours12=DateTime::format_timestamp($hours12);
                    
                $status_cpu=new StatusCpu();
                    
                //status_cpu.set_conditions('where ip=%s and date>=%s and date<=%s', [ip, date_hours12, date_now])
                    
                    #arr_cpu=status_cpu.select_to_array(['idle', 'date'])
                //cur=status_cpu.select(['idle', 'date'])
                
                $cur=$status_cpu->where(['where ip=? and date>=? and date<=?', [$ip, $date_hours12, $date_now]])->select(['idle', 'date']);
                    
                $x=0;
                    
                $arr_cpu=[];
                    
                while($cpu_info=$status_cpu->fetch_array($cur))
                {
                        
                    $arr_cpu[]=$cpu_info['idle'];
                        
                }
                
                $arr_mem=[];
                
                $status_mem=new StatusMemory();
                
                $query=$status_mem->where(['where ip=? and date>=? and date<=?', [$ip, $date_hours12, $date_now]])->select(['used', 'free', 'cached', 'date']);
                
                while($mem_info=$status_mem->fetch_array($query))
                {
                    
                    $mem_info['used']=(($mem_info['used']/1024)/1024)/1024;
                    $mem_info['free']=(($mem_info['free']/1024)/1024)/1024;
                    $mem_info['cached']=(($mem_info['cached']/1024)/1024)/1024;
                    $arr_mem[]=$mem_info;
                    
                }
                
                if(count($arr_mem)>2)
                {
                    
                    array_shift($arr_mem);
                    
                }
                                    
                $arr_net=[];
                    
                $status_net=new StatusNet();
                    
                $query=$status_net->where(['where ip=? and date>=? and date<=?', [$ip, $date_hours12, $date_now]])->select(['bytes_sent', 'bytes_recv', 'date']);
                    
                $substract_time=0;
                    
                $c_hours12=$now;
                    
                $c_elements=0;
                    
                $c_count=$status_net->affected_rows($query);
                    
                if($c_count>0)
                {
                    $data_net=$status_net->fetch_array($query);
                        
                    $first_recv=$data_net['bytes_recv'];
                    $first_sent=$data_net['bytes_sent'];
                        
                    if(count($arr_cpu)<($c_count-1))
                    {
                        $arr_cpu[]=$arr_cpu[count($arr_cpu)-1];
                    }
                        
                    
                    #for data_net in cur:
                    while($data_net=$status_net->fetch_array($query))
                    {
                            
                        $timestamp=DateTime::obtain_timestamp($data_net['date'], true);
                            
                        $diff_time=$timestamp-$substract_time;
                            
                        if($substract_time!=0 && $diff_time>300)
                        {
                                
                            $count_time=$timestamp;
                            
                            while($substract_time<=$count_time)
                            {
                    
                                $form_time=DateTime::format_timestamp($substract_time);
                                
                                $arr_net[]=['date'=> DateTime::format_time($form_time)];
                                        
                                $substract_time+=60;
                            }
                        }
                            
                        $bytes_sent=round(($data_net['bytes_sent']-$first_sent)/1024);
                        $bytes_recv=round(($data_net['bytes_recv']-$first_recv)/1024);
                        $cpu=$arr_cpu[$x];
                            
                        $memory_used=$arr_mem[$x]['used'];
                        $memory_free=$arr_mem[$x]['free'];
                        $memory_cached=$arr_mem[$x]['cached'];

                        $arr_net[]=['bytes_sent'=> $bytes_sent, 'bytes_recv'=> $bytes_recv, 'date'=> DateTime::format_time($data_net['date']), 'cpu'=> $cpu, 'memory_used'=> $memory_used, 'memory_free'=> $memory_free, 'memory_cached'=> $memory_cached];
                            
                        $first_sent=$data_net['bytes_sent'];
                        $first_recv=$data_net['bytes_recv'];
                            
                        $c_hours12=$timestamp;
                            
                        $substract_time=$timestamp;
                            
                        $c_elements+=1;
                            
                        $x+=1;
                        
                    }
                            
                    # If the last time is more little that now make a loop 
                        
                    while($c_hours12<=$now)
                    {
                        
                        $form_time=DateTime::format_timestamp($c_hours12);
                        
                        $seconds=substr($form_time, -2);
                            
                        #print(form_time)
                        
                        if($seconds=='00')
                        {
                            
                            $arr_net[]=['date' => DateTime::format_time($form_time)];
                        }
                        
                        $c_hours12++;
                    }
                    
                    if($c_elements>2)
                    {
                        
                        echo json_encode($arr_net);
                        exit(0);
                    }
                    else
                    {
                        
                        echo '[]';
                        exit(0);
                    }
                        
                    echo '[]';
                    exit(0);
                    
                }
            
                echo '[]';
            
            break;
            
            case 2:
            
                PhangoApp\PhaLibs\AdminUtils::$show_admin_view=false;
                
                if($server) 
                {
                    
            
                    if(isset($server['ip']))
                    {
                    
                        $ip=$server['ip'];
                        
                        $status_disk=new StatusDisk();
                        
                        $arr_disk=$status_disk->where(['where ip=?', [$ip]])->select_to_list(['disk', 'used', 'free', 'date']);
                        
                        echo json_encode($arr_disk);
                        
                        exit(0);
                        
                    }
                }
            
                echo '[]';
            
            break;
            
        }
        
    }
    
}

?>
