<!DOCTYPE html>
<?php
session_start();
$mysql = include '../../config.php';
    if (!isset($_SESSION['username']) && !isset($_SESSION['lastactive']) && !isset($_SESSION['ip']) && !isset($_SESSION['admin'])) {
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
?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name=viewport content="width=device-width, initial-scale=1">
        <title>Main</title>
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
              <li class="active"><a href="#">Gameservers</a></li>
              <li><a href="../hs">Host servers</a></li>
              <li><a href="../accounts/">Accounts</a></li>
              <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Logs <span class="caret"></span></a>
                  <ul class="dropdown-menu" role="menu">
                      <li><a href="../logs/action/">Action log</a></li>
                      <li><a href="../logs/login/">Login log</a></li>
                  </ul>
              </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
              <li><a href="../logout">Logout</a></li>
            </ul>
          </div>
        </div><!--/.container-fluid -->
      </nav>
        <div class="container"> <br><br>
  <div class="ui form segment">
  <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead><th>Gameserver IP</th><th>Gameserver port</th><th>Username</th><th>Password</th><th>Owner</th></thead>
                <?php
                    $query = "SELECT * FROM swift_hosts ORDER BY id ASC";
                    $result = mysqli_query($mysql, $query);
                    $data = false;
                    
                    
                    while ($row = mysqli_fetch_array($result)) { 
                        $data = true;
                        $os = $row['islinux'] == 1 ? "Linux" : "Windows";
                        echo "<tr><td>" . $row['ip'] . "</td><td>" . $row['user'] . "</td><td>$os</td><td>TODO ID - " . $row['id'] . "</td></tr>";
                    }
                    if (!$data) {
                        echo "<tr class=\"no-records-found\"><td colspan=\"4\">No records found. You can add a new host machine by clicking the Add new host button.</td></tr>";
                    }
                
                ?>
            </table>
      
  </div>
      <br>
      <button type="button" class="ui button blue" onclick="location.href='new'">Add new host</button>
  </div>
        </div>
  
<script>
jQuery('ul.nav li.dropdown').hover(function() {
 jQuery(this).find('.dropdown-menu').stop(true, true).delay(200).fadeIn();
}, function() {
 jQuery(this).find('.dropdown-menu').stop(true, true).delay(200).fadeOut();
});
</script>
    </body>
</html>