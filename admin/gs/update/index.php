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
        if (!isset($_GET['id'])) {
            die("ID is not set");
        }
        $id = intval(trim($_GET['id']));
        $mysql = include '../../../config.php';
        if (!$mysql) {
            die(include '../../../config.php');
        }
       
        $query = "SELECT account, password, host_id from swift_servers WHERE id=$id";
        $result = mysqli_query($mysql, $query);
        if (!$result) {
            die(mysqli_error($mysql));
        }
        $serverdata = mysqli_fetch_array($result);
        $account = $serverdata['account'];
        $password = $serverdata['password'];
        $host_id = $serverdata['host_id'];
        
        $query2 = "SELECT ip, sshport FROM swift_hosts WHERE id=$host_id";
        $hostdata = mysqli_fetch_array(mysqli_query($mysql, $query2));
        $ip = $hostdata['ip'];
        $sshport = intval(trim($hostdata['sshport']));
        $connection = ssh2_connect($ip, $sshport);
        ssh2_auth_password($connection, $account, $password);
        if (!$connection) {
            die("<br>SSH failed...");
        }
        if(isset($_FILES['modso'])) {
            echo "<br>modso isset";
            $fileType = pathinfo($target_file,PATHINFO_EXTENSION);
        }
        
        ?>
        <br>
        <form method="post" enctype="multipart/form-data">
            select 1fx mod plis<br><br>
            <input type="file" name="modso" accept=".so" required><br><br>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="submit" value="Upload mod" name="submit">
        </form>
    </body>
</html>
