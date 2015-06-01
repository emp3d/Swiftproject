<!DOCTYPE html>
<?php
    if (!isset($_SESSION['username']) || !isset($_SESSION['timeout'])) {
        die("<meta http-equiv=\"refresh\" content=\"0; url=login\" />");
    }
?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name=viewport content="width=device-width, initial-scale=1">
        <title>Main</title>
        <script src="semantic/jquery-2.1.4.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
        <script src="semantic/semantic.js"></script>
        <script src="semantic/components/dropdown.js"></script>
        <link href="semantic/semantic.css" rel="stylesheet" />
        <link href="semantic/components/dropdown.css" rel="stylesheet" />
    </head>
    <body>
        <div class="container"> 
  <h1>Register Now</h1>
  
  <div class="ui form segment">   <form method='get'>
    <div class="two fields">
      <div class="field">
        <label for="GivenName">Given Name</label>
        <input id="GivenName" placeholder="Given Name" type="text" name='user' required />
      </div>

      <div class="field">
        <label for="Surname">Surname</label>
        <input id="Surname" placeholder="Surname" type="text" name="surname" required >
      </div>
    </div>

    <div class="field">
      <label for="Email">Email</label>
      <input id="Email" placeholder="Email" type="text">
    </div>

    <div class="field">
      <label for="Username">Username</label>
      <input id="Username" placeholder="Username" type="text">
    </div>

    <div class="field">
      <label for="Password">Password</label>
      <input id="Password" type="password">
    </div>

    <div class="field">
      <label for="PasswordConfirm">Password Confirm</label>
      <input id="PasswordConfirm" type="password">
    </div>

    <button class="ui blue button submit">Submit</button></form>
  </div>
  <div class="ui vertical menu">
    <a class="item">
      Home
    </a>
    <div class="ui left pointing dropdown link item">
      <i class="dropdown icon"></i>
      Messages
      <div class="menu">
        <div class="item">Inbox</div>
        <div class="item">Starred</div>
        <div class="item">Sent Mail</div>
        <div class="item">Drafts (143)</div>
        <div class="divider"></div>
        <div class="item">Spam (1009)</div>
        <div class="item">Trash</div>
      </div>
    </div>
    <a class="item">
      Browse
    </a>
    <a class="item">
      Help
    </a>
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
