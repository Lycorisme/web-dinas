<?php
session_start();
unset($_SESSION['login']);
$_SESSION['login'] = null;
header('Location: view/index.php');
exit;