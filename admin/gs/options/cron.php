#!/usr/bin/php
<?php
    include '/var/www/swiftproject/admin/gs/options/server.php';
    $mysql = include '/var/www/swiftproject/config.php';
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
	echo "Checking server $server \n";
        if (!checkStatus($hostIp, $sshport, $account, $accpass)) {
            //screen down, start it up again.
  	    $time = time();
	    $date = date("H:i, F j Y ", $time);
            echo "Date - $date.\nScreen for server $server is down, restarting...\n";
            startServer($hostIp, $sshport, $account, $accpass, $startcmd);
        } else if (!checkServer($hostIp, $gameport)) {
            //screen up, server down, start it up again
	    $time = time();
	    $date = date("H:i, F j Y ", $time);
            echo "Date - $date.\nServer $server is down, restarting...\n";
            restartServer($hostIp, $sshport, $account, $accpass, $startcmd);
        } else {
	    echo "Server $server seems to be up and running!\n";
	}
    }
?>

