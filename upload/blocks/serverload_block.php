<?php
//SERVER LOAD BLOCK

if ($CURUSER){
begin_block(T_("SERVER_LOAD"));

//Get average CPU usage 
$cpuUsage = sys_getloadavg(); 
echo "<li><b>CPU Load:</b> <b>".$cpuUsage[0]."</b></li>"; 
//Display server script execution time 
$finishedTime = number_format((float)(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]), 3, '.', ''); echo "<li><b>Execution Time:</b> <b>".$finishedTime." sec</b></li>"; 
//Display Memory used and allocated 
$usedMem = number_format((float) (memory_get_peak_usage(false) / 1024 / 1024), 2, '.', ''); 
echo "<li><b>Used Memory:</b> <b>" . $usedMem . "MB</b></li>"; 
echo "<li><b><Allocated Memory:</b> <b>" . (memory_get_peak_usage(true) / 1024 / 1024) . "MB</b></li>";

end_block();
}
?>