<!DOCTYPE html>
<?php
session_start();
$mysql = include '../config.php';
    if (!isset($_SESSION['username']) && !isset($_SESSION['lastactive']) && !isset($_SESSION['ip']) && !isset($_SESSION['admin'])) {
        die("<meta http-equiv=\"refresh\" content=\"0; url=login\" />");
    }
    $admin = $_SESSION['admin'];
    if (!(password_verify($_SESSION['username'], $admin))) {
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
?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name=viewport content="width=device-width, initial-scale=1">
        <title>Main</title>
        <script src="../semantic/jquery-2.1.4.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
        <script src="../semantic/semantic.js"></script>
        <script src="../semantic/components/dropdown.js"></script>
        <link href="../semantic/semantic.css" rel="stylesheet" />
        <link href="../semantic/components/dropdown.css" rel="stylesheet" />
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
              <li class="active"><a href="#">Home</a></li>
              <li><a href="#">Gameservers</a></li>
              <li><a href="#">Host servers</a></li>
              <li><a href="accounts/">Accounts</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
              <li><a href="logout">Logout</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
      </nav>
        <div class="container"> <br><br>
  
  
            <table class="ui table table-hover table-bordered">
                <thead><th>Servers</th></thead>
            </table>
            
            <div class="ui form segment">
                Latest actions<br>
                <table class="ui table table-hover table-bordered">
                    <thead><th>User</th><th>IP</th><th>Action</th><th>Time</th></thead>
                    <?php
                        
                        $query = "SELECT username, ip, action, time FROM swift_logs ORDER BY id DESC LIMIT 25";
                        $result = mysqli_query($mysql, $query);
                        while ($row = mysqli_fetch_array($result)) {
                            $time = date("H:i, F j Y ", $row['time']);
                            echo "<tr><td>" . $row['username'] . "</td><td>" . $row['ip'] . "</td><td>" . $row['action'] . "</td><td>$time</td></tr>";
                        }
                        
                    
                    ?>
            </table>
            </div>
        </div>
  
<script>
$(".dropdown")
  .dropdown({
    transition: 'horizontal drop'
  });
;
</script>
    </body>
</html>