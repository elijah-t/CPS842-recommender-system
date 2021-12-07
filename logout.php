<?php
    function redirect($url, $permanent = false) {
        if (headers_sent() === false) header('Location: ' . $url, true, ($permanent === true) ? 301 : 302);
        exit();
    }

    session_start();

    session_unset();
    session_destroy();
    redirect("index.php");
?>