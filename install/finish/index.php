<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.a
-->
<html>
    <head>
        <meta charset="UTF-8">
        <meta name=viewport content="width=device-width, initial-scale=1">
        <title>Finish - Swiftproject</title>
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

                <div class="step">
                    <i class="settings icon"></i>
                    <div class="content">
                        <div class="title">General configuration</div>
                        <div class="description">Enter the general configuration</div>
                    </div>
                </div>
                <div class="step">
                    <i class="write icon"></i>
                        <div class="content">
                            <div class="title">Create account</div>
                        </div>
                </div>
                <div class="active step">
                    <i class="checkmark icon"></i>
                        <div class="content">
                            <div class="title">Finish</div>
                        </div>
                </div>
            </div>
            <div class="ui form segment">
                <h3>You have successfully finished installing the Swiftproject.</h3>
                <p>Please press Continue to remove the installation script. If the program fails to delete the installation folders, then please, remove them yourself. The program will not work if the installation folders exist on the server, as it would be a major security issue.</p>
                <button type="button" class="ui button block blue" onclick="location.href='../../admin/delete.php'">Continue</button>
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