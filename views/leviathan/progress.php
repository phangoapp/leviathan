<?php

use PhangoApp\PhaI18n\I18n;
use PhangoApp\PhaView\View;
use PhangoApp\PhaLibs\AdminUtils;

function ProgressView($name_task, $hostname, $description_task, $task_id, $ip, $position=0)
{

    ?>
    <h2><?php echo I18n::lang('phastafari', 'task progress', 'Task progress'); ?> - <?php echo $name_task; ?></h2>
    <p><?php echo $description_task; ?></p>
    <hr />
    <i class="fa fa-cog fa-spin fa-5x fa-fw margin-bottom" id="gear"></i>
    <div id="progressbar"><div class="progress-label"><?php echo I18n::lang('phastafari/dashboard', 'processing_task', 'Processing task...'); ?></div></div>
    <div id="no_progress" style="border: solid #cbcbcb 1px;height:150px;overflow:scroll;padding:2px;"></div>
    <script>
        
        position=<?php echo $position; ?>;
        yes_progress=1;
        yes_position=0;
        last_status=0;
        
        text_complete="Complete!";
        
        var progressbar = $( "#progressbar" ),
        progressLabel = $( ".progress-label" );
        
            progressbar.progressbar({
              value: false,
              change: function() {
                  
                if(progressbar.progressbar( "value" )>0)
                {
                  
                    progressLabel.text( progressbar.progressbar( "value" ) + "%" );
                    
                }
                else
                {
                    progressbar.progressbar( "value", false);
                    progressLabel.text( "Processing task..." );
                    
                }
              },
              complete: function() {
                progressLabel.text( text_complete );
              }
            });
        
        
        function update_progress()
        {
            
         var objDiv = document.getElementById("no_progress");
        
         function update_messages_queue(message)
         {
             
             setTimeout(function () {
                 
                 $('#no_progress').append(message);
                 objDiv.scrollTop = objDiv.scrollHeight;
                 
             }, 500);
             
         }
         
         function update_progress_messages_queue(message, progress)
         {
             
             setTimeout(function () {
                
                progress=parseInt(progress);
                            
                progressbar.progressbar( "value", progress );
                
                $('#no_progress').append(message+'<br />');
                
                objDiv.scrollTop = objDiv.scrollHeight;
                 
             }, 600);
             
         }
         
         function finish_progress_error(progress)
         {
             
            progressbar.progressbar( "value", progress );
            progressLabel.text( "ERROR, please see the log" );
             
         }
         
         $.ajax({
            url: "<?php echo AdminUtils::set_admin_link('leviathan/showprogress', ['op' => 1, 'task_id' => $task_id, 'server' => $ip]); ?>/position/"+position,
            method: "POST",
            dataType: "json",
            data: {}
            }).done(function(data) {
                
                if(!data.hasOwnProperty("wait"))
                {
                    
                    x=data.length;
                    
                    for(k=0;k<x;k++)
                    {
                        
                        if(data[k].no_progress==1)
                        {
                            //yes_progress=0;
                            //$('#no_progress').append(data[k].message+'<br />');
                            update_messages_queue(data[k].message+'<br />');
                            
                            //Scroll
                            
                        }
                        else
                        if(data[k].no_progress==0)
                        {
                            
                            update_progress_messages_queue(data[k].message, data[k].progress);
                            
                        }
                        
                        if(data[k].status!=1)
                        {
                            position+=1;
                            last_status=0;
                            
                        }
                        else
                        {
                            
                            last_status=1;
                            
                            if(data[k].error==1)
                            {
                                text_complete='ERROR, please see the log';
                                finish_progress_error(data[k].progress);
                                
                            }
                            else
                            {
                                
                                //progressLabel.text( "Complete!" );
                                
                            }
                            
                            //clearTimeout(update_interval);
                            
                            $('#gear').removeClass('fa-spin');
                            
                        }
                        
                    }
                    
                    if(last_status==0)
                    {
                        
                        update_interval = setTimeout(update_progress, 1000);
                        
                    }
            
                }
                else
                {
                    
                    update_interval = setTimeout(update_progress, 1000);
                    
                }
                
                
            
            }).fail(function (data) {
                
                    alert(JSON.stringify(data));
                
            });
        }
        
        update_progress();
        
    </script>
    <?php
    ob_start();
    ?>
      <style>
      .ui-progressbar {
        position: relative;
      }
      .progress-label {
        position: absolute;
        left: 50%;
        top: 4px;
        font-weight: bold;
        color: #fff;
        text-shadow: 1px 1px 0 #000;
      }
      </style>
    <?php
    
    View::$header[]=ob_get_contents();
    
    ob_end_clean();
    
    View::$css_module['leviathan'][]='jquery-ui.min.css';
    View::$css_module['leviathan'][]='jquery-ui.theme.min.css';
    View::$js_module['leviathan'][]='jquery-ui.min.js';

}

?>
