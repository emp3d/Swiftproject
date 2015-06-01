<!DOCTYPE html>
<?php
    if (isset($_REQUEST['user']) && isset($_REQUEST['pass'])) {
        createAccount();
        die("<meta http-equiv=\"refresh\" content=\"0; url=../finish/\" />");
    }
    
    function createAccount() {
        $user = $_REQUEST['user'];
        $pass = $_REQUEST['pass'];
        $mysql = include '../../config.php';
        $sql1 = "CREATE TABLE swift_admin (id INTEGER NOT NULL AUTO_INCREMENT, username VARCHAR(100) NOT NULL, password VARCHAR(100) NOT NULL, PRIMARY KEY (id))";
        $sql2 = "INSERT INTO swift_admin (username, password) VALUES ('$user', '$pass')";
        $result = mysqli_query($mysql, $sql1);
        if (!$result) {
            die(mysqli_error($mysql));
        }
        
        $result2 = mysqli_query($mysql, $sql2);
        if (!$result2) {
            die(mysqli_error($mysql));
        }
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