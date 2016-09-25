<?php

use PhangoApp\PhaView\View;
use PhangoApp\PhaI18n\I18n;
use PhangoApp\PhaLibs\AdminUtils;

function DashBoardView()
{

    View::$js_module['leviathan'][]='Chart.min.js';
    View::$js_module['leviathan'][]='waterbubble.min.js';
    
    View::$css_module['leviathan'][]='leviathan.css';
    
    ob_start();
    //http://localhost/leviathan/index.php/admin/leviathan/servers/get/op/0/group_id/0/type/down
    ?>
    <p><?php echo I18n::lang('phangoapp/leviathan', 'number_of_servers', 'Number of servers'); ?>: <span id="number_servers"></span></p>
    <p><?php echo I18n::lang('phangoapp/leviathan', 'number_of_cpus', 'Number of total cpu cores'); ?>: <span id="number_cpu"></span></p>
    <p id="hosts_down" style="display:none;"><span class="error"><span id="num_hosts_down"></span> <?php echo I18n::lang('phangoapp/leviathan', 'hosts_down', 'HOSTS DOWN'); ?></span> <a target="_blank" href="<?php echo AdminUtils::set_admin_link('leviathan/servers', ['type' => 'down']); ?>"><?php echo I18n::lang('phangoapp/leviathan', 'view_down_servers', 'View down servers'); ?></a></p>
    <p id="hosts_heavy_loaded" style="display:none;"><span class="error"><span id="num_hosts_heavy_loaded"></span> <?php echo I18n::lang('phangoapp/leviathan', 'hosts_loaded', 'HOSTS HEAVILY LOADED'); ?></span> <a target="_blank" href="<?php echo AdminUtils::set_admin_link('leviathan/servers', ['type' => 'heavy']); ?>"><?php echo I18n::lang('phangoapp/leviathan', 'view_heavy_servers', 'View heavily loaded servers'); ?></a></p>
    <?php
    
    $content=ob_get_contents();
    
    ob_end_clean();

    ?>
    <div class="left_dashboard">
    <?php

    echo View::load_view(['Servers', $content], 'admin/content');

    ?>
    </div>
    <div class="right_dashboard">
        <div class="menu_title title">
            <?php echo I18n::lang('phangoapp/leviathan', 'total_cpu_use_average', 'Total cpu use average'); ?>
        </div>
        <div class="cont_text" style="text-align:center;">
            <canvas id="cpu_average"></canvas>
        </div>
    </div>
    <div class="left_dashboard">
        <div class="menu_title title">
            <?php echo I18n::lang('phangoapp/leviathan', 'servers_load', 'Servers load'); ?>
        </div>
        <div class="cont_text">
            <canvas id="info_cpu"></canvas>
        </div>
    </div>
    <div class="right_dashboard">
        <div class="menu_title title">
            <?php echo I18n::lang('phangoapp/leviathan', 'total_net_use', 'Total network use'); ?>
        </div>
        <div class="cont_text">
            <canvas id="canvas_net"></canvas>
        </div>
    </div>
    <script>
        
        //Chart for info cpu
        
        var ctx_cpu = document.getElementById("info_cpu").getContext("2d");
    
        var data_cpu = {
            labels: [
                "0% - 30%",
                "30% - 70%",
                "70% - 100%"
            ],
            datasets: [
                {
                    data: [0, 0, 0],
                    backgroundColor: [
                        "#FF6384",
                        "#8f1100",
                        "#FF0000"
                    ]
                }]
        };
        
        options_cpu={
            title: {
                display: true,
                text: 'Global load of servers'
            },
            responsive: true,
            legend: {
                    position: 'bottom',
            }
        };
        
        window.cpu_pie = new Chart(ctx_cpu,{ type: 'pie', data: data_cpu, options: options_cpu });
        
        //Now the total net transferred net
        
        var ctx_net = document.getElementById("canvas_net").getContext("2d");
        
        var config_net = {
                type: 'bar',
                data: {
                    labels: ["In", "Out"],
                    datasets: [{
                        label: 'In',
                        borderColor: "#0000ff",
                        backgroundColor: "#0000ff",
                        data: [0]
                    },
                    {
                        label: 'Out',
                        borderColor: "#00ff00",
                        backgroundColor: "#00ff00",
                        data: [0]
                    }]

                },
                options: {
                    elements: {
                        rectangle: {
                            borderWidth: 0,
                            borderColor: 'rgb(255, 0, 0)',
                            borderSkipped: 'bottom'
                        }
                    },
                    hover: {
                        mode: 'label'
                    },
                    responsive: true,
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: 'Network use in GigaBytes since last boot'
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    },
                    scales: {
                            yAxes: [{
                            display: true,
                            scaleLabel: {
                                display: true,
                                labelString: 'Gigabytes'
                            },
                            ticks: {
                                
                            }
                        }]
                    }
                    
                }
            };
       
        window.bar_net = new Chart(ctx_net, config_net);
        
        function get_info() {
        
            $.ajax({
            url: "<?php echo AdminUtils::set_admin_link('leviathan/dashboard', ['op' => 1]); ?>",
            method: "GET",
            dataType: "json",
            data: {}
            }).done(function(data) {
                
                if(data.num_servers_down>0)
                {
                    
                    $('#hosts_down').show();
                    $('#num_hosts_down').html(data.num_servers_down);
                    
                }
                else
                {
                    
                    $('#hosts_down').hide();
                    
                }
                
                $('#number_servers').html(data['num_servers']);
                $('#number_cpu').html(data['num_cpu']);
                
                $('#cpu_average').waterbubble({
                    radius: 100,
                    lineWidth: 5,
                    data: data.average_idle/100,
                    waterColor: 'rgba(25, 139, 201, 1)',
                    textColor: 'rgba(06, 85, 128, 0.8)',
                    txt: data.average_idle+' %',
                    font: 'bold 60px "Helvetica"',
                    wave: true,
                    animation: false
                });
                
                window.bar_net.destroy();
                
                config_net.data.datasets[0].data[0]=((((data.total_bytes_recv)/1024)/1024)/1024).toPrecision(2);
                config_net.data.datasets[1].data[1]=((((data.total_bytes_sent)/1024)/1024)/1024).toPrecision(2);
                
                window.bar_net = new Chart(ctx_net, config_net);
                
                window.cpu_pie.destroy();
                
                cpu_pie.data.datasets[0].data[0]=data.cpu_info['0-30'];
                cpu_pie.data.datasets[0].data[1]=data.cpu_info['30-70'];
                cpu_pie.data.datasets[0].data[2]=data.cpu_info['70-100'];
                
                if(data.cpu_info['70-100']>0)
                {
                    $('#hosts_heavy_loaded').show();
                    $('#num_hosts_heavy_loaded').html(data.cpu_info['70-100']);
                    
                    
                }
                else
                {
                    
                    $('#hosts_heavy_loaded').hide();
                    
                }
                
                window.cpu_pie = new Chart(ctx_cpu,{ type: 'pie', data: data_cpu, options: options_cpu });
                
                
            }).fail(function (data) {
                
                    alert(JSON.stringify(data));
                
            });
        
        }
        
        get_info();
        
        setInterval(get_info, 60000);
        
    </script>
    <?php

}

?>
