#!/usr/bin/php
<?php
    $dir = __DIR__;
    include "$dir/server.php";
    $mysql = include "$dir/../../../config.php";
    $setNA = "UPDATE swift_servers SET players='N/A' WHERE active=0";
    mysqli_real_escape_string($mysql, $setNA);
    mysqli_query($mysql, $setNA);
    $getServers = "SELECT swift_servers.port AS gport, swift_hosts.ip AS hostip, swift_servers.id AS gid FROM swift_servers, swift_hosts WHERE swift_servers.host_id=swift_hosts.id AND swift_servers.active=1";
    $result = mysqli_query($mysql, $getServers);
    while ($sqldata = mysqli_fetch_array($result)) {
        $hostip = trim($sqldata['hostip']);
        $port = intval(trim($sqldata['gport']));
        $srvid = intval(trim($sqldata['gid']));
        $conn = fsockopen("udp://$hostip", $port);
        socket_set_timeout($conn, 2);
        fputs($conn, "\xFF\xFF\xFF\xFFgetinfo");
        $currentClients = $maxplayers = 0;
        $players = "";
        while ($o = fgets($conn)) {
            $o = str_replace("\xFF\xFF\xFF\xFFprint", "", $o);
            $o = str_replace("\xFF\xFF\xFF\xFFinfoResponse", "", $o);

            if (strlen($o) <= 1) {
                continue;
            }
            $strings = explode("\\", $o);

            for ($i = 0; $i < sizeof($strings); $i++) {
                $string = $strings[$i];
                if (strpos($string, "sv_maxclients") !== false) {
                    $i++;
                    $maxplayers = intval(trim($strings[$i]));
                    continue;
                }
                if (strpos($string, "clients") !== false) {
                    $i++;
                    $currentClients = intval(trim($strings[$i]));
                    break;
                }
            }
        }
        fclose($conn);
        $players = "$currentClients / $maxplayers";
        $updateSQL = "UPDATE swift_servers SET players='$players' WHERE id=$srvid";
        mysqli_real_escape_string($mysql, $updateSQL);
        mysqli_query($mysql, $updateSQL);
    }
