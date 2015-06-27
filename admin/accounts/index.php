<!DOCTYPE html>
<?php
session_start();
$mysql = include '../../config.php';
    if (!isset($_SESSION['username']) || !isset($_SESSION['lastactive']) || !isset($_SESSION['ip']) || !isset($_SESSION['admin'])) {
        die("<meta http-equiv=\"refresh\" content=\"0; url=../login\" />");
    }
    $admin = $_SESSION['admin'];
    if (!(password_verify($_SESSION['username'], $admin))) {
        die("<meta http-equiv=\"refresh\" content=\"0; url=../login\" />");
    }
    $lastactive = $_SESSION['lastactive'];
    $time = time();
    if ($time >= $lastactive + 600) { //10 minutoo
        session_destroy();
        die("<meta http-equiv=\"refresh\" content=\"0; url=../login/?r=e\" />");
    } else {
        $_SESSION['lastactive'] = time();
    }
    $admuser = $_SESSION['username'];
    $ip = $_SESSION['ip'];
    $error2 = false;
    if (isset($_REQUEST['disable']) && isset($_REQUEST['id'])) {
        $query = "";
        $isadmin = intval(trim($_REQUEST['disable'])) == 1? true:false;
        $accid = intval(trim($_REQUEST['id']));
        if ($isadmin) {
            
            //check that should we do it (am I the last admin?)
            $checkActive = "SELECT Count(*) AS activeAdmins FROM swift_admin WHERE active=1";
            $result = mysqli_fetch_array(mysqli_query($mysql, $checkActive));
            $activeAdmins = intval(trim($result['activeAdmins']));
            if ($activeAdmins <= 1) {
                $error2 = true;
            } else {
                $query = "UPDATE swift_admin SET active=0 WHERE id=$accid";
                $accquery = "SELECT username FROM swift_admin WHERE id=$accid";
                $acc = mysqli_fetch_array(mysqli_query($mysql, $accquery));
                $acc2 = $acc['username'];
                $log = "INSERT INTO swift_logs(username, ip, action, time) VALUES ('$admuser', '$ip', 'Disabled $acc2\'s admin account.', '" . time() . "')";
                mysqli_query($mysql, $log);
            }
        } else {
            $query = "UPDATE swift_users SET active=0 WHERE id=$accid";
            $accquery = "SELECT username FROM swift_users WHERE id=$accid";
            $acc = mysqli_fetch_array(mysqli_query($mysql, $accquery));
            $acc2 = $acc['username'];
            $log = "INSERT INTO swift_logs(username, ip, action, time) VALUES ('$admuser', '$ip', 'Disabled $acc2\'s account.', '" . time() . "')";
            mysqli_query($mysql, $log);
        }
        if (!$error2) {
            mysqli_query($mysql, $query);
        }
    } else if (isset($_REQUEST['enable']) && isset($_REQUEST['id'])) {
        $query = "";
        $isadmin = intval(trim($_REQUEST['enable'])) == 1? true:false;
        $accid = intval(trim($_REQUEST['id']));
        if ($isadmin) {
            $query = "UPDATE swift_admin SET active=1 WHERE id=$accid";
            $accquery = "SELECT username FROM swift_admin WHERE id=$accid";
            $acc = mysqli_fetch_array(mysqli_query($mysql, $accquery));
            $acc2 = $acc['username'];
            $log = "INSERT INTO swift_logs(username, ip, action, time) VALUES ('$admuser', '$ip', 'Enabled $acc2\'s admin account.', '" . time() . "')";
            mysqli_query($mysql, $log);
        } else {
            $query = "UPDATE swift_users SET active=1 WHERE id=$accid";
            $accquery = "SELECT username FROM swift_users WHERE id=$accid";
            $acc = mysqli_fetch_array(mysqli_query($mysql, $accquery));
            $acc2 = $acc['username'];
            $log = "INSERT INTO swift_logs(username, ip, action, time) VALUES ('$admuser', '$ip', 'Enabled $acc2\'s account.', '" . time() . "')";
            mysqli_query($mysql, $log);
        }
        mysqli_query($mysql, $query);
    }
