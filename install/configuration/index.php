<!DOCTYPE html>
<?php
    $error = false;
    $dbhost = "";
    $dbuser = "";
    $dbpass = "";
    $db = "";
    if (file_exists("../../config.php")) {
        die("<meta http-equiv=\"refresh\" content=\"0; url=../../\" />");
    }
    
    if (isset($_REQUEST['dbhost'])) {
        if (checkConnection()) {
            writeConfig();
            die("<meta http-equiv=\"refresh\" content=\"0; url=../create/\" />");
        } else {
            $error = true;
        }
    }
    
    function checkConnection() {
        global $db, $dbuser, $dbpass, $dbhost;
        $db = $_REQUEST['db'];
        $dbuser = $_REQUEST['dbuser'];
        $dbpass = $_REQUEST['dbpass'];
        $dbhost = $_REQUEST['dbhost'];
        $connection = mysqli_connect($dbhost, $dbuser, $dbpass, $db);
        if (!$connection) {
            return false;
        }
        return true;
        
    }
    
    function writeConfig() {
        global $db, $dbuser, $dbpass, $dbhost;
        $file = fopen("../../config.php", "w") or die("Unable to create the configuration file, please check the permissions!");
        $txt = "<?php\n\$dbhost = \"" . $dbhost . "\";\n\$dbuser = \"" . $dbuser . "\";\n\$dbpass = \"" . $dbpass . "\";\n\$db = \"" . $db . "\";\n"
                . "\n\$mysql = mysqli_connect(\$dbhost, \$dbuser, \$dbpass, \$db);\n"
                . "if (!\$mysql) {\n\treturn \"Error in config.php file. Please check that you have set the right variables.<br>\";\n}\n"
                . "return \$mysql;\n?>\n";
        
        fwrite($file, $txt);
        fclose($file);
    }
?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name=viewport content="width=device-width, initial-scale=1">
        <title>Configuration - Swiftproject</title>
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

                <div class="link step" onclick="location.href='../'">
                    <i class="wizard icon"></i>
                    <div class="content">
                        <div class="title">Welcome</div>
                    </div>
                </div>

                <div class="active step">
                    <i class="settings icon"></i>
                    <div class="content">
                        <div class="title">General configuration</div>
                        <div class="description">Enter the general configuration</div>
                    </div>
                </div>
                <div class="disabled step">
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
                <?php
                    if ($error) {
                        echo "<h2 style=\"color: red;\">Could not connect to the MySQL server, please re-check your configuration.</h2>";
                    } else {
                        echo "<h2>Please enter the information required in order to continue setting up Swiftproject</h2>";
                    }
                ?>
                
                <form method="get">
                    <div class="field">
                        <label for="dbhost">Database hostname</label>
                        <input id="dbhost" placeholder="Your database host location(ip or domain)" type="text" name="dbhost" value="<?php echo $dbhost; ?>" required />
                    </div>
                    <div class="field">
                        <label for="dbuser">Database username</label>
                        <input id="dbuser" placeholder="Your database account name" type="text" name="dbuser" value="<?php echo $dbuser; ?>" required />
                    </div>
                    <div class="field">
                        <label for="dbpass">Database username password</label>
                        <input id="dbpass" placeholder="Your database account password" type="password" name="dbpass" required />
                    </div>
                    <div class="field">
                        <label for="db">Database</label>
                        <input id="db" placeholder="The name of the database" type="text" name="db" value="<?php echo $db; ?>" required />
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