<!DOCTYPE html>
<?php
session_start();
$mysql = include '../../../config.php';

$page = setPage();


    if (!isset($_SESSION['username']) && !isset($_SESSION['lastactive']) && !isset($_SESSION['ip']) && !isset($_SESSION['admin'])) {
        die("<meta http-equiv=\"refresh\" content=\"0; url=login\" />");
    }
    $admin = $_SESSION['admin'];
    if (!(password_verify($_SESSION['username'], $admin))) {
        die("<meta http-equiv=\"refresh\" content=\"0; url=login\" />");
    }
    $lastactive = $_SESSION['lastactive'];
    $time = time();
    if ($time >= $lastactive + 600) {
        session_destroy();
        die("<meta http-equiv=\"refresh\" content=\"0; url=login/?r=e\" />");
    } else {
        $_SESSION['lastactive'] = time();
    }
    
    function setPage() {
        $whereAmI = isset($_REQUEST[page])? $_REQUEST['page'] : 0;
        return ($whereAmI - 1) * 25;
    }
?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name=viewport content="width=device-width, initial-scale=1">
        <title>Main</title>
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
              <li><a href="#">Home</a></li>
              <li><a href="#">Gameservers</a></li>
              <li><a href="#">Host servers</a></li>
              <li><a href="accounts/">Accounts</a></li>
              <li class="dropdown active">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Logs <span class="caret"></span></a>
                  <ul class="dropdown-menu" role="menu">
                      <li><a href="logs/action/">Action log</a></li>
                      <li><a href="logs/login/">Login log</a></li>
                  </ul>
              </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
              <li><a href="logout">Logout</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
      </nav>
        <div class="container"> <br><br>
            
            <div class="ui form segment">
                Failed logins<br>
                <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead><th>User</th><th>IP</th><th>Time</th></thead>
                    <?php
                        $query = "SELECT Count(*) FROM swift_loginlog";
                        $result2 = mysqli_fetch_array(mysqli_query($mysql, $query));
                        $sqlPage = $result2[0] - $page;
                        $query = "SELECT user, ip, date FROM swift_loginlog WHERE id < $sqlPage ORDER BY id DESC LIMIT 25";
                        $result = mysqli_query($mysql, $query);
                        while ($row = mysqli_fetch_array($result)) {
                            $time = date("H:i, F j Y ", $row['date']);
                            echo "<tr><td>" . $row['user'] . "</td><td>" . $row['ip'] . "</td><td>$time</td></tr>";
                        }
                        $count = $result2[0] / 25;
                        $countint = $count % 25;
                    
                    ?>
            </table>
                </div>
                <div class="ui pagination menu">
                    <a class="icon item">
                        <i class="left arrow icon"></i>
                    </a>
                    <?php
                    $pagination = 0;
                    $rpage = isset($_REQUEST['page'])? $_REQUEST['page'] : 1;
                        while ($pagination <= $countint) {
                            $pagination++;
                            if ($pagination == $rpage) {
                                echo "<a class=\"active item\">$pagination</a>";
                            } else {
                                echo "<a class=\"item\" href=\"?page=$pagination\">$pagination</a>";
                            }
                        }
                            
                    ?>
                    <a class="icon item">
                        <i class="right arrow icon"></i>
                    </a>
                    </a>
                </div>
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