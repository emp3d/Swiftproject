<!DOCTYPE html>
<?php
session_start();
$error = false;
$mysql = include '../../../config.php';
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
    $modified = false;
    if (isset($_REQUEST['username']) && isset($_REQUEST['password']) && isset($_REQUEST['id']) && isset($_REQUEST['admin'])) {
        $username = trim($_REQUEST['username']);
        $password = trim($_REQUEST['password']);
        $id = intval(trim($_REQUEST['id']));
        $admin = intval(trim($_REQUEST['admin'])) == 1? true:false;
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $query = "";
        if ($admin) {
            $query = "UPDATE swift_admin SET username='$username', password='$passwordHash' WHERE id=$id";
            $admacc = $_SESSION['username'];
            $log = "INSERT INTO swift_logs(username, ip, action, time) VALUES ('$admacc', '$ip', 'Modified admin account $username.', '" . time() . "')";
            mysqli_query($mysql, $log);
        } else {
            $query = "UPDATE swift_users SET username='$username', password='$passwordHash' WHERE id=$id";
            $admacc = $_SESSION['username'];
            $log = "INSERT INTO swift_logs(username, ip, action, time) VALUES ('$admacc', '$ip', 'Modified account $username.', '" . time() . "')";
            mysqli_query($mysql, $log);
        }
        mysqli_query($mysql, $query);
        $modified = true;
    } 
    if (!isset($_REQUEST['admin']) || !isset($_REQUEST['id'])) {
        die("<meta http-equiv=\"refresh\" content=\"0; url=../\" />");
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
              <li><a href="../../gs">Gameservers</a></li>
              <li><a href="../../hs">Host servers</a></li>
              <li class="active"><a href="../">Accounts</a></li>
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
                if ($modified) {
                    echo "<h3>Account has been modified</h3>";
                }
                
                ?>
                <h3>You can modify the account data below.</h3>
                <h4>Setting the password is not mandatory, but account field must have some data before submitting.</h4><br>
                <form method="post">
                    <?php
                        $id = intval(trim($_REQUEST['id']));
                        $admin = intval(trim($_REQUEST['admin'])) == 1? true:false;
                        $username = "";
                        $query = "";
                        if ($admin) {
                            $query = "SELECT username FROM swift_admin WHERE id=$id";
                        } else {
                            $query = "SELECT username FROM swift_users WHERE id=$id";
                        }
                        $result = mysqli_fetch_array(mysqli_query($mysql, $query));
                        $username = trim($result['username']);
                    ?>
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <input type="hidden" name="admin" value="<?php echo $_REQUEST['admin']; ?>">
                    <div class="field">
                        <label>The username</label>
                        <input type="text" name="username" placeholder="Set a new username for the user <?php echo $username; ?>" value="<?php echo $username; ?>" required>
                    </div>
                    <div class="field">
                        <label>The password</label>
                        <input type="password" name="password" placeholder="Set a new password for the user <?php echo $username; ?>">
                    </div>
                    <button type="submit" class="ui button blue">Submit the data</button>
                </form>
            </div>
        </div>
    </body>
</html>