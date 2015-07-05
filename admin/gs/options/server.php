<?php

function deleteServer($hostIp, $sshport, $admacc, $admpass, $account, $password) {
    stopServer($hostIp, $sshport, $account, $password);
    $con = ssh2_connect($hostIp, $sshport);
    ssh2_auth_password($con, $admacc, $admpass);
    $command = "userdel -r $account";
    ssh2_exec($con, $command);
}

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
    $cmd1 = "screen -ls | grep -o '[0-9]\{1,5\}.$account'";
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
    socket_set_timeout($fp, 2);
    fputs($fp, "\xFF\xFF\xFF\xFFgetinfo");
    $o = fgets($fp);
    if ($o) {
        return true;
    }
    sleep(5);
    fputs($fp, "\xFF\xFF\xFF\xFFgetinfo");
    $o = fgets($fp);
    if ($o) {
        return true;
    }
    return false;
}

function addCronJob($hostIp, $sshport, $admacc, $admpass) {
    $con = ssh2_connect($hostIp, $sshport);
    ssh2_auth_password($con, $admacc, $admpass);
    $cmd = "crontab -l >> mycron";
    $loc = $_SERVER['DOCUMENT_ROOT'];
    $cmd2 = "echo \"\n* * * * * $loc/swiftproject/admin/gs/options/cron.php\" >> mycron";
    $cmd3 = "crontab mycron";
    $stream = ssh2_exec($con, $cmd);
    stream_set_blocking($stream, true);
    $stream = ssh2_exec($con, $cmd2);
    stream_set_blocking($stream, true);
    $stream = ssh2_exec($con, $cmd3);
    stream_set_blocking($stream, true);
}

function queryMaster() {
    $str = "";
    $servers = Array();
    $con = fsockopen("master.1fxmod.org", 20110);
    stream_set_timeout($con, 1);
    fputs($con, "\xFF\xFF\xFF\xFFgetservers 2002 empty full");
    while ($o = fgets($con)) {
        $str .= $o;
    }
    fputs($con, "\xFF\xFF\xFF\xFFgetservers 2004 empty full");
    while ($o = fgets($con)) {
        $str .= $o;
    }
    for ($i = 0; $i < strlen($str); $i++) {
        if ($str[$i] == "\\" && $str[$i + 7] == "\\") {
            $ip = ord($str[$i + 1]) . "." . ord($str[$i + 2]) . "." . ord($str[$i + 3]) . "." . ord($str[$i + 4]);
            $port = (ord($str[$i + 5]) << 8) + ord($str[$i + 6]);
            array_push($servers, array($ip, $port));
        }
    }
    return servers;
}