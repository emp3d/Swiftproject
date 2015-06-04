<?php

function startServer($hostIp, $sshport, $account, $accpass, $startcmd) {
    $con = ssh2_connect($hostIp, $sshport);
    ssh2_auth_password($con, $account, $accpass);
    $command = "screen -d -S $account -m sh -c";
    ssh2_exec($con, "$command \"$startcmd\"");
}

function stopServer($hostIp, $sshport, $account, $accpass) {
    $con = ssh2_connect($hostIp, $sshport);
    ssh2_auth_password($con, $account, $accpass);
    $cmd2 = "screen -S $account -X quit";
    ssh2_exec($con, $cmd2);
}

function restartServer($hostIp, $sshport, $account, $accpass, $startcmd) {
    stopServer($hostIp, $sshport, $account, $accpass);
    startServer($hostIp, $sshport, $account, $accpass, $startcmd);
}

function checkStatus($hostIp, $sshport, $account, $accpass) {
    $con = ssh2_connect($hostIp, $sshport);
    ssh2_auth_password($con, $account, $accpass);
    $cmd1 = "screen -ls | grep -o '[0-9]\{3,4,5\}'";
    $stream = ssh2_exec($con, $cmd1);
    stream_set_blocking($stream, true);
    $output = fgets($stream);
    if (!$output) {
        return false;
    }
    return true;
}

function checkServer ($hostIp, $gameport) {
    $fp = fsockopen("udp://$hostIp",$gameport);
    if (!$fp) {
        return false;
    }
    socket_set_timeout($fp, 2);
    fputs($fp, "\xFF\xFF\xFF\xFFgetinfo");
    $o = fgets($fp);
    if ($o) {
        return true;
    }
    return false;
}
