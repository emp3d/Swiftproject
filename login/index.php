<!DOCTYPE html>
<?php
    session_start();
    $error = false;
$mysql = include '../config.php';
if (!($mysql instanceof mysqli)) {
            die($mysql);
        }
    include '../ip.php';
    $ip = getRealIP();
    if (isset($_REQUEST['user']) && isset($_REQUEST['pass'])) {

        authenticate($_REQUEST['user'], $_REQUEST['pass']);
    }
    
    function authenticate($user, $pass) {
        global $error, $ip, $mysql;
        $user = htmlentities($user);
        
        $user = mysqli_real_escape_string($mysql, $user);
        $query = "SELECT * FROM swift_users WHERE username COLLATE Latin1_General_CS='$user' AND active=1";
        $result = mysqli_fetch_array(mysqli_query($mysql, $query));
        if (isset($result['password'])) {
            $hash = $result['password'];
            if (password_verify($pass, $hash)) {
                $_SESSION['user'] = $user;
                $_SESSION['lastactive'] = time();
                $_SESSION['ip'] = $ip;
                $_SESSION['acc'] = password_hash($user, PASSWORD_DEFAULT);
                $_SESSION['accid'] = intval(trim($result['id']));
                die("<meta http-equiv=\"refresh\" content=\"0; url=../\" />");
            } else {
                $error = true;
            }
        } else {
            $error = true;
        }
    }
    
    if ($error) {
        $user = $_REQUEST['user'];
        $user = htmlentities($user);
        $user = mysqli_real_escape_string($mysql, $user);
        $query = "INSERT INTO swift_loginlog (user, ip, date) VALUES ('$user', '$ip', '" . time() . "')";
        mysqli_query($mysql, $query);
    }

?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name=viewport content="width=device-width, initial-scale=1">
        <title>Login - 1fx. # Server Panel</title>
        <script async src="../semantic/jquery-2.1.4.min.js"></script>
        <!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">-->
        <script async src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
        <script async src="../semantic/semantic.js"></script>
        <script async src="../semantic/components/dropdown.js"></script><!--
        <link href="../semantic/semantic.css" rel="stylesheet" />
        <link href="../semantic/components/dropdown.css" rel="stylesheet" />-->
    </head>
    <body>
        <div class="container"> 
            <br><br>
  
  <div class="ui form segment">
                <h2>Login into system</h2>
                <form method="post">
                    <div class="field">
                        <label for="user">Account name</label>
                        <input id="user" placeholder="The account name which was given to you by the administrator" type="text" name="user" required />
                    </div>
                    <div class="field">
                        <label for="pass">Account password</label>
                        <input id="pass" placeholder="The password for your account" type="password" name="pass" required />
                    </div>
                    <button type="submit" class="ui button block blue">Submit the data</button>
                </form>
            </div>

</div>
    </body>
<script>
var cb = function() {
        var l = document.createElement('link'); l.rel = 'stylesheet';
        l.href = '../semantic/semantic.css';
        var h = document.getElementsByTagName('head')[0]; h.parentNode.insertBefore(l, h);
l = document.createElement('link'); l.rel = 'stylesheet';
        l.href = '../semantic/components/dropdown.css';
        h = document.getElementsByTagName('head')[0]; h.parentNode.insertBefore(l, h);
l = document.createElement('link'); l.rel = 'stylesheet';
        l.href = '../bootstrap/bootstrap.min.css';
        h = document.getElementsByTagName('head')[0]; h.parentNode.insertBefore(l, h);
l = document.createElement('link'); l.rel = 'stylesheet';
        l.href = '../bootstrap/bootstrap-theme.min.css';
        h = document.getElementsByTagName('head')[0]; h.parentNode.insertBefore(l, h);

      };
var raf = requestAnimationFrame || mozRequestAnimationFrame ||
          webkitRequestAnimationFrame || msRequestAnimationFrame;
      if (raf) raf(cb);
      else window.addEventListener('load', cb);

</script>
</html>


