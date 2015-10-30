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
        <title>Add a new gameserver | 1fx. # Server Panel</title>
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
                    if (isset($_REQUEST['name']) && isset($_REQUEST['host']) && isset($_REQUEST['owner']) && isset($_REQUEST['game'])) {
                        $hostId = intval(trim($_REQUEST['host']));
                        $owner = intval(trim($_REQUEST['owner']));
                        $name = $_REQUEST['name'];
                        $getPort = "SELECT port FROM swift_servers WHERE host_id=$hostId ORDER BY port DESC LIMIT 1";
                        $result = mysqli_query($mysql, $getPort);
                        $row = mysqli_fetch_array($result);
                        $port = 0;
                        if ($row) {
                            $port = intval(trim($row['port'])) + 1;
                        } else {
                            $port = 20100;
                        }
                        if (isset($_REQUEST['gsport']) && strlen($_REQUEST['gsport']) > 0) {
                            $port = intval(trim($_REQUEST['gsport']));
                        }
                        $accountQuery = "SELECT account FROM swift_servers WHERE host_id=$hostId AND account LIKE 'srv%' ORDER BY id DESC LIMIT 1";
                        $result = mysqli_query($mysql, $accountQuery);
                        $row = mysqli_fetch_array($result);
                        $account = "";
                        if ($row) {
                            $getAccount = $row['account'];
                            $getAccount = intval(substr_replace($getAccount, "", 0, 3));
                            $getAccount++;
                            $account = "srv" . $getAccount;
                        } else {
                            $account = "srv1";
                        }
                        if (isset($_REQUEST['acc']) && strlen($_REQUEST['acc']) > 0) {
                            $account = trim($_REQUEST['acc']);
                        }
                        $accpass = getPassword();
                        if (isset($_REQUEST['pwd']) && strlen($_REQUEST['pwd']) > 0) {
                            $accpass = trim($_REQUEST['pwd']);
                        }
                        $hostAccPass = "SELECT user, pass, ip, sshport, islinux FROM swift_hosts WHERE id=$hostId";
                        $result = mysqli_query($mysql, $hostAccPass);
                        $row = mysqli_fetch_array($result);
                        $hostAcc = trim($row['user']);
                        $hostPass = trim($row['pass']);
                        $hostIp = trim($row['ip']);
                        $sshport = intval(trim($row['sshport']));
                        $connection = ssh2_connect($hostIp, $sshport);
                        
                        ssh2_auth_password($connection, $hostAcc, $hostPass);
                        $command1 = $command2 = "";
                        
                        $isLinux = intval(trim($row['islinux']));
                        $gameId = intval(trim($_REQUEST['game']));
                        $startupScript = "SELECT startcmd, location FROM swift_game WHERE id=$gameId";
                        $result = mysqli_fetch_array(mysqli_query($mysql, $startupScript));
                        $filesLocation = trim($result['location']);
                        $startcmd = trim($result['startcmd']);
                        //
                        $startcmd = str_replace("{user}", $account, $startcmd);
                        if ($isLinux == 1) {
                            $command1 = "useradd -m $account";
                            $command2 = "echo \"$account:$accpass\" | chpasswd";
                            $command3 = "cp -R $filesLocation/* /home/$account/";
                            $command4 = "chown -R $account /home/$account";
                            ssh2_exec($connection, $command1);
                            ssh2_exec($connection, $command2);//cmd /c mklink /d "C:\Users\Janno\test\base" "C:\Games\SoF2 - 1.00\base"
                            ssh2_exec($connection, $command3);
                            ssh2_exec($connection, $command4);
                            
                            
                        } else {
                            $command1 = "cmd /c net user /add $account $accpass";
                            $command2 = "cmd /c xcopy /s $filesLocation C:/Users/$account/";
                            ssh2_exec($connection, $command1);
                            ssh2_exec($connection, $command2);
                            //ssh2_exec($connection, $command);
                        }
                        
                        
                        
                        
                        $query = "INSERT INTO swift_servers(owner_id, host_id, account, password, script, name, port, active) VALUES('$owner', '$hostId', '$account', '$accpass', '$startcmd', '$name', '$port', '0')";
                        $log = "INSERT INTO swift_logs(username, ip, action, time) VALUES ('$username', '$ip', 'Added a new server with name $name', '" . time() . "')";
                        $result = mysqli_query($mysql, $query);
                        if (!$result) {
                            die(mysqli_error($mysql));
                        }
                        $result = mysqli_query($mysql, $log);
                        if (!$result) {
                            die(mysqli_error($mysql));
                        }
                        echo "<h2>Server $name has been added. You can start it the Gameservers page.</h2>";
                    } else {
                        echo "<h2>Put the server information below</h2>";
                    }
                
                ?>
                
                <form method="get" id="srv">
                    <div class="field">
                        <label for="name">Server name</label>
                        <input id="name" placeholder="Type in a friendly name for the server" type="text" name="name" required />
                    </div>
                    <div class="field">
                        <label for="acc">Account</label>
                        <input id="acc" placeholder="Type in the account name which you want to use (not required)" type="text" name="acc" />
                    </div>
                    <div class="field">
                        <label for="pwd">Account password</label>
                        <input id="pwd" placeholder="Type in the password of the account you specified" type="password" name="pwd" />
                    </div>
                    <div class="field">
                        <label for="gsport">Gameserver port</label>
                        <input id="gsport" placeholder="Type in your own port for the server (not required)" type="text" name="gsport" />
                    </div>
                    <label for="sel1">Host server</label>
                    <br>
                    <div id="sel1" class="ui selection dropdown"><input type="hidden" name="host">
                        <div class="default text">Server</div>
                        <i class="dropdown icon"></i>
                        <div class="menu">
                        <?php
                            $query = "SELECT id, name FROM swift_hosts";
                            $result = mysqli_query($mysql, $query);
                            $firstHostID = -1;
                            $isData = false;
                            
                            while ($row = mysqli_fetch_array($result)) {
                                $isData = true;
                                if ($firstHostID == -1) {
                                    $firstHostID = $row['id'];
                                }
                                echo "<div class=\"item\" data-value=\"" . $row['id'] . "\">" . $row['name'] . "</div>";
                            }
                        
                        ?>
                        
                        </div>
                    </div><br><br>
                    <label for="sel2">Owner</label>
                    <br>
                    <div id="sel2" class="ui selection dropdown"><input type="hidden" name="owner">
                        <div class="default text">Owner account</div>
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
                    </div>
                    <br><br>
                    <label for="sel3">Game</label>
                    <br>
                    <div id="sel3" class="ui selection dropdown"><input type="hidden" name="game">
                        <div class="default text">Select the game</div>
                        <i class="dropdown icon"></i>
                        <div class="menu">
                        <?php
                            $query = "SELECT id, name FROM swift_game";
                            $result = mysqli_query($mysql, $query);
                            $firstHostID = -1;
                            $isData = false;
                            
                            while ($row = mysqli_fetch_array($result)) {
                                $isData = true;
                                if ($firstHostID == -1) {
                                    $firstHostID = $row['id'];
                                }
                                echo "<div class=\"item\" data-value=\"" . $row['id'] . "\">" . $row['name'] . "</div>";
                            }
                        
                        ?>
                        
                        </div>
                    </div>
                    <br><br>
                    
                    <button type="submit" class="ui button blue">Add game server</button>
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
