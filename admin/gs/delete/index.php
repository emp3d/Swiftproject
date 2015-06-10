<?php
    session_start();
    $error = false;
    $mysql = include '../../../config.php';
    include '../options/server.php';
    if (!isset($_SESSION['username']) && !isset($_SESSION['lastactive']) && !isset($_SESSION['ip']) && !isset($_SESSION['admin'])) {
        die("<meta http-equiv=\"refresh\" content=\"0; url=../../login\" />");
    }
    $admin = $_SESSION['admin'];
    if (!(password_verify($_SESSION['username'], $admin))) {
        die("<meta http-equiv=\"refresh\" content=\"0; url=../../login\" />");
    }
    $lastactive = $_SESSION['lastactive'];
    $time = time();
    if ($time >= $lastactive + 600) { //10 minutoo
        session_destroy();
        die("<meta http-equiv=\"refresh\" content=\"0; url=../../login/?r=e\" />");
    } else {
        $_SESSION['lastactive'] = time();
    }
    if (!(isset($_REQUEST['id']))) {
        die("<meta http-equiv=\"refresh\" content=\"0; url=../\" />");
    }
    $username = $_SESSION['username'];
    $ip = $_SESSION['ip'];
    $id = intval(trim($_REQUEST['id']));
    
    $hostidquery = "SELECT host_id, account, password, name FROM swift_servers WHERE id=$id";
    $result = mysqli_fetch_array(mysqli_query($mysql, $hostidquery));
    $hostid = intval(trim($result['host_id']));
    $gsname = $result['name'];
    $account = trim($result['account']);
    $password = trim($result['password']);
    $hostdata = "SELECT user, pass, sshport, ip, islinux FROM swift_hosts WHERE id=$hostid";
    $result = mysqli_fetch_array(mysqli_query($mysql, $hostdata));
    $hostacc = trim($result['user']);
    $hostpass = trim($result['pass']);
    $hostip = trim($result['ip']);
    $sshport = intval(trim($result['sshport']));
    $con = ssh2_connect($hostip, $sshport);
    ssh2_auth_password($con, $hostacc, $hostpass);
    stopServer($hostip, $sshport, $account, $accpass);
    $cmd = "userdel -fr $account";
    ssh2_exec($con, $cmd);
    $query = "DELETE FROM swift_servers WHERE id=$id";
    mysqli_query($mysql, $query);
    $admacc = $_SESSION['username'];
    
    $log = "INSERT INTO swift_logs(username, ip, action, time) VALUES ('$admacc', '$ip', 'Deleted server $gsname.', '" . time() . "')";
    mysqli_query($mysql, $log);
    die("<meta http-equiv=\"refresh\" content=\"0; url=../?deleted=$id\" />");

