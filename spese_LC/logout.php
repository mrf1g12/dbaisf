<?php
    session_start();
    $_SESSION['passwordOk'] = false;

    require('https://www.ai-sf.it/dbaisf/access.php');
    header('Location: ' . 'https://www.ai-sf.it/dbaisf/index.php');
?>