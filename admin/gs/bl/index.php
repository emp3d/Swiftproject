<!DOCTYPE html>
<?php
    session_start();
    $error = false;
    $mysql = include '../../../config.php';
    include '../options/server.php';
    if (!isset($_SESSION['username']) || !isset($_SESSION['lastactive']) || !isset($_SESSION['ip']) || !isset($_SESSION['admin'])) {
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
    if (!(isset($_GET['srvid']))) {
        die("<meta http-equiv=\"refresh\" content=\"0; url=../\" />");
    }
    $srvid = trim($_GET['srvid']);
    function stripColors ($quoi) {
        $result = $quoi;
        $result = preg_replace ("/\^x....../i", "", $result); // remove OSP's colors (^Xrrggbb)
        $result = preg_replace ("/\^./", "", $result); // remove Q3 colors (^2) or OSP control (^N, ^B etc..)
        $result = preg_replace ("/</", "&lt;", $result); // convert < into &lt; for proper display
        return $result;
    }
    $query = "SELECT swift_servers.name AS srvname, swift_servers.port AS srvport, swift_servers.account AS srvacc, swift_hosts.ip AS hostip, swift_hosts.sshport AS sshport, swift_hosts.user AS hostuser, swift_hosts.pass AS hostpwd FROM swift_servers, swift_hosts WHERE swift_servers.id=$srvid AND swift_servers.active=1 AND swift_servers.host_id=swift_hosts.id";
    $row = mysqli_fetch_array(mysqli_query($mysql, $query));
    $srvname = $row['srvname'];
    $srvacc = trim($row['srvacc']);
    $srvport = intval(trim($row['srvport']));
    $hostip = trim($row['hostip']);
    $sshport = intval(trim($row['sshport']));
    $hostuser = trim($row['hostuser']);
    $hostpwd = trim($row['hostpwd']);
    $connection = ssh2_connect($hostip, $sshport);
    ssh2_auth_password($connection, $hostuser, $hostpwd);
    $cmd = "less /home/$srvacc/1fx/Config.cfg | grep 'seta rconpassword' | grep -o '\"[A-Za-z0-9]*\"' | sed \"s/\\\"//g\"";
    $stream = ssh2_exec($connection, $cmd);
    stream_set_blocking($stream, true);
    $output = fgets($stream);
    function startsWith($haystack, $needle) {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }
    if (!$output) {
        die("Couldn't read the Configuration file (or didn't find the RCONPassword from it).<br>Command - $cmd <br>");
    }
    $output = trim($output);
    $conn = fsockopen("udp://$hostip", $srvport);
    if (!$conn) {
        die("Server with ip $hostip and port $srvport can't be queried.");
    }
    socket_set_timeout($conn, 2);
    
    $maxplayers = 0;
    $currentClients = 0;
    
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
              <li class="active"><a href="../">Gameservers</a></li>
              <li><a href="../../hs">Host servers</a></li>
              <li><a href="../../accounts/">Accounts</a></li>
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
                <div class="table-responsive">
                    <table class="ui table table-bordered table-hover">
                        <tr><td>
                        <?php 
                            fputs($conn, "\xFF\xFF\xFF\xFFrcon $output banlist");
                            $i = 0;
                            while ($o = fgets($conn)) {
                                $o = str_replace("\xFF\xFF\xFF\xFFprint", "", $o);
                                $o = trim($o);
                                if (strlen($o) == 0) {
                                    continue;
                                }
                                $o = stripColors($o);
                                //$o = htmlentities($o);
                                //if ($i > 9 && startsWith($o, "[0$i]")) {
                                  //  $out = preg_split("/\s+/", $o);
                                    
                                   // print_r($out);
                               /* } else if ($i > 99 && startsWith($o, "[$i]")) {
                                    $out = preg_split("/\s+/", $o);
                                    print_r($out);
                                } else if (startsWith($o, "[00$i]")) {
                                    $out = preg_split("/\s+/", $o);
                                    print_r($out);
                                }*/
                                echo "$o<br>";
                            }
                            
                        ?>
                            </td></tr>
                    </table>
                    <br><br>
                    
                </div>
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