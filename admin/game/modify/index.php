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
    if (!isset($_REQUEST['id'])) {
        die("<meta http-equiv=\"refresh\" content=\"0; url=../\" />");
    }
    $id = intval(trim($_REQUEST['id']));
    $success = false;
    
    if (isset($_REQUEST['id']) && isset($_REQUEST['name']) && isset($_REQUEST['location']) && isset($_REQUEST['startcmd']) && isset($_REQUEST['os'])) {
        $gsid = intval(trim($_REQUEST['id']));
        $gsname = trim($_REQUEST['name']);
        $gsloc = trim($_REQUEST['location']);
        $gscmd = trim($_REQUEST['startcmd']);
        $islinux = intval(trim($_REQUEST['os']));
        $query = "UPDATE swift_game SET name='$gsname', location='$gsloc', startcmd='$gscmd', islinux=$islinux WHERE id=$gsid";
        mysqli_query($mysql, $query);
        $admacc = $_SESSION['username'];
        $log = "INSERT INTO swift_logs(username, ip, action, time) VALUES ('$admacc', '$ip', 'Modified game $gsname', '" . time() . "')";
        mysqli_query($mysql, $log);
        $success = true;
    }
    $query = "SELECT * FROM swift_game WHERE id=$id";
    $result = mysqli_fetch_array(mysqli_query($mysql, $query));
    $gamename = trim($result['name']);
    $location = trim($result['location']);
    $startcmd = trim($result['startcmd']);
    $islinux = intval(trim($result['islinux']));
?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name=viewport content="width=device-width, initial-scale=1">
        <title>Modify game <?php echo $gamename; ?> | 1fx. # Server Panel</title>
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
              <li><a href="../../accounts">Accounts</a></li>
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
               if ($success) {
                   echo "<h3>Data has been updated!</h3>";
               }
               ?>
                <h3>You can change the game parameters here</h3>
                <form method="get">

                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <div class="field">
                        <label>Game name</label>
                        <input type="text" name="name" placeholder="The game name" value="<?php echo $gamename; ?>" required>
                    </div>
                    <div class="field">
                        <label>Location on on the host</label>
                        <input type="text" name="location" placeholder="The location of the game files on disk" value="<?php echo $location; ?>" required>
                    </div>
                    <div class="field">
                        <label>Startup script. Use {user} to show where the server account name should go and {port} where the gameserver port should go.</label>
                        <textarea class="ui text textarea" name="startcmd" required><?php echo $startcmd; ?></textarea>
                    </div>
                    <label for="sel">Operating system</label>
                    <br>
                    <div id="sel" class="ui selection dropdown">
                        <input type="hidden" name="os" value="<?php echo $islinux; ?>">
                        <div class="default text">Linux</div>
                        <i class="dropdown icon"></i>
                        <div class="menu">
                            <div class="item" data-value="1">Linux</div>
                            <div class="item" data-value="0">Windows</div>
                        </div>
                    </div>
                    <br><br>
                    <button type="submit" class="ui button blue">Submit the data</button>
                </form>
            </div>
        </div>
        <script>
        $('.ui.dropdown').dropdown();
        </script>
    </body>
</html>