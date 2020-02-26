<?php

require __DIR__ . '/vendor/autoload.php';

define('ROOT', __DIR__);


use app\Login;

if (isset($_POST['username'], $_POST['password'])) {
    $username = htmlspecialchars($_POST['username']);
    $result = Login::login($username, $_POST['password']);
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
            integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n"
            crossorigin="anonymous"></script>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
           integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6"
           crossorigin="anonymous"></script>
</head>
<body>
<div class="container" style="margin-top: 50px;">
    <?php if ($result) echo $result; ?>
    <div class="row">
        <div class="col-md-4 offset-md-4">
            <form method="post">

                <div class="form-group">
                    <label for="exampleInputEmail1">Email address</label>
                    <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp"
                           name="username"
                           placeholder="Enter email">
                </div>
                <div class="form-group">
                    <label for="exampleInputPassword1">Password</label>
                    <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password"
                           name="password">
                </div>
                <button type="submit" class="btn btn-primary col-md-12">Submit</button>
            </form>
        </div>
    </div>
</div>
<div class="ifo">
    <?php print("<pre>" . print_r(Login::getData(), true) . "</pre>"); ?>
</div>
</body>
</html>
