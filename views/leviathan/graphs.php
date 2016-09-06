<?php

use PhangoApp\PhaView\View;
use PhangoApp\PhaLibs\AdminUtils;
use PhangoApp\PhaI18n\I18n;

function GraphsView($server)
{

View::$js_module['leviathan'][]='Chart.min.js';

?>

<h2><?php echo I18n::lang('phangoapp/leviathan', 'graphs_of', 'Servers Graphs'); ?>: <?php echo $server['hostname']; ?></h2>
<p><a href="<?php echo AdminUtils::set_admin_link('leviathan/servers'); ?>"><?php echo I18n::lang('phangoapp/leviathan', 'servers', 'Servers'); ?></a> &gt;&gt; <?php echo $server['hostname']; ?></p>
<div class="title">
    <?php echo I18n::lang('phangoapp/leviathan', 'graphs', 'Graphs'); ?>
</div>
<div class="cont">
    <div class="first_canvas">
        <canvas id="canvas"></canvas>
    </div>
    <div class="other_canvas">
        <canvas id="canvas_cpu"></canvas>
    </div>
    <div class="other_canvas">
        <canvas id="canvas_mem"></canvas>
    </div>
    <div class="other_canvas">
        <canvas id="canvas_disk"></canvas>
    </div>
</div>
<script>
    
background_out="#32cd32";
background_in="#0033cc";

var config_net = {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: "In",
                data: [],
                fill: false,
                borderColor: background_in,
                backgroundColor: background_in,
                pointBorderColor: background_in,
                pointBackgroundColor: background_in,
                pointRadius: 0,
                lineTension: 0,
                //borderDash: [5, 5],
            }
            ,{
                label: "Out",
                data: [],
                fill: true,
                lineTension: 0,
                //borderDash: [5, 5],
                borderColor: background_out,
                backgroundColor: background_out,
                pointBorderColor: background_out,
                pointBackgroundColor: background_out,
                pointBorderWidth: 1,
                pointRadius: 0
            }]
        },
        options: {
            
            showXLabels: 10,
            animation : false,
            responsive: true,
            legend: {
                position: 'bottom',
            },
            hover: {
                mode: 'label'
            },
            scales: {
                xAxes: [{
                    scaleLabel: {
                        display: true,
                        labelString: 'Time'
                    },
                    ticks: {
                        maxTicksLimit: 8,
                        
                        // Return an empty string to draw the tick line but hide the tick label
                        // Return `null` or `undefined` to hide the tick line entirely
                           userCallback: function(value, index, values) {
                            return value;
                        }
                    }
                }],
                yAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'KiloBytes'
                    },
                    ticks: {
                        suggestedMax: 500,
                        min: 0
                        
                    }
                }]
            },
            title: {
                display: true,
                text: 'Use of network in this server'
            }
        }
    };
    
    var config_cpu = {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: "CPU idle",
                data: [],
                fill: false,
                borderColor: background_in,
                backgroundColor: background_in,
                pointBorderColor: background_in,
                pointBackgroundColor: background_in,
                pointRadius: 0,
                lineTension: 0,
                //borderDash: [5, 5],
            }]
        },
        options: {
            showXLabels: 10,
            animation : false,
            responsive: true,
            legend: {
                position: 'bottom',
            },
            hover: {
                mode: 'label'
            },
            scales: {
                xAxes: [{
                    scaleLabel: {
                        display: true,
                        labelString: 'Time'
                    },
                    ticks: {
                        maxTicksLimit: 8,
                        
                        // Return an empty string to draw the tick line but hide the tick label
                        // Return `null` or `undefined` to hide the tick line entirely
                           userCallback: function(value, index, values) {
                            return value;
                        }
                    }
                }],
                yAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'CPU idle %'
                    },
                    ticks: {
                        max: 100,
                        min: 0
                        
                    }
                }]
            },
            title: {
                display: true,
                text: 'Use of CPU in this server'
            }
        }
    };
    
    var config_mem = {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: "Used",
                data: [],
                fill: false,
                borderColor: '#dd0000',
                backgroundColor: '#dd0000',
                pointBorderColor: '#dd0000',
                pointBackgroundColor: '#dd0000',
                pointRadius: 0,
                lineTension: 0,
                //borderDash: [5, 5],
            }
            ,{
                label: "Free",
                data: [],
                fill: false,
                lineTension: 0,
                //borderDash: [5, 5],
                borderColor: background_out,
                backgroundColor: background_out,
                pointBorderColor: background_out,
                pointBackgroundColor: background_out,
                pointBorderWidth: 1,
                pointRadius: 0
            },
            {
                label: "Cached",
                data: [],
                fill: false,
                lineTension: 0,
                //borderDash: [5, 5],
                borderColor: background_in,
                backgroundColor: background_in,
                pointBorderColor: background_in,
                pointBackgroundColor: background_in,
                pointBorderWidth: 1,
                pointRadius: 0
            }]
        },
        options: {
            showXLabels: 10,
            animation : false,
            responsive: true,
            legend: {
                position: 'bottom',
            },
            hover: {
                mode: 'label'
            },
            scales: {
                xAxes: [{
                    scaleLabel: {
                        display: true,
                        labelString: 'Time'
                    },
                    ticks: {
                        maxTicksLimit: 8,
                        
                        // Return an empty string to draw the tick line but hide the tick label
                        // Return `null` or `undefined` to hide the tick line entirely
                           userCallback: function(value, index, values) {
                            return value;
                        }
                    }
                }],
                yAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'GigaBytes'
                    },
                    ticks: {
                        //suggestedMax: 500,
                        min: 0
                        
                    }
                }]
            },
            title: {
                display: true,
                text: 'Use of memory in this server'
            }
        }
    };
    
    //Now the disk donut
    
    var config_disk = {
            type: 'bar',
            data: {
                labels: ["/", "/home"],
                datasets: [{
                    label: 'Free space',
                    borderColor: background_in,
                    backgroundColor: background_in,
                    data: [0, 0]
                },
                {
                    label: 'Used space',
                    borderColor: "#ff0000",
                    backgroundColor: "#ff0000",
                    data: [0, 0]
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
                    text: 'Disk use in GigaBytes'
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

    var ctx_disk = document.getElementById("canvas_disk").getContext("2d");
    window.bar_disk = new Chart(ctx_disk, config_disk);
    
    var ctx = document.getElementById("canvas").getContext("2d");
    window.myLine = new Chart(ctx, config_net);
    
    var ctx_cpu = document.getElementById("canvas_cpu").getContext("2d");
    window.myLineCpu = new Chart(ctx_cpu, config_cpu);
    
    var ctx_mem = document.getElementById("canvas_mem").getContext("2d");
    window.myLineMem = new Chart(ctx_mem, config_mem);
    
    window.onload=function () {
        
        update_graph_net();
        update_graph_disk();
        setInterval(update_graph_net, 50000);
        setInterval(update_graph_disk, 100000);
    }
    
    var randomColor = function(opacity) {
        return 'rgba(' + Math.round(Math.random() * 255) + ',' + Math.round(Math.random() * 255) + ',' + Math.round(Math.random() * 255) + ', 1)';
    };
    
    function update_graph_net() {
        
        $.ajax({
        url: "<?php echo AdminUtils::set_admin_link('leviathan/graphs', ['op' => 1, 'server_id' => $server['IdServer']]); ?>",
        method: "GET",
        dataType: "json",
        data: {}
        }).done(function(data) {
            //{"1":{"bytes_sent":"4545988","bytes_recv":"116474015","date":"20160509220644","IdStatus_net":"1"},"2":{"bytes_sent":"4547076","bytes_recv":"116474967","date":"20160509220705","IdStatus_net":"2"}
            
            window.myLine.destroy();
            window.myLineCpu.destroy();
            window.myLineMem.destroy();
            
            config_net.data.labels=[];
            config_net.data.datasets[0].data=[];
            config_net.data.datasets[1].data=[];
            
            config_mem.data.labels=[];
            config_mem.data.datasets[0].data=[];
            config_mem.data.datasets[1].data=[];
            config_mem.data.datasets[2].data=[];
            
            config_cpu.data.labels=[];
            config_cpu.data.datasets[0].data=[];
            
            for(id in data){
                
                //alert(row);
                config_net.data.labels.push(data[id].date);
                config_net.data.datasets[0].data.push(data[id].bytes_recv);
                config_net.data.datasets[1].data.push(data[id].bytes_sent);
                
                config_cpu.data.labels.push(data[id].date);
                config_cpu.data.datasets[0].data.push(data[id].cpu);
                
                config_mem.data.labels.push(data[id].date);
                config_mem.data.datasets[0].data.push(data[id].memory_used);
                config_mem.data.datasets[1].data.push(data[id].memory_free);
                config_mem.data.datasets[2].data.push(data[id].memory_cached);
                
            }
            
            /*window.myLine.update();
            window.myLineCpu.update();
            window.myLineMem.update();
            
            window.myLine.resize();
            window.myLineCpu.resize();
            window.myLineMem.resize();*/
            
            window.myLine = new Chart(ctx, config_net);
            window.myLineCpu = new Chart(ctx_cpu, config_cpu);
            window.myLineMem = new Chart(ctx_mem, config_mem);
            
        }).fail(function (data) {
            
                alert(JSON.stringify(data));
            
        });
        
    };
    
    function update_graph_disk() {
        
        $.ajax({
        url: "<?php echo AdminUtils::set_admin_link('leviathan/graphs', ['op' => 2, 'server_id' => $server['IdServer']]); ?>",
        method: "GET",
        dataType: "json",
        data: {}
        }).done(function(data) {
            //{"1":{"bytes_sent":"4545988","bytes_recv":"116474015","date":"20160509220644","IdStatus_net":"1"},"2":{"bytes_sent":"4547076","bytes_recv":"116474967","date":"20160509220705","IdStatus_net":"2"}
            
            //data_disk.labels=[];
            //data_disk.datasets[0].data=[];
            
            z=0;
            
            config_disk.data.labels=[]
            
            for(key in data)
            {
                
                /*
                data: {
                    labels: ["/", "/home"],
                    datasets: [{
                        label: 'Free space',
                        borderColor: background_in,
                        backgroundColor: background_in,
                        data: [80, 86]
                    },
                    {
                        label: 'Used space',
                        borderColor: "#ff0000",
                        backgroundColor: "#ff0000",
                        data: [40, 16]
                    }]

                }
                */
                
                config_disk.data.labels.push(data[key]['disk']);
                config_disk.data.datasets[0].data[z]=((((data[key]['free'])/1024)/1024)/1024).toPrecision(2);
                config_disk.data.datasets[1].data[z]=((((data[key]['used'])/1024)/1024)/1024).toPrecision(2);
                //alert(config_disk.data.datasets[0].data[1]);
                
                /*
                config_disk.data.datasets[z]={};
                
                config_disk.data.datasets[z].labels=['pepe', 'popi'];

                config_disk.data.datasets[z].label

                config_disk.data.datasets[z].data=[];
                config_disk.data.datasets[z].backgroundColor=[];
                
                config_disk.data.datasets[z].data.push(data[key]['used']);
                config_disk.data.datasets[z].data.push(data[key]['free']);
                
                config_disk.data.datasets[z].backgroundColor.push('#FF0000');
                config_disk.data.datasets[z].backgroundColor.push('#46BFBD');
                */
                z++;
            }
            
            window.bar_disk.destroy();
            
            window.bar_disk = new Chart(ctx_disk, config_disk);
            
        }).fail(function (data) {
            
                alert(JSON.stringify(data));
            
        });
        
    };
    
</script>
<?php

}

?>
