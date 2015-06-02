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
    
    function getPassword() {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $password = "";
        for ($i = 0; $i < 6; $i++) {
            $randomInt = rand(0, strlen($alphabet) - 1);
            $password .= $alphabet[$randomInt];
        }
        return $password;
    }
    
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
              <li><a href="../../accounts/">Accounts</a></li>
              <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Logs <span class="caret"></span></a>
                  <ul class="dropdown-menu" role="menu">
                      <li><a href="../../logs/action/">Action log</a></li>
                      <li><a href="../../logs/login/">Login log</a></li>
                  </ul>
              </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
              <li><a href="../../logout">Logout</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
      </nav>
        <div class="container"> <br><br>
            <div class="ui form segment">
                <?php
                    if (isset($_REQUEST['ip']) && isset($_REQUEST['user']) && isset($_REQUEST['password']) && isset($_REQUEST['os'])) {
                        $srvip = $_REQUEST['ip'];
                        $user = $_REQUEST['user'];
                        $pass = $_REQUEST['password'];
                        $os = $_REQUEST['os'];
                        //lets test SSH prior.
                        $connection = ssh2_connect($srvip, 22);
                        ssh2_auth_password($connection, $user, $pass);
                        $output = ssh2_exec($connection, "whoami");
                        stream_set_blocking($output, true);
                        $stream_out = ssh2_fetch_stream($output, SSH2_STREAM_STDIO);
                        $whoami = stream_get_contents($output);
                        if (!strcmp(trim($whoami), $user)) { 
                            $query = "INSERT INTO swift_hosts(ip, user, pass, islinux) VALUES('$srvip', '$user', '$pass', '$os')";
                            $result = mysqli_query($mysql, $query);
                            if (!$result) {
                                die(mysqli_error($mysql));
                            }
                            mysqli_query($mysql, "INSERT INTO swift_logs(username, ip, action, time) VALUES ('$username', '$ip', 'Added new server with IP $srvip', '" . time() . ")')");
                            
                            echo "<h2>Server with the IP $srvip has been added to the system!</h2>";
                        } else {
                            echo "<h2>Couldn't SSH to server with the IP $ip with account $user.</h2>";
                        }
                    } else {
                        echo "<h2>Enter the server information below</h2>";
                    }
                
                ?>
                
                <form method="get" id="srv">
                    <div class="field">
                        <label for="ip">IP</label>
                        <input id="ip" placeholder="Type in the host server IP" type="text" name="ip" required />
                    </div>
                    <div class="field">
                        <label for="username">Username</label>
                        <input id="username" placeholder="Type in the username of the host server" type="text" name="user" required />
                    </div>

                    <div class="field">
                        <label for="password">Password</label>
                        <input id="password" placeholder="Type in the password of the host server" type="password" name="password" required >
                    </div>
                    <label for="sel">Operation system</label>
                    <br>
                    <div id="sel" class="ui selection dropdown">
                        <input type="hidden" name="os" value="1">
                        <div class="default text">UNIX</div>
                        <i class="dropdown icon"></i>
                        <div class="menu">
                            <div class="item" data-value="1">UNIX</div>
                            <div class="item" data-value="0">Windows</div>
                        </div>
                    </div>
                    <br><br>
                    <button type="submit" class="ui button blue">Add host server</button>
                </form>
            </div>
        </div>
  
<script>
jQuery('ul.nav li.dropdown').hover(function() {
 jQuery(this).find('.dropdown-menu').stop(true, true).delay(200).fadeIn();
}, function() {
 jQuery(this).find('.dropdown-menu').stop(true, true).delay(200).fadeOut();
});
$('.ui.dropdown').dropdown();
</script>
    </body>
</html>