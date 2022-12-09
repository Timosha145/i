<?php
session_start();
if (!isset($_SESSION['tuvastamine'])) {
    header('Location: linn.php');
    exit();
}
if(isset($_POST['logout'])){
    session_destroy();
    header('Location: linn.php');
    exit();
}
?>