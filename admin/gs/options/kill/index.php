<?php

$con2 = ssh2_connect("ip", "sshport");
ssh2_auth_password($con2, "account", "password");
$cmd1 = "screen -ls | grep -o '[0-9]\{5\}'";
$stream = ssh2_exec($con2, $cmd1);
stream_set_blocking($stream, true);
$output = fgets($stream);
$output = intval(trim($output));
$cmd2 = "screen -S $output -X quit";
ssh2_exec($con2, $cmd2);
die("SUCCESS");//