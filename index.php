<!DOCTYPE html>
<html>
    <head>
    <title>Twilio Client Quickstart</title>
    <link rel="stylesheet" href="css/site.css">
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
        <link rel="shortcut icon" type="image/x-icon" href="https://orbisx.ca/app/favicon.ico">
    </head>
    <body>
      
        <script>
            function dialerClick(type, value) {
                let input = $('#phone-number');
                let input_val = $('#phone-number').val();
                if (type == 'dial') {
                    input.val(input_val + value);
                } else if (type == 'delete') {
                    input.val(input_val.substring(0, input_val.length - 1));
                } else if (type == 'clear') {
                    input.val("");
                }
            }
        </script>
        <!-- Modal -->
        <div class="call_logs" id="call_logs">
            <?php 
                require_once 'api/prerequist.php';
                $db = database();
                $page = isset($_GET['page']) ? $_GET['page'] : 1;
                $limit = 20;
                $offset = ($page - 1) * $limit;
                $total_pages =  ceil($db->query("SELECT * FROM call_logs")->num_rows / $limit);
                $query = " SELECT * FROM `call_logs` ORDER BY `id` DESC limit $limit OFFSET $offset";
                $final_result = [];
                if ($result = $db->query($query)) {
                    while ($row = $result->fetch_row()) {
                        $final_result[] = $row;
                    }
                    
                  }
                  $db -> close();
               
            ?>
             <div class="container">    
                <div style="margin-top:20px">
                    <div style="float:left">
                        <h2>Call Logs</h2>
                    </div>
                    <div style="float:right">
                        <a type="button" id="diale_btn" class="btn btn-primary tablinks" data-toggle="modal" data-target="#dialer_modal">Dialer</a> 
                    </div> 
                    <div class="clearfix"></div> 
                    <p>Logs are coming against their creation date.</p>  
                    
                </div>
                <div>          
                    <table class="table">
                        <thead>
                        <tr>
                            <th colspan="1">Sr No.</th>
                            <th colspan="2">From</th>
                            <th colspan="2">To</th>
                            <th colspan="6">Recording</th>
                            <th colspan="1">Date</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($final_result)) { ?>
                                <?php $i=0; foreach($final_result as $item) { ?>
                                    <tr>
                                        <td colspan="1"><?php echo ++$i; ?></td>
                                        <td colspan="2"><?php echo str_replace('client:', '', $item['1']) ?? ''?></td>
                                        <td colspan="2"><?php echo $item['2'] ?? ''?></td>
                                        <td colspan="6"><?php if(!empty($item['5'])) {?>

                                            <audio controls>
                                            <source src="<?php echo $item['5']?>.ogg" type="audio/ogg">
                                            <source src="<?php echo $item['5']?>.mp3" type="audio/mpeg">
                                            Your browser does not support the audio element.
                                            </audio>
                                            <?php } else{?>
                                                <?php echo "No recording";?>
                                                <?php } ?>
                                        </td>
                                        <td colspan="1"><?php echo date('m/d/Y h:i A', strtotime($item['7'])) ?? ''?></td>
                                    </tr>
                                <?php } ?>
                                <?php if ($page < $total_pages) {?>
                                    <div style="float:left"> Pages <?php echo $page; ?> of <?php echo $total_pages; ?> </div>
                                <?php } if ($page < $total_pages) {?>    
                                    <div style="float:right; margin-left:20px"><a href="<div style="float:right"><a href="index.php?page=<?php echo $page + 1; ?>">Next</a></div>
                                <?php } if ($page > 1 && $page <= $total_pages) {?>    
                                    <div style="float:right"><a href="index.php?page=<?php echo $page - 1; ?>">Last</a></div>
                            
                                <?php } ?>    
                            <?php } ?>        
                    
                        </tbody>
                    </table>
                </div>
                <?php if ($page < $total_pages) {?>
                    <div style="float:left"> Pages <?php echo $page; ?> of <?php echo $total_pages; ?> </div>
                <?php } if ($page < $total_pages) {?>    
                    <div style="float:right; margin-left:20px"><a href="<div style="float:right"><a href="index.php?page=<?php echo $page + 1; ?>">Next</a></div>
                <?php } if ($page > 1 && $page <= $total_pages) {?>    
                    <div style="float:right"><a href="<div style="float:right"><a href="index.php?page=<?php echo $page - 1; ?>">Last</a></div>
                
                <?php } ?>    
            </div>


        </div>
        <div class="modal fade" id="dialer_modal" tabindex="-1" aria-labelledby="dialer_modal_label" aria-hidden="true" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="dialer_modal_label">Dialer</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="dialpad-screen" id="dialpad-dialer">
                            <table id="dialer_table">
                                <tr>
                                    <td colspan="3">
                                        <input id="phone-number" type="number" class="form-control" />
                                    </td>
                                </tr>
                                <tr class="dialer_num_tr">
                                    <td class="dialer_num" onclick="dialerClick('dial', 1)">1</td>
                                    <td class="dialer_num" onclick="dialerClick('dial', 2)">2</td>
                                    <td class="dialer_num" onclick="dialerClick('dial', 3)">3</td>
                                </tr>
                                <tr class="dialer_num_tr">
                                    <td class="dialer_num" onclick="dialerClick('dial', 4)">4</td>
                                    <td class="dialer_num" onclick="dialerClick('dial', 5)">5</td>
                                    <td class="dialer_num" onclick="dialerClick('dial', 6)">6</td>
                                </tr>
                                <tr class="dialer_num_tr">
                                    <td class="dialer_num" onclick="dialerClick('dial', 7)">7</td>
                                    <td class="dialer_num" onclick="dialerClick('dial', 8)">8</td>
                                    <td class="dialer_num" onclick="dialerClick('dial', 9)">9</td>
                                </tr>
                                <tr class="dialer_num_tr">
                                    <td class="dialer_del_td">
                                        <img alt="clear" onclick="dialerClick('clear', 'clear')" src="data:image/svg+xml;base64,PHN2ZyBhcmlhLWhpZGRlbj0idHJ1ZSIgZm9jdXNhYmxlPSJmYWxzZSIgZGF0YS1wcmVmaXg9ImZhcyIgZGF0YS1pY29uPSJlcmFzZXIiIHJvbGU9ImltZyIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB2aWV3Qm94PSIwIDAgNTEyIDUxMiIgY2xhc3M9InN2Zy1pbmxpbmUtLWZhIGZhLWVyYXNlciBmYS13LTE2IGZhLTd4Ij48cGF0aCBmaWxsPSIjYjFiMWIxIiBkPSJNNDk3Ljk0MSAyNzMuOTQxYzE4Ljc0NS0xOC43NDUgMTguNzQ1LTQ5LjEzNyAwLTY3Ljg4MmwtMTYwLTE2MGMtMTguNzQ1LTE4Ljc0NS00OS4xMzYtMTguNzQ2LTY3Ljg4MyAwbC0yNTYgMjU2Yy0xOC43NDUgMTguNzQ1LTE4Ljc0NSA0OS4xMzcgMCA2Ny44ODJsOTYgOTZBNDguMDA0IDQ4LjAwNCAwIDAgMCAxNDQgNDgwaDM1NmM2LjYyNyAwIDEyLTUuMzczIDEyLTEydi00MGMwLTYuNjI3LTUuMzczLTEyLTEyLTEySDM1NS44ODNsMTQyLjA1OC0xNDIuMDU5em0tMzAyLjYyNy02Mi42MjdsMTM3LjM3MyAxMzcuMzczTDI2NS4zNzMgNDE2SDE1MC42MjhsLTgwLTgwIDEyNC42ODYtMTI0LjY4NnoiIGNsYXNzPSIiPjwvcGF0aD48L3N2Zz4=" width="22px" title="Clear" />
                                    </td>
                                    <td class="dialer_num" onclick="dialerClick('dial', 0)">0</td>
                                    <td class="dialer_del_td">
                                        <img alt="delete" onclick="dialerClick('delete', 'delete')" src="data:image/svg+xml;base64,PHN2ZyBhcmlhLWhpZGRlbj0idHJ1ZSIgZm9jdXNhYmxlPSJmYWxzZSIgZGF0YS1wcmVmaXg9ImZhciIgZGF0YS1pY29uPSJiYWNrc3BhY2UiIHJvbGU9ImltZyIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB2aWV3Qm94PSIwIDAgNjQwIDUxMiIgY2xhc3M9InN2Zy1pbmxpbmUtLWZhIGZhLWJhY2tzcGFjZSBmYS13LTIwIGZhLTd4Ij48cGF0aCBmaWxsPSIjREMxQTU5IiBkPSJNNDY5LjY1IDE4MS42NWwtMTEuMzEtMTEuMzFjLTYuMjUtNi4yNS0xNi4zOC02LjI1LTIyLjYzIDBMMzg0IDIyMi4wNmwtNTEuNzItNTEuNzJjLTYuMjUtNi4yNS0xNi4zOC02LjI1LTIyLjYzIDBsLTExLjMxIDExLjMxYy02LjI1IDYuMjUtNi4yNSAxNi4zOCAwIDIyLjYzTDM1MC4wNiAyNTZsLTUxLjcyIDUxLjcyYy02LjI1IDYuMjUtNi4yNSAxNi4zOCAwIDIyLjYzbDExLjMxIDExLjMxYzYuMjUgNi4yNSAxNi4zOCA2LjI1IDIyLjYzIDBMMzg0IDI4OS45NGw1MS43MiA1MS43MmM2LjI1IDYuMjUgMTYuMzggNi4yNSAyMi42MyAwbDExLjMxLTExLjMxYzYuMjUtNi4yNSA2LjI1LTE2LjM4IDAtMjIuNjNMNDE3Ljk0IDI1Nmw1MS43Mi01MS43MmM2LjI0LTYuMjUgNi4yNC0xNi4zOC0uMDEtMjIuNjN6TTU3NiA2NEgyMDUuMjZDMTg4LjI4IDY0IDE3MiA3MC43NCAxNjAgODIuNzRMOS4zNyAyMzMuMzdjLTEyLjUgMTIuNS0xMi41IDMyLjc2IDAgNDUuMjVMMTYwIDQyOS4yNWMxMiAxMiAyOC4yOCAxOC43NSA0NS4yNSAxOC43NUg1NzZjMzUuMzUgMCA2NC0yOC42NSA2NC02NFYxMjhjMC0zNS4zNS0yOC42NS02NC02NC02NHptMTYgMzIwYzAgOC44Mi03LjE4IDE2LTE2IDE2SDIwNS4yNmMtNC4yNyAwLTguMjktMS42Ni0xMS4zMS00LjY5TDU0LjYzIDI1NmwxMzkuMzEtMTM5LjMxYzMuMDItMy4wMiA3LjA0LTQuNjkgMTEuMzEtNC42OUg1NzZjOC44MiAwIDE2IDcuMTggMTYgMTZ2MjU2eiIgY2xhc3M9IiI+PC9wYXRoPjwvc3ZnPg==" width="25px" title="Delete" />
                                    </td>
                                </tr>
                                <!-- <tr>
                                    <td colspan="3">
                                        
                                    </td>
                                </tr> -->
                            </table>
                        </div>
                        <div class="dialpad-screen text-capitalise hidden" id="dialpad-calling">
                            <p class="text-center" id="call-direction"></p>
                            <p class="text-center" id="display-number"></p>
                            <p class="text-center" id="dialer-timer"></p>
                        </div>
                        <div class="dialpad-footer text-center">
                            <a href="#" type="button" class="call-btn call-accept" id="button-call">
                                <i class="fa fa-phone" aria-hidden="true"></i>
                            </a>
                            <a href="#" type="button" class="call-btn call-reject" id="button-hangup">
                                <i class="fa fa-phone" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        <div id="info" class="hide">
            <p class="instructions">Twilio Client</p>
            <div id="client-name"></div>
            <div id="output-selection">
            <label>Ringtone Devices</label>
            <select id="ringtone-devices" multiple></select>
            <label>Speaker Devices</label>
            <select id="speaker-devices" multiple></select><br/>
            <a id="get-devices">Seeing unknown devices?</a>
            </div>
        </div>
        <div id="call-controls"  class="hide">
            <p class="instructions">Make a Call:</p>
            <div id="volume-indicators">
            <label>Mic Volume</label>
            <div id="input-volume"></div><br/><br/>
            <label>Speaker Volume</label>
            <div id="output-volume"></div>
            </div>
        </div>
        <div id="log"  class="hide"></div>
        <div id="client-name"  class="hide"></div>
        <script type="text/javascript"
            src="js/twilio.js"></script>  <script src="js/jquery.min.js"></script>
        <script src="js/quickstart.js"></script>
    </body>
</html>