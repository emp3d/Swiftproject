<!DOCTYPE html>
<?php
session_start();
$error = false;
$mysql = include '../../../config.php';
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
    
    
?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name=viewport content="width=device-width, initial-scale=1">
        <title>Add a new game | 1fx. # Server Panel</title>
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
              <li><a href="../../hs/">Host servers</a></li>
              <li><a href="../../accounts/">Accounts</a></li>
              <li class="active"><a href="../">Games</a></li>
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
                    if (isset($_REQUEST['game']) && isset($_REQUEST['location']) && isset($_REQUEST['startcmd']) && isset($_REQUEST['os'])) {
                        $game = $_REQUEST['game'];
                        $location = $_REQUEST['location'];
                        $cmd = $_REQUEST['startcmd'];
                        $islinux = intval($_REQUEST['os']);
                        $os = "";
                        if ($islinux == 1) {
                            $os = "Linux";
                        } else {
                            $os = "Windows";
                        }
                        $query = "INSERT INTO swift_game (name, location, startcmd, islinux) VALUES ('$game', '$location', '$cmd', '$islinux')";
                        $log = "INSERT INTO swift_logs (username, ip, action, time) VALUES ('$username', '$ip', 'Added a new game called $game for $os', '" . time() . "')";
                        $result = mysqli_query($mysql, $query);
                        if (!$result) {
                            die(mysqli_error($mysql));
                        }
                        $result = mysqli_query($mysql, $log);
                        
                        if (!$result) {
                            die(mysqli_error($mysql));
                        }
                        echo "<h2>Game $game for $os has been successfully added!</h2>";
                    } else {
                        echo "<h2>Enter the game data below</h2>";
                    }                
                ?>
                
                <form method="get" id="srv">
                    <div class="field">
                        <label for="name">Game name</label>
                        <input id="name" placeholder="Type in a friendly name for the game" type="text" name="game" required />
                    </div>
                    <div class="field">
                        <label for="location">Game location on host</label>
                        <input id="location" placeholder="Type in the location for the game files (base folder)" type="text" name="location" required />
                    </div>

                    <div class="field">
                        <label for="startcmd">Start command</label>
                        <textarea class="ui textarea" id="startcommand" placeholder="Type in the command which starts the gameserver" type="text" name="startcmd" required ></textarea>
                    </div>
                    <label for="sel">Operating system</label>
                    <br>
                    <div id="sel" class="ui selection dropdown">
                        <input type="hidden" name="os" value="1">
                        <div class="default text">Linux</div>
                        <i class="dropdown icon"></i>
                        <div class="menu">
                            <div class="item" data-value="1">Linux</div>
                            <div class="item" data-value="0">Windows</div>
                        </div>
                    </div>
                    <br><br>
                    <button type="submit" class="ui button blue">Add new game</button>
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