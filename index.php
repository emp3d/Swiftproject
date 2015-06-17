<!DOCTYPE html>
<?php
session_start();
$mysql = include 'config.php';
include 'admin/gs/options/server.php';
    if (!isset($_SESSION['user']) || !isset($_SESSION['lastactive']) || !isset($_SESSION['ip']) || !isset($_SESSION['acc'])) {
        die("<meta http-equiv=\"refresh\" content=\"0; url=login\" />");
    }
    
    $admin = $_SESSION['acc'];
    if (!(password_verify($_SESSION['user'], $admin))) {
        die("<meta http-equiv=\"refresh\" content=\"0; url=login\" />");
    }
    $lastactive = $_SESSION['lastactive'];
    $time = time();
    if ($time >= $lastactive + 600) { //10 minutoo
        session_destroy();
        die("<meta http-equiv=\"refresh\" content=\"0; url=login/?r=e\" />");
    } else {
        $_SESSION['lastactive'] = time();
    }
    $user = $_SESSION['user'];
    $ip = $_SESSION['ip'];
    
    $action = false;
    $msg = "";
    if (isset($_REQUEST['reboot'])) {
        $action = true;
        $msg = "Server has been rebooted.";
        $id = intval(trim($_REQUEST['reboot']));
        
        $query = "SELECT swift_servers.account AS account, swift_servers.password AS accpass, swift_hosts.ip AS hostIp, swift_hosts.sshport AS sshport FROM swift_servers, swift_hosts WHERE swift_servers.host_id=swift_hosts.id AND swift_servers.id='$id'";
        $result = mysqli_fetch_array(mysqli_query($mysql, $query));
        $hostIp = trim($result['hostIp']);
        $sshport = intval(trim($result['sshport']));
        $account = trim($result['account']);
        $accpass = trim($result['accpass']);
        stopServer($hostIp, $sshport, $account, $accpass);sleep(3);
        $srvnamequery = "SELECT name FROM swift_servers WHERE id=$id";
        $result = mysqli_fetch_array(mysqli_query($mysql, $srvnamequery));
        $srvname = $result['name'];
        $admacc = $_SESSION['user'];
    
        $log = "INSERT INTO swift_logs(username, ip, action, time) VALUES ('$admacc', '$ip', 'Restarted server $srvname.', '" . time() . "')";
        mysqli_query($mysql, $log);
    } else if (isset($_REQUEST['start'])) {
        $action = true;
        $msg = "Server has been started.";
        $id = intval(trim($_REQUEST['start']));
        $query = "UPDATE swift_servers SET active=1 WHERE id=$id";
        mysqli_query($mysql, $query);sleep(3);
        $srvnamequery = "SELECT name FROM swift_servers WHERE id=$id";
        $result = mysqli_fetch_array(mysqli_query($mysql, $srvnamequery));
        $srvname = $result['name'];
        $admacc = $_SESSION['user'];
    
        $log = "INSERT INTO swift_logs(username, ip, action, time) VALUES ('$admacc', '$ip', 'Started server $srvname.', '" . time() . "')";
        mysqli_query($mysql, $log);
    } else if (isset($_REQUEST['stop'])) {
        $action = true;
        $msg = "Server has been stopped.";
        $id = intval(trim($_REQUEST['stop']));
        $query = "UPDATE swift_servers SET active=0 WHERE id=$id";
        mysqli_query($mysql, $query);
        $query = "SELECT swift_servers.account AS account, swift_servers.password AS accpass, swift_hosts.ip AS hostIp, swift_hosts.sshport AS sshport FROM swift_servers, swift_hosts WHERE swift_servers.host_id=swift_hosts.id AND swift_servers.id='$id'";
        $result = mysqli_fetch_array(mysqli_query($mysql, $query));
        $hostIp = trim($result['hostIp']);
        $sshport = intval(trim($result['sshport']));
        $account = trim($result['account']);
        $accpass = trim($result['accpass']);
        stopServer($hostIp, $sshport, $account, $accpass);sleep(3);
        $srvnamequery = "SELECT name FROM swift_servers WHERE id=$id";
        $result = mysqli_fetch_array(mysqli_query($mysql, $srvnamequery));
        $srvname = $result['name'];
        $admacc = $_SESSION['user'];
    
        $log = "INSERT INTO swift_logs(username, ip, action, time) VALUES ('$admacc', '$ip', 'Stopped server $srvname.', '" . time() . "')";
        mysqli_query($mysql, $log);
    }
    
    $query = "SELECT swift_servers.id AS srvId, swift_servers.script AS startcmd, swift_servers.active AS isactive, swift_servers.port AS port, swift_hosts.ip AS ip, swift_hosts.sshport AS sshport, swift_servers.account AS acc, swift_servers.password AS pwd, swift_servers.name AS name, swift_users.username AS user, swift_hosts.name AS hostname FROM swift_servers, swift_users, swift_hosts WHERE swift_servers.owner_id=swift_users.id AND swift_servers.host_id=swift_hosts.id ORDER BY swift_servers.id ASC";
    $result = mysqli_query($mysql, $query);
    $data = false;


    while ($row = mysqli_fetch_array($result)) { 
        $active = intval(trim($row['isactive'])) == 1 ? true:false;
        $acc = $row['acc'];
        $pwd = $row['pwd'];
        $name = $row['name'];
        $owner = $row['user'];
        $hostname = $row['hostname'];
        $port = $row['port'];
        $hostip = $row['ip'];
        $srvId = $row['srvId'];
        $sshport = $row['sshport'];

        $startcmd = trim($row['startcmd']);
        $startcmd = str_replace("{port}", $port, $startcmd);
        $data = true;
        $task = "";
        if ($active) {
            //Before showing everything to the user, parse ALL servers which are active (not stopped) and check that are they running.
            if (!checkStatus($hostip, $sshport, $acc, $pwd)) {
                startServer($hostip, $sshport, $acc, $pwd, $startcmd);
            } else if (!checkServer($hostip, $port)) {
                restartServer($hostip, $sshport, $acc, $pwd, $startcmd);
            }
        }
    }
    $id = intval(trim($_SESSION['accid']));
