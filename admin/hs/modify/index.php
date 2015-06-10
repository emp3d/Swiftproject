<!DOCTYPE html>
<?php
session_start();
$error = false;
$mysql = include '../../../config.php';
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
    $username = $_SESSION['username'];
    $ip = $_SESSION['ip'];
    if (!isset($_REQUEST['id'])) {
        die("<meta http-equiv=\"refresh\" content=\"0; url=../\" />");
    }
    $id = intval(trim($_REQUEST['id']));
?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name=viewport content="width=device-width, initial-scale=1">
        <title>Add a new user - Swiftproject Admin Panel</title>
        <script src="../../../semantic/jquery-2.1.4.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
        <script src="../../../semantic/semantic.js"></script>
        <script src="../../../semantic/components/dropdown.js"></script>
        <link href="../../../semantic/semantic.css" rel="stylesheet" />
        <link href="../../../semantic/components/dropdown.css" rel="stylesheet" />
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
            <a class="navbar-brand" href="#">Welcome, <?php echo $_SESSION['username']; ?></a>
          </div>
          <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
              <li><a href="../../">Home</a></li>
              <li><a href="../../gs">Gameservers</a></li>
              <li class="active"><a href="../">Host servers</a></li>
              <li><a href="../../accounts">Accounts</a></li>
              <li><a href="../../game/">Games</a></li>
              <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Logs <span class="caret"></span></a>
                  <ul class="dropdown-menu" role="menu">
                      <li><a href="../../logs/action/">Action log</a></li>
                      <li><a href="../../logs/login/">Login log</a></li>
                  </ul>
              </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="../../preferences"><i class="setting icon"></i>Preferences</a></li>
              <li><a href="../../logout">Logout</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
      </nav>
        <div class="container"> <br><br>
            <div class="ui form segment">
                <?php
                $error = false;
                $errstr = "";
                if (isset($_REQUEST['id']) && isset($_REQUEST['ip']) && isset($_REQUEST['sshport']) && isset($_REQUEST['name']) && isset($_REQUEST['user']) && isset($_REQUEST['password'])) {
                    $srvid = intval(trim($_REQUEST['id']));
                    $srvip = trim($_REQUEST['ip']);
                    $sshport = intval(trim($_REQUEST['sshport']));
                    $srvname = trim($_REQUEST['name']);
                    $srvuser = trim($_REQUEST['user']);
                    $password = trim($_REQUEST['password']);
                    if (!is_int($sshport) || $sshport == 0 || $sshport == 1) {
                        $sshport = 22;
                    }
                    $connection = ssh2_connect($srvip, $sshport);
                    ssh2_auth_password($connection, $srvuser, $password);
                    if (!$connection) {
                        $error = true;
                        $errstr = "Could not connect to the host server over SSH!";
                    }
                    $output = ssh2_exec($connection, "whoami");
                    stream_set_blocking($output, true);
                    $stream_out = ssh2_fetch_stream($output, SSH2_STREAM_STDIO);
                    $whoami = stream_get_contents($output);
                    if (!strcmp(trim($whoami), $srvuser)) {
                        $query = "UPDATE swift_hosts SET name='$srvname', ip='$srvip', sshport=$sshport, user='$srvuser', pass='$password' WHERE id=$id";
                        mysqli_query($mysql, $query);
                        echo "<h3>Server parameters updated!</h3>";
                        $admacc = $_SESSION['username'];

                        $log = "INSERT INTO swift_logs(username, ip, action, time) VALUES ('$admacc', '$ip', 'Modified host server $srvname.', '" . time() . "')";
                        mysqli_query($mysql, $log);
                    } else {
                        $error = true;
                        $errstr = "Could not connect to the host server over SSH!";
                    }
                }
                
                if ($error) {
                    echo "<h3>$errstr</h3>";
                }
                ?>
                <h3>You can change the parameters here</h3>
                <h5>If you want to change the parameters, you need to provide the password to the account you can enter below.</h5><br>
                <form method="get">
                    <?php
                    $query = "SELECT * FROM swift_hosts WHERE id=$id";
                    $result = mysqli_fetch_array(mysqli_query($mysql, $query));
                    $hostip = trim($result['ip']);
                    $name = trim($result['name']);
                    $sshport = intval(trim($result['sshport']));
                    $user = trim($result['user']);
                    ?>
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <div class="field">
                        <label>Host IP</label>
                        <input type="text" name="ip" placeholder="The host server IP" value="<?php echo $hostip; ?>" required>
                    </div>
                    <div class="field">
                        <label>SSH Port</label>
                        <input type="number" name="sshport" placeholder="The host server SSH port" value="<?php echo $sshport; ?>" required>
                    </div>
                    <div class="field">
                        <label>Name</label>
                        <input type="text" name="name" placeholder="The host server name" value="<?php echo $name; ?>" required>
                    </div>
                    <div class="field">
                        <label>User account</label>
                        <input type="text" name="user" placeholder="The host server account" value="<?php echo $user; ?>" required>
                    </div>
                    <div class="field">
                        <label>User password</label>
                        <input type="password" name="password" placeholder="The host server account password" required>
                    </div>
                    <button type="submit" class="ui button blue">Submit the data</button>
                </form>
            </div>
        </div>
    </body>
</html>