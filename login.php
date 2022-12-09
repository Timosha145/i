<?php

if (!empty($_POST['login']) && !empty($_POST['pass'])) {
    $login = $_POST['login'];
    $pass = $_POST['pass'];
    if ($login=='admin' && $pass=='admin') {
        $_SESSION['tuvastamine'] = 'misiganes';
        header('Location: admin.php');
    }
}
?>
<header><h1>Login</h1></header>

<form action="" method="post">
    Login: <input type="text" name="login"><br>
    Password: <input type="password" name="pass"><br>
    <input type="submit" value="Logi sisse">
</form>