<?php

use PhangoApp\PhaI18n\I18n;
use PhangoApp\PhaLibs\AdminUtils;

function multiprogressView($name_task, $description_task, $task_id, $num_servers)
{

?>

<h2><?php echo $name_task; ?></h2>
<p><?php echo $description_task; ?></p>
<hr />
<p><?php echo I18n::lang('phangoapp/leviathan', 'num_servers', 'Number of servers'); ?>: <span id="num_servers"><?php echo $num_servers; ?></span></p>
<p><?php echo I18n::lang('phangoapp/leviathan', 'completed_tasks', 'Completed tasks'); ?>: <span id="num_completed">0</span></p>
<p id="detecting_servers" style="display:none;"><?php echo I18n::lang('phangoapp/leviathan', 'making_tasks', 'Making tasks...'); ?> <i class="fa fa-cog fa-spin fa-fw"></i></p>
<table class="table_servers">
    <tr class="row_server" id="father_server" style="display:none;">
        <td class="hostname">Hostname</td>
        <td class="progress">In progress <i class="fa fa-cog fa-spin fa-fw"></i> <a href="#" class="server_log" target="_blank"><?php echo I18n::lang('phangoapp/leviathan', 'server_log', 'Server log'); ?></a></td>
    </tr>
</table>
<div id="finished" style="display:none;">
    <p><strong>All tasks were finished.</strong></p>
</div>
<script>
    
    total_servers=<?php echo $num_servers; ?>;
    total_servers_log=0;
    total_servers_done=0;
    servers_to_delete={};
    deleted=0;
    position=0;
    
    arr_servers={};
    
    if(total_servers>0)
    {
        $('#detecting_servers').fadeIn(1);
        
        get_progress_servers();
    }
    else
    {
        $('#detecting_servers').html('Sorry, no servers to update');
    }
    /*
    function get_servers() {
        
    $('#detecting_servers').fadeIn(1);
    
     $.ajax({
        url: "<?php echo AdminUtils::set_admin_link('leviathan/showmultiprogress', ['op' => 1, 'task_id' => $task_id]); ?>/position/"+total_servers_log,
        method: "GET",
        dataType: "json",
        data: {}
        }).done(function(data) {
            
            if(data.error==0)
            {
                
                c=data.servers.length;
                
                //if(c==total_servers)
                if(c>0)
                {
                
                    for(x=0;x<c;x++) {
                        
                        row=$('#father_server').clone().appendTo('.table_servers');
                        
                        row.attr('id', data.servers[x].ip)
                        
                        row.css('display', 'block');
                        
                        row.children('.hostname').html(data.servers[x].hostname);
                        
                        row.children('.progress').children('a').attr('href', '<?php echo AdminUtils::set_admin_link('leviathan/showprogress', ['task_id' =>  $task_id]); ?>/server/'+data.servers[x].ip);
                        
                        arr_servers[data.servers[x].ip]=data.servers[x].ip;
                        
                    }
                    
                    total_servers_log+=c;
                    
                    get_progress_servers();
                    
                }
                else
                {
                    setTimeout(get_servers, 1000);
                    
                }
                
            }
            else
            {
             
                $('#detecting_servers').html('<span class="error">'+data.message+'</span>');
                
            }
        
        }).fail(function (data) {
            
                alert(JSON.stringify(data));
            
        });
     
    }*/
    /*
    function get_progress_servers() {
        
        $.ajax({
        url: "<?php echo AdminUtils::set_admin_link('leviathan/showmultiprogress', ['op' => 2, 'task_id' => $task_id]); ?>",
        method: "POST",
        dataType: "json",
        data: {servers: JSON.stringify(arr_servers)}
        }).done(function(data) {
           
           if(total_servers_done<total_servers_log)
           {
                
               for(x in data) {
                    
                    if(data[x].status==1) {
                    
                        //server_index=arr_servers.indexOf(data[x].server)
                        
                        server_dom=$(document.getElementById(data[x].server));
                        
                        delete arr_servers[data[x].server];
                        
                        if(data[x].error==1) {
                            //alert($('#'+data[x].server).attr('id'));
                            //$('#'+data[x].server);
                            
                            server_dom.children('.progress').children('i').removeClass('fa-cog fa-spin fa-fw');
                            server_dom.children('.progress').children('i').addClass('fa-ban');
                            server_dom.children('.progress').children('i').css('color', '#ff0000');
                            server_dom.children('.progress').append(' Error: please, review the log')
                            
                            //alert('#'+data[x].server);
                            
                        }
                        else {
                            
                            server_dom.children('.progress').children('i').removeClass('fa-cog fa-spin fa-fw');
                            server_dom.children('.progress').children('i').addClass('fa-check');
                            server_dom.children('.progress').children('i').css('color', '#005a00');
                            
                            servers_to_delete.push(data[x].server);
                            
                        }
                        
                        total_servers_done+=1;
                        
                        $('#num_completed').html(total_servers_done);
                    }
                   
               }
               
               setTimeout(get_progress_servers, 1000);
            
            }
            else
            {
           
                if(total_servers_done<total_servers) {
               
                    setTimeout(get_servers, 1000);
                    
                    //Delete servers
                
                    c=servers_to_delete.length;
                    
                    for(x=deleted;x<c;x++)
                    {
                            
                        $(document.getElementById(servers_to_delete[x])).fadeOut(1000, function() {
                    
                            $(document.getElementById(servers_to_delete[x])).remove();
                        
                        });
                                    
                    }
                    
                    deleted=c;
            
                }
                else {
                    
                    //Delete servers
                    
                    c=servers_to_delete.length;
                    
                    for(x=deleted;x<c;x++)
                    {
                     
                        $(document.getElementById(servers_to_delete[x])).fadeOut(1000, function() {
                    
                            $(document.getElementById(servers_to_delete[x])).remove();
                        
                        });
                                    
                    }
                    
                    deleted=c;
                    
                    $('#detecting_servers').fadeOut(1);
                    $('#finished').fadeIn(1000);
                    
                }
                
            }
        
        }).fail(function (data) {
            
                alert(JSON.stringify(data));
            
        });
        
    }*/
    
    function get_progress_servers() {
        
        $.ajax({
        url: "<?php echo AdminUtils::set_admin_link('leviathan/showmultiprogress', ['op' => 1, 'task_id' => $task_id]); ?>/position/"+position,
        method: "GET",
        dataType: "json",
        }).done(function(data) {
            
            servers_to_delete=[];
           
            for(x in data)
            {
                
                if(arr_servers.hasOwnProperty(data[x].server))
                {
                    
                    if(data[x].status==1)
                    {
                        server_dom=$(document.getElementById(data[x].server));
                        
                        if(data[x].error==1) {
                            
                            server_dom.children('.progress').children('i').removeClass('fa-cog fa-spin fa-fw');
                            server_dom.children('.progress').children('i').addClass('fa-ban');
                            server_dom.children('.progress').children('i').css('color', '#ff0000');
                            server_dom.children('.progress').append(' <?php echo I18n::lang('phangoapp/leviathan', 'error_review_log', 'Error: please, review the log'); ?>');
                            
                        }
                        else {
                            
                            server_dom.children('.progress').children('i').removeClass('fa-cog fa-spin fa-fw');
                            server_dom.children('.progress').children('i').addClass('fa-check');
                            server_dom.children('.progress').children('i').css('color', '#005a00');
                            
                            servers_to_delete[data[x].server]=data[x].server;
                            
                        }
                        
                        total_servers_done+=1;
                    
                    }
                    
                    $('#num_completed').html(total_servers_done);
                    
                }
                else
                {
                    
                    row=$('#father_server').clone().appendTo('.table_servers');
                        
                    row.attr('id', data[x].server);
                    
                    row.css('display', 'block');
                    
                    row.children('.hostname').html(data[x].hostname);
                    
                    row.children('.progress').children('a').attr('href', '<?php echo AdminUtils::set_admin_link('leviathan/showprogress', ['task_id' =>  $task_id]); ?>/server/'+data[x].server);
                    
                    arr_servers[data[x].server]=data[x].server;
                    
                }
                
                c=servers_to_delete.length;
                    
                
            }
           
            if(total_servers_done<total_servers)
            {
                
                setTimeout(get_progress_servers, 1000);
                
            }
            else
            {
                
                $('#detecting_servers').fadeOut(1);
                $('#finished').fadeIn(1000);
                
            }
            /*
            for(x=deleted;x<c;x++)
            {
                    
                $(document.getElementById(servers_to_delete[x])).fadeOut(1000, function() {
            
                    $(document.getElementById(servers_to_delete[x])).remove();
                
                });
                            
            }*/
            
            position+=data.length;
            
            
            
        
        }).fail(function (data) {
            
            alert(JSON.stringify(data));
            
        });
        
    }
    
    
</script>

<?php
}
?>
