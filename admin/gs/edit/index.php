<!DOCTYPE html>
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
    $username = $_SESSION['username'];
    $ip = $_SESSION['ip'];
    if (!(isset($_REQUEST['id'])) || trim($_REQUEST['id']) == "") {
        die("<meta http-equiv=\"refresh\" content=\"0; url=../\" />");
    }
    $id = intval(trim($_REQUEST['id']));
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
                
                <?php
                $error = false;
                $errstr = "";
                    if (isset($_REQUEST['owner']) && isset($_REQUEST['srvname']) && isset($_REQUEST['gport']) && isset($_REQUEST['script']) && isset($_REQUEST['hostid'])) {
                        $owner = intval(trim($_REQUEST['owner']));
                        $srvname = trim($_REQUEST['srvname']);
                        $gport = intval(trim($_REQUEST['gport']));
                        $script = trim($_REQUEST['script']);
                        $hostid = intval(trim($_REQUEST['hostid']));
                        $query = "UPDATE swift_servers SET name='$srvname', port='$gport', owner_id='$owner', script='$script' WHERE id=$id";
                        $checkPort = "SELECT port FROM swift_servers WHERE host_id=$hostid AND id!=$id";
                        $result = mysqli_query($mysql, $checkPort);
                        while ($row = mysqli_fetch_array($result)) {
                            $sqlPort = intval(trim($row['port']));
                            if ($sqlPort == $gport) {
                                $error = true;
                                $errstr = "The port $gport is already defined in the system for this host!";
                                break;
                            }
                        }
                        if (!$error) {
                            mysqli_query($mysql, $query);
                            $admacc = $_SESSION['username'];
    
                            $log = "INSERT INTO swift_logs(username, ip, action, time) VALUES ('$admacc', '$ip', 'Modified server $srvname.', '" . time() . "')";
                            mysqli_query($mysql, $log);
                            echo "<h2>Server has been updated!</h2>";
                        } else {
                            echo "<h2>$errstr</h2>";
                        }
                        
                    }
                
                ?>
                <form method="get" id="srv">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <?php
                    //get the server data.
                    $query = "SELECT swift_servers.name AS srvname, swift_servers.port AS srvport, swift_servers.script AS srvcmd, swift_hosts.name AS hostname, swift_hosts.id AS hostid, swift_users.username AS owner, swift_users.id AS ownerid, swift_hosts.islinux AS linux FROM swift_servers, swift_hosts, swift_users WHERE swift_servers.owner_id = swift_users.id AND swift_servers.host_id = swift_hosts.id AND swift_servers.id=$id";
                    $result = mysqli_fetch_array(mysqli_query($mysql, $query));
                    $srvname = trim($result['srvname']);
                    $srvport = intval(trim($result['srvport']));
                    $srvcmd = trim($result['srvcmd']);
                    $hostname = trim($result['hostname']);
                    $owner = trim($result['owner']);
                    $hostid = intval(trim($result['hostid']));
                    $ownerid = intval(trim($result['ownerid']));
                    $islinux = intval(trim($result['linux'])) == 1? true:false;
                    ?>
                    
                    <input type="hidden" name="hostid" value="<?php echo $hostid; ?>">
                    <label for="sel2">Owner</label>
                    <br>
                    <div id="sel2" class="ui selection dropdown">
                        <input type="hidden" name="owner" value="<?php echo $ownerid; ?>">
                        <div class="default text">Owner name</div>
                        <i class="dropdown icon"></i>
                        <div class="menu">
                        <?php
                            $query = "SELECT id, username FROM swift_users";
                            $result = mysqli_query($mysql, $query);
                            $firstHostID = -1;
                            $isData = false;
                            
                            while ($row = mysqli_fetch_array($result)) {
                                $isData = true;
                                if ($firstHostID == -1) {
                                    $firstHostID = $row['id'];
                                }
                                echo "<div class=\"item\" data-value=\"" . $row['id'] . "\">" . $row['username'] . "</div>";
                            }
                        
                        ?>
                        
                        </div>
                    </div><br><br>
                    <div class="ui field">
                        <label>Server name</label>
                        <input type="text" name="srvname" value="<?php echo $srvname; ?>" required>
                    </div>
                    
                    <label>Game port</label>
                    <input type="number" name="gport" value="<?php echo $srvport; ?>" required>
                    <br><br>
                    <label>Start command</label>
                    <br>
                    <textarea type="text" class="ui text textarea" name="script" required><?php echo $srvcmd; ?></textarea>
                    <br><br>
                    <button type="submit" class="ui button blue">Edit game server</button>
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