?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name=viewport content="width=device-width, initial-scale=1">
        <title>Main</title>
        <script src="semantic/jquery-2.1.4.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
        <script src="semantic/semantic.js"></script>
        <script src="semantic/components/dropdown.js"></script>
        <link href="semantic/semantic.css" rel="stylesheet" />
        <link href="semantic/components/dropdown.css" rel="stylesheet" />
    </head>
    <body>
        <nav class="navbar navbar-default">
        <div class="container-fluid">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Welcome, <?php echo $_SESSION['user']; ?></a>
          </div>
          <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
              <li class="active"><a href="#">Home</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="preferences"><i class="setting icon"></i>Preferences</a></li>
              <li><a href="logout">Logout</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
      </nav>
        <div class="container"> <br><br>
            <div class="ui form segment">
                <?php
                    if ($action) {
                        echo "<h3>$msg</h3>";
                    }
                ?>
                <h4>Your servers</h4>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <th>Status</th>
                            <th>Name</th>
                            <th>IP</th>
                            <th>Port</th>
                            <th>Account</th>
                            <th>Password</th>
                            <th>Options</th>
                        </thead>
                        <?php
                            $query = "SELECT host_id, id, name, port, account, password, active FROM swift_servers WHERE owner_id=$id ORDER BY id";
                            //die($query);
                            $result = mysqli_query($mysql, $query);
                            $output = false;
                            while ($row = mysqli_fetch_array($result)) {
                                $hostid = intval(trim($row['host_id']));
                                $hostIPquery = "SELECT ip FROM swift_hosts WHERE id=$hostid";
                                $hostip = trim(mysqli_fetch_array(mysqli_query($mysql, $hostIPquery))['ip']);
                                $output = true;
                                $name = $row['name'];
                                $port = $row['port'];
                                $account = $row['account'];
                                $password = $row['password'];
                                $srvId = intval(trim($row['id']));
                                $active = intval(trim($row['active'])) == 1? true:false;
                                $status = "Stopped";
                                $task = "<i class=\"play icon\" title=\"Start the server\" onclick=\"location.href='?start=$srvId'\" style=\"cursor:pointer;color:blue;\"></i><i class=\"cloud upload icon\" style=\"cursor:pointer;\" title=\"Update the 1fx. Mod on this server\" onclick=\"location.href='update/?id=$srvId'\"></i>";
                                if ($active) {
                                    $status = "Running";
                                    $task = "<i class=\"stop icon\" title=\"Stop the server\" onclick=\"location.href='?stop=$srvId';\" style=\"cursor:pointer;color:red;\"></i> <i class=\"refresh icon\" title=\"Restart the server\" style=\"cursor:pointer;color:green;\" onclick=\"location.href='?reboot=$srvId'\"></i><i class=\"cloud upload icon\" style=\"cursor:pointer;\" title=\"Update the 1fx. Mod on this server\" onclick=\"location.href='update/?id=$srvId'\"></i>";
                                }
                                echo "<tr><td>$status</td><td>$name</td><td>$hostip</td><td>$port</td><td>$account</td><td>$password</td><td><center>$task</center></td></tr>";
                            }
                            if (!$output) {
                                echo "<tr class=\"no-records-found\"><td colspan=\"7\">No records found.</td></tr>";
                            }
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </body>
</html>
