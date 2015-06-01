<!DOCTYPE html>
<?php
    if (file_exists("../config.php")) {
        die("<meta http-equiv=\"refresh\" content=\"0; url=../\" />");
    }
?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name=viewport content="width=device-width, initial-scale=1">
        <title>Installation - Swiftproject</title>
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
        <div class="container"> 
            <br>
            <div class="ui steps">
                <div class="active step">
                    <i class="wizard icon"></i>
                    <div class="content">
                        <div class="title">Welcome</div>
                    </div>
                </div>
                <div class="disabled step">
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
                <h2>Welcome to Swiftproject!</h2>
                <p>To run this application, you need to have MySQL with an account, password and a database. Please make sure you do have them before continuing.</p>
                <a href="configuration"><button type="button" class="ui button blue block">Continue</button></a>
            </div>
        </div>
    </body>
</html>