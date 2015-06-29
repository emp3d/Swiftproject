#!/usr/bin/php
<?php
    $dir = __DIR__;
    include "$dir/server.php";
    $mysql = include "$dir/../../../config.php";
    $query = "SELECT swift_servers.id AS srvid, swift_servers.account AS account, swift_servers.name AS srvname, swift_servers.password AS accpass, swift_servers.script AS startcmd, swift_servers.port AS gameport, swift_hosts.ip AS hostIp, swift_hosts.sshport AS sshport FROM swift_servers, swift_hosts WHERE swift_servers.active=1 AND swift_servers.host_id = swift_hosts.id";
    $result = mysqli_query($mysql, $query);
    while ($row = mysqli_fetch_array($result)) {
        $srvid = intval(trim($row['srvid']));
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
            //check that is something wrong, has the server been restarting continuously.
            $checkReboots = "SELECT time FROM swift_logs WHERE username='CRON Task' AND action LIKE '%$server' ORDER BY id DESC LIMIT 5";
            $getTimes = mysqli_query($mysql, $checkReboots);
            $i = 1;
            $lastTime = time();
            while ($times = mysqli_fetch_array($getTimes)) {
                $time = intval(trim($times['time']));
                if ($lastTime - $time <= 75) {
                    $i++;
                    $lastTime = $time;
                }
            }
            if ($i == 5) {
                stopServer($hostIp, $sshport, $account, $accpass);
                $closeServer = "UPDATE swift_servers SET active=0 WHERE id=$srvid";
                mysqli_query($mysql, $closeServer);
                $time = time();
                $setAlert = "INSERT INTO swift_logs (username, ip, action, time) VALUES('CRON Task', '$hostIp', 'Server $server did not start correctly and has now been stopped.', '$time')";
                mysqli_query($mysql, $setAlert);
                break;
            }
  	    $time = time();
	    $query2 = "INSERT INTO swift_logs (username, ip, action, time) VALUES ('CRON Task', '$hostIp', 'Restarted server $server', '$time')";
            restartServer($hostIp, $sshport, $account, $accpass, $startcmd);
	    mysqli_query($mysql, $query2);
        } 
    }
?>

