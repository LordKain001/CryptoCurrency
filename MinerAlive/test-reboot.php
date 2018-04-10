<?php
echo shell_exec("ls");
echo shell_exec("sudo /home/miner/MinerAlive/force-reboot");
shell_exec("echo s | sudo tee /proc/sysrq-trigger");
shell_exec("echo b | sudo tee /proc/sysrq-trigger");
?>

