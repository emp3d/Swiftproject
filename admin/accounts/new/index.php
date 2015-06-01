<!DOCTYPE html>
<?php
session_start();
$error = false;
$mysql = include '../../../config.php';
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
    $username = $_SESSION['username'];
    $ip = $_SESSION['ip'];
    
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
              <li><a href="#">Home</a></li>
              <li><a href="#">Gameservers</a></li>
              <li><a href="#">Host servers</a></li>
              <li class="active"><a href="accounts/">Accounts</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
              <li><a href="logout">Logout</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
      </nav>
        <div class="container"> <br><br>
            <div class="ui form segment">
                <form method="get">
                    <?php
                    if (isset($_REQUEST['user']) && isset($_REQUEST['password'])) {
                        $user = $_REQUEST['user'];
                        $password = $_REQUEST['password'];
                        $pass = password_hash($password, PASSWORD_DEFAULT);
                        $isAdmin = isset($_REQUEST['isAdmin']);
                        $query = "";
                        if ($isAdmin) {
                            $query = "INSERT INTO swift_admin(username, password) VALUES('$user', '$pass')";
                        } else {
                            $query = "INSERT INTO swift_users(username, password) VALUES('$user', '$pass')";
                        }
                        $result = mysqli_query($mysql, $query);
                        if (!$result) {
                            $error = true;
                        }
                        $query = "INSERT INTO swift_logs (username, ip, action, time) VALUES('$username', '$ip', 'Created account $user', '" . time() . "')";
                        if (!$error) {
                            $result = mysqli_query($mysql, $query);
                            echo "<h2>Account $user with the password $password has been created!</h2>";
                        }
                        if ($error) {
                            echo "<h2>Account $user already exists in the database!</h2>";
                        }
                    } else {
                        echo "<h2>Fill in the fields</h2>";
                    }
                    ?>
                    <div class="field">
                        <label for="username">Username</label>
                        <input id="username" placeholder="Type in the username which you want to create" type="text" name="user" required />
                    </div>

                    <div class="field">
                        <label for="password">Password</label>
                        <input id="password" placeholder="Type in the password which you want to create" type="password" name="password" required >
                    </div>
                    <div class="ui toggle checkbox">
                        <input type="checkbox" name="isAdmin">
                        <label>Set administrative privileges</label>
                    </div>
                    <br>
                    <button type="submit" class="ui button blue">Create user</button>
                </form>
            </div>
        </div>
  
<script>
$(".dropdown")
  .dropdown({
    transition: 'horizontal drop'
  });
;
$('.ui.checkbox')
  .checkbox()
;
</script>
    </body>
</html>