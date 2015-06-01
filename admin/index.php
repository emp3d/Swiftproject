<!DOCTYPE html>
<?php
    $error = false;
    $ip = getRealIP();
    if (isset($_REQUEST['user']) && isset($_REQUEST['password'])) {
        authenticate($_REQUEST['user'], $_REQUEST['password']);
    }
    
    function authenticate($user, $pass) {
        global $error;
        $user = htmlentities($user);
        $pass = htmlentities($pass);
        $pass = password_hash($pass, PASSWORD_DEFAULT);
        echo $pass;
        $mysql = include "../config.php";
        if (!($mysql instanceof mysqli)) {
            die($mysql);
        }
        $query = "SELECT * FROM swift_admin WHERE username='$user' AND password='$pass'";
        $result = mysqli_fetch_array(mysqli_query($mysql, $query));
        if ($result) {
            die(print_r($result));
        } else {
            $error = true;
        }
    }
    
    function getRealIP() {
        if (!empty($_SERVER['HTTP_X_REAL_IP'])) {
            return $_SERVER['HTTP_X_REAL_IP'];
        } else if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
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
        <div class="container"> <br><br>
  
  
  <div class="ui form segment">   <form method='get'>
          <?php
          echo "Your IP - " . $ip;
                if ($error) {
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