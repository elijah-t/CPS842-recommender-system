<?php

    $command = escapeshellcmd('python recomender-working.py 990');
    $output = shell_exec($command);
    echo $output;

?>