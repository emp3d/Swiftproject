<!DOCTYPE html>
<?php
session_start();
$mysql = include '../config.php';
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
    $idquery = "SELECT id FROM swift_users WHERE username='$username'";
    $ownerid = intval(trim(mysqli_fetch_array(mysqli_query($mysql, $idquery))[id]));
    $message = "";
        if (!isset($_GET['id'])) {
            die("<meta http-equiv=\"refresh\" content=\"0; url=../\" />");
        }
        $id = intval(trim($_GET['id']));
        if (!$mysql) {
            die($mysql);
        }
       
        $query = "SELECT account, password, host_id, name from swift_servers WHERE id=$id AND owner_id=$ownerid";
        
        $result = mysqli_query($mysql, $query);
        if (!$result) {
            die(mysqli_error($mysql));
        }
        $serverdata = mysqli_fetch_array($result);
        if (!$serverdata) {
            $time = time();
            $log = "INSERT INTO swift_logs (username, ip, action, time) VALUES ('$username', '$ip', 'Tried to update the mod on a server he or she did not own (server id - $id).', '$time')";
            mysqli_query($mysql, $log);
            die("<meta http-equiv=\"refresh\" content=\"0; url=../\" />");
        }
        $account = $serverdata['account'];
        $password = $serverdata['password'];
        $host_id = $serverdata['host_id'];
        $srvname = $serverdata['name'];
        $query2 = "SELECT ip, sshport FROM swift_hosts WHERE id=$host_id";
        $hostdata = mysqli_fetch_array(mysqli_query($mysql, $query2));
        $hostip = $hostdata['ip'];
        $sshport = intval(trim($hostdata['sshport']));
        
    ?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name=viewport content="width=device-width, initial-scale=1">
        <title>Update server <?php echo $srvname; ?> | 1fx. # Server Panel</title>
        <script src="../semantic/jquery-2.1.4.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
        <script src="../semantic/semantic.js"></script>
        <script src="../semantic/components/dropdown.js"></script>
        <link href="../semantic/semantic.css" rel="stylesheet" />
        <link href="../semantic/components/dropdown.css" rel="stylesheet" />
        <script src="../bootstrap/fileinput/js/fileinput.js"></script>
        <link href="../bootstrap/fileinput/css/fileinput.css" rel="stylesheet" />
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
              <li class="active"><a href="../">Home</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="preferences"><i class="setting icon"></i>Preferences</a></li>
              <li><a href="logout">Logout</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
      </nav>
        <?php
        
        if (isset($_FILES['modso'])) {
            $connection = ssh2_connect($hostip, $sshport);
            ssh2_auth_password($connection, $account, $password);
            if (!$connection) {
                $message = "SSH failed.";
            }
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
                $path = dirname(__FILE__) . "/$target_file";
                $command = "cp $path ~/1fx/";
                ssh2_exec($connection, $command);
            } else {
                $message = "File not uploaded.";
            }
            //file at /admin/gs/upload/tmp
            
            
        }
        
        ?>
        <div class="container"><br><br>
            
            <div class="ui form segment"><?php if ($message !== "") {echo "<h2>$message</h2>";}?><h2>Upload a new mod to a server called <?php echo $srvname; ?></h2>
                <h4>To do so, press Browse to choose the file you wish to upload. Currently supported file extensions: .so</h4><br>
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
