<!DOCTYPE html>
<?php
    session_start();
    $error = false;
$mysql = include "../../config.php";
if (!($mysql instanceof mysqli)) {
            die($mysql);
        }
    include '../../ip.php';
    $ip = getRealIP();
    if (isset($_REQUEST['user']) && isset($_REQUEST['password'])) {

        authenticate($_REQUEST['user'], $_REQUEST['password']);
    }
    
    function authenticate($user, $pass) {
        global $error, $ip, $mysql;
        $user = htmlentities($user);
        
        
        $query = "SELECT * FROM swift_admin WHERE username='$user'";
        $result = mysqli_fetch_array(mysqli_query($mysql, $query));
        print_r($result);
        if (isset($result['password'])) {
            echo "isset";
            $hash = $result['password'];
            echo "<br>";
            echo "strlen1 - " . strlen($hash);
            echo password_verify($pass, $hash)? "yes":"no";
            echo mysqli_character_set_name($mysql);
            if (password_verify($pass, $hash)) {
                echo "tere";
                $_SESSION['username'] = $user;
                $_SESSION['lastactive'] = time();
                $_SESSION['ip'] = $ip;
                $_SESSION['admin'] = password_hash($user, PASSWORD_DEFAULT);
                print_r($_SESSION);
                die("<meta http-equiv=\"refresh\" content=\"0; url=../\" />");
            } else {
                echo "puts";
                $error = true;
            }
        } else {
            $error = true;
        }
    }
    
    if ($error) {
        $user = $_REQUEST['user'];
        $user = htmlentities($user);
        echo $user;
        $user = mysqli_real_escape_string($mysql, $user);
        $query = "INSERT INTO swift_loginlog (user, ip, date) VALUES ('$user', '$ip', '" . time() . "')";
        mysqli_query($mysql, $query);
        echo $user . "<br>" . $query;
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
        <div class="container"> <br><br>
  
  
  <div class="ui form segment">   <form method='get'>
          <?php
          if (isset($_REQUEST['r'])) {
              echo '<h2>Your session has expired, please log in again.</h2>';
          } else if ($error) {
                    echo '<h2 style="color: red;">Wrong username and/or password!</h2>';
                } else {
                    echo '<h2>Login into the panel</h2>';
                }
          ?>
      <div class="field">
        <label for="username">Username</label>
        <input id="username" placeholder="Your username" type="text" name="user" required />
      </div>

      <div class="field">
        <label for="password">Password</label>
        <input id="password" placeholder="Your password" type="password" name="password" required >
      </div>
    </div>

    <button class="ui blue button submit">Login</button></form>
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