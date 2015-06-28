#!/usr/bin/php
<?php
    $dir = __DIR__;
    include "$dir/server.php";
    $mysql = include "$dir/../../../config.php";
    $query = "SELECT swift_servers.account AS account, swift_servers.name AS srvname, swift_servers.password AS accpass, swift_servers.script AS startcmd, swift_servers.port AS gameport, swift_hosts.ip AS hostIp, swift_hosts.sshport AS sshport FROM swift_servers, swift_hosts WHERE swift_servers.active=1 AND swift_servers.host_id = swift_hosts.id";
    $result = mysqli_query($mysql, $query);
    while ($row = mysqli_fetch_array($result)) {
        $account = trim($row['account']);
        $accpass = trim($row['accpass']);
        $startcmd = trim($row['startcmd']);
        $gameport = intval(trim($row['gameport']));
        $hostIp = trim($row['hostIp']);
	$server = $row['srvname'];
        $sshport = intval(trim($row['sshport']));
	$startcmd = str_replace("{port}", $gameport, $startcmd);
        $date = date("H:i, F j Y ", time());
        $startcmd = str_replace("{date}", $date, $startcmd);
	$query2 = "";
        if (!checkStatus($hostIp, $sshport, $account, $accpass)) {
  	    $time = time();
	    $query2 = "INSERT INTO swift_logs (username, ip, action, time) VALUES ('CRON Task', '$hostIp', 'Restarted server $server', '$time')";
            restartServer($hostIp, $sshport, $account, $accpass, $startcmd);
	    mysqli_query($mysql, $query2);
        } 
    }
?>