?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name=viewport content="width=device-width, initial-scale=1">
        <title>Accounts | 1fx. # Server Panel</title>
        <script src="../../semantic/jquery-2.1.4.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
        <script src="../../semantic/semantic.js"></script>
        <script src="../../semantic/components/dropdown.js"></script>
        <link href="../../semantic/semantic.css" rel="stylesheet" />
        <link href="../../semantic/components/dropdown.css" rel="stylesheet" />
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
              <li><a href="../">Home</a></li>
              <li><a href="../gs">Gameservers</a></li>
              <li><a href="../hs">Host servers</a></li>
              <li class="active"><a href="#">Accounts</a></li>
              <li><a href="../game/">Games</a></li>
              <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Logs <span class="caret"></span></a>
                  <ul class="dropdown-menu" role="menu">
                      <li><a href="../logs/action/">Action log</a></li>
                      <li><a href="../logs/login/">Login log</a></li>
                  </ul>
              </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="../preferences"><i class="setting icon"></i>Preferences</a></li>
              <li><a href="../logout">Logout</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
      </nav>
        <div class="container"> <br><br>
        <div class="ui form segment">
            <?php
            if ($error2) {
                echo "<h2>You cannot disable the final administrator account as it would leave the panel without any administrative rights!</h2>";
            }
            
            ?>
            <h3>Normal accounts</h3>
            <div class="table-responsive">
            <table class="table table-hover table-bordered">
                
                <thead><th>Username</th><th>Options</th></thead>
                <?php
                $query = "SELECT id, username, active FROM swift_users ORDER BY id ASC";
                $result = mysqli_query($mysql, $query);
                while ($row = mysqli_fetch_array($result)) {
                    $id = trim($row['id']);
                    $isactive = intval(trim($row['active'])) == 1? true:false;
                    $delorenable = "";
                    if ($isactive) {
                        $delorenable = "<i class=\"remove icon\" title=\"Disable user\" style=\"cursor:pointer; color:red;\" onclick=\"disableAcc(0, $id, '" . $row['username'] . "');\"></i>";
                    } else {
                        $delorenable = "<i class=\"checkmark icon\" title=\"Enable user\" style=\"cursor:pointer; color:green;\" onclick=\"enableAcc(0, $id, '" . $row['username'] . "');\"></i>";
                    }
                    echo "<tr><td>" . $row['username'] . "</td><td><center><i class=\"settings icon\" title=\"Modify user\" style=\"cursor:pointer;\" onclick=\"location.href='modify/?id=$id&admin=0';\"></i> $delorenable </center></td>";
                }
                
                ?>
            </table></div>
            <h3>Administrator accounts</h3><div class="table-responsive">
            <table class="table table-hover table-bordered">
                
                <thead><th>Username</th><th>Options</th></thead>
                <?php
                $query = "SELECT id, username, active FROM swift_admin ORDER BY id ASC";
                $result = mysqli_query($mysql, $query);
                while ($row = mysqli_fetch_array($result)) {
                    $id = trim($row['id']);
                    $isactive = intval(trim($row['active'])) == 1? true:false;
                    $delorenable = "";
                    if ($isactive) {
                        $delorenable = "<i class=\"remove icon\" title=\"Disable user\" style=\"cursor:pointer; color:red;\" onclick=\"disableAcc(1, $id, '" . $row['username'] . "');\"></i>";
                    } else {
                        $delorenable = "<i class=\"checkmark icon\" title=\"Enable user\" style=\"cursor:pointer; color:green;\" onclick=\"enableAcc(1, $id, '" . $row['username'] . "');\"></i>";
                    }
                    echo "<tr><td>" . $row['username'] . "</td><td><center><i class=\"settings icon\" title=\"Modify user\" style=\"cursor:pointer;\" onclick=\"location.href='modify/?id=$id&admin=1';\"></i> $delorenable </center></td></tr>";
                }
                
                ?>
            </table></div><br><br>
            <center><button type="button" onclick="location.href='new'" class="ui button blue">Add a new user</button></center>
            
            <h3>Latest actions</h3><br><div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead><th>User</th><th>IP</th><th>Action</th><th>Time</th></thead><tbody>
                    <?php
                        
                        $query = "SELECT username, ip, action, time FROM swift_logs ORDER BY id DESC LIMIT 25";
                        $result = mysqli_query($mysql, $query);
                        while ($row = mysqli_fetch_array($result)) {
                            $time = date("H:i, F j Y ", $row['time']);
                            echo "<tr><td>" . $row['username'] . "</td><td>" . $row['ip'] . "</td><td>" . $row['action'] . "</td><td>$time</td></tr>";
                        }
                        
                    
                    ?>
                </tbody>
                </table></div>
            </div>
            
        </div>
  
<script>
jQuery('ul.nav li.dropdown').hover(function() {
 jQuery(this).find('.dropdown-menu').stop(true, true).delay(200).fadeIn();
}, function() {
 jQuery(this).find('.dropdown-menu').stop(true, true).delay(200).fadeOut();
});

function disableAcc(isAdmin, id, acc) {
    var x = confirm("Are you sure you want to disable account " + acc);
    if (x) {
        location.href = "?disable=" + isAdmin + "&id=" + id;
    }
}

function enableAcc(isAdmin, id, acc) {
    var x = confirm("Are you sure you want to enable account " + acc);
    if (x) {
        location.href = "?enable=" + isAdmin + "&id=" + id;
    }
}
</script>
    </body>
</html>