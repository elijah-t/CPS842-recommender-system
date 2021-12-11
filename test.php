<?php
    $command = escapeshellcmd("python recommender.py 15 3 1");
    $output = shell_exec($command);
    echo $output;

    $arr = explode(",", $output);
    // print_r($arr);
    // echo "<br>";
    $id_arr = array();
    $rating_arr = array();

    for($i = 0; $i < count($arr)-1; $i++){
        $exploded = explode(" ", $arr[$i]);
        array_push($id_arr, $exploded[0]);
        array_push($rating_arr, $exploded[1]);
    }

    echo "<br>";
    print_r($id_arr);
    echo "<br>";
    print_r($rating_arr);

?>