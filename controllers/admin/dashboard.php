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

function DashBoardAdmin()
{
    
    settype($_GET['op'], 'integer');
    
    switch($_GET['op'])
    {
        
        default:
        
            echo View::load_view([], 'leviathan/dashboard', 'phangoapp/leviathan');
        
        break;
        
        case 1:
        
            $arr_data=[];
        
            $now=DateTime::obtain_timestamp(DateTime::now(false));
                    
            $hours12=$now-7200;
            
            $five_minutes=$now-300;
                
            $date_now=DateTime::format_timestamp($now);
                
            $date_hours12=DateTime::format_timestamp($hours12);
            
            $date_five_minutes=DateTime::format_timestamp($five_minutes);
        
            $s=new Server();
            
            $arr_id_server=$s->select_to_list(['IdServer'], true);
            
            $c_servers=$s->select_count();
            
            $arr_data['num_servers']=$c_servers;
            
            $arr_data['num_servers_down']=$s->where('WHERE date<'.$date_five_minutes)->select_count();
            
            $c_servers-=$arr_data['num_servers_down'];
            
            $statuscpu=new StatusCpu();
        
            PhangoApp\PhaLibs\AdminUtils::$show_admin_view=false;
            
            /*$query=$s->query('select SUM(num_cpu), SUM(idle) from statuscpu where last_updated=1');
            
            list($arr_data['num_cpu'], $sum_idle)=$s->fetch_row($query);
            */
            
            $sum_idle=0;
            
            $arr_data['num_cpu']=0;
            
            $arr_cpu=['0-30' => 0, '30-70' => 0, '70-100'=> 0];
            
            $query=$statuscpu->where('WHERE last_updated=1')->select(['num_cpu', 'idle']);
            
            while(list($num_cpu, $idle)=$s->fetch_row($query))
            {
                
                $sum_idle+=$idle;
                
                $arr_data['num_cpu']+=$num_cpu;
                
                if($idle>70)
                {
                    $arr_cpu['70-100']++;
                }
                elseif($idle>30)
                {
                    $arr_cpu['30-70']++;
                }
                else
                {
                    $arr_cpu['0-30']++;
                }
                
                
            }
            
            
            $arr_data['average_idle']=0;
            
            if($c_servers>0)
            {
            
                $arr_data['average_idle']=round($sum_idle)/$c_servers;
                
            }
            
            $arr_data['cpu_info']=$arr_cpu;
            
            $statusnet=new StatusNet();
            
            //$arr_net=$statusnet->where('WHERE last_updated=1')->select_to_array(['bytes_sent', 'bytes_recv']);
            
            $query=$statusnet->query('select SUM(bytes_sent), SUM(bytes_recv) from statusnet where last_updated=1');
            
            list($arr_data['total_bytes_sent'], $arr_data['total_bytes_recv'])=$statusnet->fetch_row($query);
            
            /*
            with status_cpu.select(['idle']) as cur:
                
                for cpu in cur:
                    if cpu['idle']>70:
                        arr_cpu['70-100']+=1
                    elif cpu['idle']>30:
                        arr_cpu['30-70']+=1
                    else:
                        arr_cpu['0-30']+=1
            */
            
            echo json_encode($arr_data);
        
        break;
        
    }
    
}

?>
