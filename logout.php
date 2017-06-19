<?php
    session_start();
    $_SESSION['passwordOk'] = false;

    require('access.php');
    header('Location: ' . 'index.php');
?>