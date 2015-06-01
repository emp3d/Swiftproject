<!DOCTYPE html>
<?php
include '../../ip.php';
$ip = getRealIP();
    if (isset($_REQUEST['user']) && isset($_REQUEST['pass'])) {
        createAccount();
        die("<meta http-equiv=\"refresh\" content=\"0; url=../finish/\" />");
    }
    
    function createAccount() {
        global $ip;
        $user = $_REQUEST['user'];
        $pwd = $_REQUEST['pass'];
        $pass = password_hash($pwd, PASSWORD_DEFAULT);
        $mysql = include '../../config.php';
        
        $sql1 = "CREATE TABLE swift_admin (id INTEGER NOT NULL AUTO_INCREMENT, username VARCHAR(100) NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id), CONSTRAINT sw_unq UNIQUE (username))";
        $sql2 = "INSERT INTO swift_admin (username, password) VALUES ('$user', '$pass')";
        $sql3 = "CREATE TABLE swift_logs (id INTEGER NOT NULL AUTO_INCREMENT, username VARCHAR(100), ip VARCHAR(15), action VARCHAR(255), time VARCHAR(255), PRIMARY KEY(id))";
        $sql4 = "INSERT INTO swift_logs (username, ip, action, time) VALUES ('$user', '$ip', 'Installed Swiftproject!', '" . time() . "')";
        $sql5 = "CREATE TABLE swift_servers (id INTEGER NOT NULL AUTO_INCREMENT, owner_id INTEGER NOT NULL, host_id INTEGER NOT NULL, account VARCHAR(100), password VARCHAR(100), active TINYINT(1), script VARCHAR(500), PRIMARY KEY(id))";
        $sql6 = "CREATE TABLE swift_users (id INTEGER NOT NULL AUTO_INCREMENT, username VARCHAR(100) NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id), CONSTRAINT swu_unq UNIQUE (username))";
        $sql7 = "CREATE TABLE swift_hosts (id INTEGER NOT NULL AUTO_INCREMENT, ip VARCHAR(15), user VARCHAR(100), pass VARCHAR(100), PRIMARY KEY(id))";
        
        $result = mysqli_query($mysql, $sql1);
        if (!$result) {
            die(mysqli_error($mysql));
        }
        
        $result2 = mysqli_query($mysql, $sql2);
        if (!$result2) {
            die(mysqli_error($mysql));
        }
        mysqli_query($mysql, $sql3);
        
        mysqli_query($mysql, $sql4);
        mysqli_query($mysql, $sql5);
        mysqli_query($mysql, $sql6);
        mysqli_query($mysql, $sql7);
    }
?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name=viewport content="width=device-width, initial-scale=1">
        <title>Create administrator account - Swiftproject</title>
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
        <div class="container"> 
            <br>
            <div class="ui steps">

                <div class="step">
                    <i class="wizard icon"></i>
                    <div class="content">
                        <div class="title">Welcome</div>
                    </div>
                </div>

                <div class="step">
                    <i class="settings icon"></i>
                    <div class="content">
                        <div class="title">General configuration</div>
                        <div class="description">Enter the general configuration</div>
                    </div>
                </div>
                <div class="active step">
                    <i class="write icon"></i>
                        <div class="content">
                            <div class="title">Create account</div>
                        </div>
                </div>
                <div class="disabled step">
                    <i class="checkmark icon"></i>
                        <div class="content">
                            <div class="title">Finish</div>
                        </div>
                </div>
            </div>
            <div class="ui form segment">
                <h2>Create yourself an account!</h2>
                <form method="get">
                    <div class="field">
                        <label for="user">Account name</label>
                        <input id="user" placeholder="The account which you will use to administrate the application." type="text" name="user" required />
                    </div>
                    <div class="field">
                        <label for="pass">Account password</label>
                        <input id="pass" placeholder="The password for your administrative account." type="password" name="pass" required />
                    </div>
                    <button type="submit" class="ui button block blue">Submit the data</button>
                </form>
            </div>
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