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
        <title>Add a new user - Swiftproject Admin Panel</title>
        <script src="../../../semantic/jquery-2.1.4.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
        <script src="../../../semantic/semantic.js"></script>
        <script src="../../../semantic/components/dropdown.js"></script>
        <link href="../../../semantic/semantic.css" rel="stylesheet" />
        <link href="../../../semantic/components/dropdown.css" rel="stylesheet" />
        <link href="../../../bootstrap/fileinput/css/fileinput.css" rel="stylesheet" />
        <script src="../../../bootstrap/fileinput/js/fileinput.js"></script>
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
        <?php
        $message = "";
        if (!isset($_GET['id'])) {
            die("<meta http-equiv=\"refresh\" content=\"0; url=../\" />");
        }
        $id = intval(trim($_GET['id']));
        if (!$mysql) {
            die($mysql);
        }
       
        $query = "SELECT account, password, host_id, name from swift_servers WHERE id=$id";
        $result = mysqli_query($mysql, $query);
        if (!$result) {
            die(mysqli_error($mysql));
        }
        $serverdata = mysqli_fetch_array($result);
        $account = $serverdata['account'];
        $password = $serverdata['password'];
        $host_id = $serverdata['host_id'];
        $srvname = $serverdata['name'];
        $query2 = "SELECT ip, sshport FROM swift_hosts WHERE id=$host_id";
        $hostdata = mysqli_fetch_array(mysqli_query($mysql, $query2));
        $hostip = $hostdata['ip'];
        $sshport = intval(trim($hostdata['sshport']));
        $connection = ssh2_connect($hostip, $sshport);
        ssh2_auth_password($connection, $account, $password);
        if (!$connection) {
            $message = "SSH failed.";
        }
        if (isset($_FILES['modso'])) {
            $error = false;
            $target_dir = "tmp/";
            $target_file = $target_dir . basename($_FILES['modso']['name']);
            $filetype = pathinfo($target_file, PATHINFO_EXTENSION);
            
            if ($filetype != "so" && $filetype != "gz" && $filetype != "zip") {
                $message = "Only .so, .tar.gz or .zip files are allowed!";
                $error = true;
            }
            if ((move_uploaded_file($_FILES['modso']['tmp_name'], $target_file)) && !$error) {
                $message = "File uploaded";
            } else {
                $message = "File not uploaded.";
            }
            //file at /admin/gs/upload/tmp
            $path = dirname(__FILE__) . "/$target_file";
            $command = "cp $path ~/1fx/";
            ssh2_exec($connection, $command);
            
        }
        
        ?>
        <div class="container"><br><br>
            
            <div class="ui form segment"><?php if ($message !== "") {echo "<h2>$message</h2>";}?><h2>Upload a new mod to a server called <?php echo $srvname; ?></h2>
        <form method="post" enctype="multipart/form-data">
            <div class="ui input">
                <input id="fileinput" type="file" class="file" name="modso" accept=".so, .tar.gz, .zip" required></div>
            <div id="errorBlock" class="help-block"></div>
            <input type="hidden" name="id" value="<?php echo $id; ?>"><br><br>
        </form></div>
        </div>
        <script>
            $("#fileinput").fileinput({
                showPreview: false,
                allowedFileExtensions: ["zip", "gz", "so"],
                elErrorContainer: "#errorBlock"
                
            });
        </script>
    </body>
</html>
