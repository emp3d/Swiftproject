<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
            include 'server.php';
            $hostIp = "185.34.216.28";
            $hostPort = 20100;
            $op = checkServer($hostIp, $hostPort);
            if ($op) {
                echo "true for $hostIp <br>";
            } else {
                echo "false for $hostIp <br>";
            }
            $hostIp = "127.0.0.1";
            $op = checkServer($hostIp, $hostPort);
            if ($op) {
                echo "true for $hostIp <br>";
            } else {
                echo "false for $hostIp <br>";
            }
            $hostIp = "213.163.68.175";
            $op = checkServer($hostIp, $hostPort);
            if ($op) {
                echo "true for $hostIp <br>";
            } else {
                echo "false for $hostIp <br>";
            }
            $hostIp = "200.200.200.200";
            $op = checkServer($hostIp, $hostPort);
            if ($op) {
                echo "true for $hostIp <br>";
            } else {
                echo "false for $hostIp <br>";
            }
        ?>
    </body>
</html>
