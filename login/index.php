<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name=viewport content="width=device-width, initial-scale=1">
        <title>Login - Swiftproject</title>
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
            <br><br>
  
  <div class="ui form segment">
                <h2>Login into system</h2>
                <form method="get">
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
<script>
$(".dropdown")
  .dropdown({
    transition: 'horizontal drop'
  });
;
</script>
    </body>
</html>