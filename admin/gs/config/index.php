<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->

<?php
    session_start();
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

            if (isset($_POST['config'])) {
                $cfgfile = $_POST['config'];
                $file = "ftp://$acc:$pass@$srv/1fx/admin.log";
                $overwrite = array('ftp'=>array('overwrite'=> true));
                $context = stream_context_create($overwrite);
                file_put_contents($file, $cfgfile, false, $context);
            }

            $file = "ftp://$acc:$pass@$srv/1fx/admin.log";
            $fp = fopen($file, "r");
            $filecontents = "";
            while ($o = fgets($fp)) {
                $filecontents .= $o;
            }
            fclose($fp);
            ?>
        <br><br>
        <div class="container">
            <div class="ui form segment">
                <form method="post">
                    <textarea name="config" class="ui text textarea"><?php echo $filecontents; ?></textarea>
                    <button type="submit">nep</button>
                </form>
            </div>
        </div>
    </body>
</html>
