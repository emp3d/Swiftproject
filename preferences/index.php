<!DOCTYPE html>
<?php
session_start();
$mysql = include '../config.php';
//user lastactive ip acc
    if (!isset($_SESSION['user']) || !isset($_SESSION['lastactive']) || !isset($_SESSION['ip']) || !isset($_SESSION['acc'])) {
        die("<meta http-equiv=\"refresh\" content=\"0; url=../login\" />");
    }
    $admin = $_SESSION['acc'];
    if (!(password_verify($_SESSION['user'], $admin))) {
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
    $username = $_SESSION['user'];
    $ip = $_SESSION['ip'];
?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name=viewport content="width=device-width, initial-scale=1">
        <title>Preferences | 1fx. # Server Panel</title>
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
            <a class="navbar-brand" href="#">Welcome, <?php echo $_SESSION['user']; ?></a>
          </div>
          <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
              <li><a href="../">Home</a></li>
              <li><a href="../gs">Gameservers</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li class="active"><a href="#"><i class="setting icon"></i>Preferences</a></li>
              <li><a href="../logout">Logout</a></li>
            </ul>
          </div>
        </div><!--/.container-fluid -->
      </nav>
        <div class="container"> <br><br>
  <div class="ui form segment">
      <?php
        if (isset($_REQUEST['old']) && isset($_REQUEST['new']) && isset($_REQUEST['confirm'])) {
            $oldpass = trim($_REQUEST['old']);
            $newpass = trim($_REQUEST['new']);
            $confirm = trim($_REQUEST['confirm']);
            if (!strcmp($newpass, $confirm)) {
                $query = "SELECT password FROM swift_users WHERE username='$username'";
                $pwd = trim(mysqli_fetch_array(mysqli_query($mysql, $query))['password']);
                if (password_verify($oldpass, $pwd)) {
                    $password = password_hash($newpass, PASSWORD_DEFAULT);
                    $query = "UPDATE swift_users SET password='$password' WHERE username='$username'";
                    mysqli_query($mysql, $query);
                    echo "<h2>Password changed!</h2>";
                } else {
                    echo "<h2 style=\"color:red;\">The password which you set to Current password didn't match with your password.</h2>";
                }
                
            } else {
                echo "<h2 style=\"color:red;\">The new password and confirm password didn't match!</h2>";
            }
        }
      
      ?>
      <form method="post" id="pwchange">
            <div class="field">
                <label for="current">Current password</label>
                <input id="current" placeholder="Type in your current password" type="password" name="old" required >
            </div>
          <div class="field">
                <label for="new">New password</label>
                <input id="new" placeholder="Type in the new password" type="password" name="new" required >
            </div>
          <div class="field">
                <label for="confirm">Confirm new password</label>
                <input id="confirm" placeholder="Type in the new password to confirm it" type="password" name="confirm" required >
            </div>
          <button type="submit" class="ui button blue">Submit the data</button>
      </form>
  </div>
        </div>
    </body>
</html>