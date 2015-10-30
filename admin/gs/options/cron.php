#!/usr/bin/php
<?php
    $dir = __DIR__;
    include "$dir/server.php";
    $mysql = include "$dir/../../../config.php";
    $setna = "UPDATE swift_servers SET players='N/A' WHERE active=0";
    mysqli_query($mysql, $setna);
    $query = "SELECT swift_servers.id AS srvid, swift_servers.account AS account, swift_servers.name AS srvname, swift_servers.password AS accpass, swift_servers.script AS startcmd, swift_servers.port AS gameport, swift_hosts.ip AS hostIp, swift_hosts.sshport AS sshport FROM swift_servers, swift_hosts WHERE swift_servers.active=1 AND swift_servers.host_id = swift_hosts.id";
    $result = mysqli_query($mysql, $query);
	$date = date("H:i:s, F j Y ");
	echo "\n--- CRON Task, Time - $date ---";
    while ($row = mysqli_fetch_array($result)) {
        $localIp = gethostbyname(gethostname());
        $srvid = intval(trim($row['srvid']));
        $account = trim($row['account']);
        $accpass = trim($row['accpass']);
        $startcmd = trim($row['startcmd']);
        $gameport = intval(trim($row['gameport']));
        $hostIp = trim($row['hostIp']);//
		if ($hostIp == "www.3d-sof2.com" || $hostIp == "185.34.216.28" /*|| $hostIp == nonddosprotectedip */) { 
			$hostIp = "127.0.0.1";
		}
	$server = $row['srvname'];
	echo "\n\nChecking server $server, ip $hostIp...";
        $sshport = intval(trim($row['sshport']));
	$startcmd = str_replace("{port}", $gameport, $startcmd);
        $date = date("H:i, F j Y ", time());
        $startcmd = str_replace("{date}", $date, $startcmd);
	$query2 = "";

        $localIp = gethostbyname(gethostname());
        if ($localIp != gethostbyname($hostIp) && $hostIp != "127.0.0.1") {
            continue;
        }
		echo "\nServer $server is installed locally, continuing...";
        if (!checkStatus($hostIp, $sshport, $account, $accpass)) {
            $checkUser = "SELECT time FROM swift_logs WHERE action LIKE '%tarted server $server%' AND  username != 'CRON Task' ORDER BY id DESC LIMIT 1";
            $data = mysqli_fetch_array(mysqli_query($mysql, $checkUser));
            $lastStart = intval(trim($data['time']));
            
            $checkReboots = "SELECT time FROM swift_logs WHERE username='CRON Task' AND action LIKE '%$server' AND time>$lastStart ORDER BY id DESC LIMIT 5";
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
				echo "\nServer $server stopped due to 5 reboots in a row.";
                stopServer($hostIp, $sshport, $account, $accpass);
                $closeServer = "UPDATE swift_servers SET active=0 WHERE id=$srvid";
                mysqli_query($mysql, $closeServer);
                $time = time();
                $setAlert = "INSERT INTO swift_logs (username, ip, action, time) VALUES('CRON Task', '$hostIp', 'Server $server did not start correctly and has now been stopped.', '$time')";
                mysqli_query($mysql, $setAlert);
                continue;
            }
			$time = time();
			echo "\nRebooting server $server due to screen being down";
			$query33 = "SELECT active FROM swift_servers WHERE id=$srvid";
			$result2 = mysqli_fetch_array(mysqli_query($mysql, $query33));
			if (intval($result2['active']) == 1) {
				$query2 = "INSERT INTO swift_logs (username, ip, action, time) VALUES ('CRON Task', '$hostIp', 'Restarted server $server', '$time')";
					restartServer($hostIp, $sshport, $account, $accpass, $startcmd);
				mysqli_query($mysql, $query2);
				continue;
			} else {
				continue;
			}
        } else {
			$conn = fsockopen("udp://$hostIp", $gameport);
        socket_set_timeout($conn, 2);
        fputs($conn, "\xFF\xFF\xFF\xFFgetinfo");
        $currentClients = $maxplayers = 0;
		$map = "";
        $players = "";
		$motd = "";
		$gametype = "";
        while ($o = fgets($conn)) {
            $o = str_replace("\xFF\xFF\xFF\xFFprint", "", $o);
            $o = str_replace("\xFF\xFF\xFF\xFFinfoResponse", "", $o);

            if (strlen($o) <= 1) {
                continue;
            }
            $strings = explode("\\", $o);

            for ($i = 0; $i < sizeof($strings); $i++) {
                $string = $strings[$i];
				
				if (strpos($string, "mapname") !== false) {
					$i++;
					$map = trim($strings[$i]);
					break;
				}
                if (strpos($string, "sv_maxclients") !== false) {
                    $i++;
                    $maxplayers = intval(trim($strings[$i]));
                    continue;
                }
                if (strpos($string, "clients") !== false) {
                    $i++;
                    $currentClients = intval(trim($strings[$i]));
                    continue;
                }
            }
        }
        fclose($conn);
        $players = "$currentClients / $maxplayers";
		if ($currentClients == $maxplayers && $maxplayers == 0) {
			$query33 = "SELECT active FROM swift_servers WHERE id=$srvid";
			$result3 = mysqli_fetch_array(mysqli_query($mysql, $query33));
			if (intval($result3['active']) == 1) {
				sleep(5);
				$conn = fsockopen("udp://$hostIp", $gameport);
				socket_set_timeout($conn, 2);
				fputs($conn, "\xFF\xFF\xFF\xFFgetinfo");
				$currentClients = $maxplayers = 0;
				$players = "";
				$gametype = "";
				$motd = "";
				while ($o = fgets($conn)) {
					$o = str_replace("\xFF\xFF\xFF\xFFprint", "", $o);
					$o = str_replace("\xFF\xFF\xFF\xFFinfoResponse", "", $o);

					if (strlen($o) <= 1) {
						continue;
					}
					$strings = explode("\\", $o);

					for ($i = 0; $i < sizeof($strings); $i++) {
						$string = $strings[$i];
						if (strpos($string, "mapname") !== false) {
							$i++;
							$map = trim($strings[$i]);
							break;
						}
						if (strpos($string, "sv_maxclients") !== false) {
							$i++;
							$maxplayers = intval(trim($strings[$i]));
							continue;
						}
						if (strpos($string, "clients") !== false) {
							$i++;
							$currentClients = intval(trim($strings[$i]));
							continue;
						}
						
					}
				}
				fclose($conn);
				$players = "$currentClients / $maxplayers";
				if ($currentClients == $maxplayers && $maxplayers == 0) {
					$query33 = "SELECT active FROM swift_servers WHERE id=$srvid";
					$result4 = mysqli_fetch_array(mysqli_query($mysql, $query33));
					if (intval($result4['active']) == 1) {
						echo "\nServer $server active, but dead. Rebooting";
						$time = time();
						$query2 = "INSERT INTO swift_logs (username, ip, action, time) VALUES ('CRON Task', '$hostIp', 'Restarted server $server (players 0 / 0)', '$time')";
						restartServer($hostIp, $sshport, $account, $accpass, $startcmd);
						mysqli_query($mysql, $query2);
						continue;
					}
				}
			} 
		}
		/* 3D Specific H&S statistics update code */
		if ($srvid == 6) {
			$motd = "";
			$gametype = "";
			$conn = fsockopen("udp://127.0.0.1", 20100);
			socket_set_timeout($conn, 2);
			fwrite($conn, "\xFF\xFF\xFF\xFFrcon rconpassword gametype");
			while ($o = fgets($conn)) {
				if (strpos($o, "Gametype") !== false) {
					$arr = explode(":", $o);
					$arrw = explode(".", $arr[1]);
					$gametype = trim($arrw[0]);
					$gametype = htmlentities($gametype);
				}
			}
			fwrite($conn, "\xFF\xFF\xFF\xFFrcon rconpassword g_motd");
			while ($o = fgets($conn)) {
				if (strpos($o, "g_motd") !== false) {
					
					$arr = explode("\"", $o);
					$motd = $arr[3];
					$motd = preg_replace("/\^./", "", $motd);
				}
			}
			fclose($conn);
			$somequery = "SELECT id, maxplayers FROM hns_stat WHERE mapname='$map' AND gametype='$gametype' AND motd='$motd'";
			
			$resultstat = mysqli_fetch_array(mysqli_query($mysql, $somequery));
			if (!$resultstat) {
				
				$somequery = "INSERT INTO hns_stat(mapname, totalplayers, totaltime, gametype, motd, maxplayers) VALUES('$map', '$currentClients', '1', '$gametype', '$motd', $currentClients)";
				mysqli_query($mysql, $somequery);
			} else {
				$someresultstat = $resultstat;
				$statid = intval(trim($someresultstat['id']));
				$maxplyrs = intval(trim($someresultstat['maxplayers']));
				if ($currentClients > $maxplyrs) {
					$maxplyrs = $currentClients;
				}
				$somequery = "UPDATE hns_stat SET totaltime = totaltime + 1, totalplayers = totalplayers + $currentClients, maxplayers = $maxplyrs WHERE id=$statid";
				mysqli_query($mysql, $somequery);
//delete which have totaltime 1 (timeout might occur on mapchange, task got old map but new g_motd)
$somequeryy = "DELETE FROM hns_stat WHERE totaltime=1";
mysqli_query($mysql, $somequeryy);
			}		
		}

		echo "\nSetting playercount to $players on server $server...";
        $updateSQL = "UPDATE swift_servers SET players='$players' WHERE id=$srvid";
        mysqli_real_escape_string($mysql, $updateSQL);
        mysqli_query($mysql, $updateSQL);
    
            
            
               
                
            
        }
    }
?>

