<?php
    session_start();
?>

<!DOCTYPE html>
<html>
    <head>
        <!--Main CSS-->
        <link rel="stylesheet" href="index.css">

        <!--Bootstrap-->
        <!--<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">-->

        <!--Font Awesome-->
        <script src="https://kit.fontawesome.com/f996950fb3.js" crossorigin="anonymous"></script>

        <!--Google Fonts-->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville&display=swap" rel="stylesheet">

        <title>ELM Movie Recommender</title>
        <link rel="shortcut icon" type="image/jpg" href="favicon.ico"/>
    </head>
    <body>
        <?php
            $command = escapeshellcmd('python recomender-working.py 990');
            $output = shell_exec($command);
            echo $output;

            if(isset($_SESSION["user"])){
                echo "<div id=\"login-button\">
                    <i class=\"fas fa-user\"></i>
                        Signed in as {$_SESSION["user"]} |
                        <a href=\"logout.php\">
                            Logout
                        </a>
                    </div>";
            } else {
                echo "<div id=\"login-button\">
                        <a href=\"login.html\">
                            <i class=\"fas fa-user\"></i>
                            Login
                        </a>
                    </div>";
            }
        ?>
        
        <br>

        <div class="header">
            <h1 class="title">ELM Movie Recommender</h1>
        </div>

        <nav class="nav">
            <a href="index.php">Home</a>
            <a href="my-ratings.php">My Ratings</a>
            <a href="recommend-me.php">Recommend a Movie</a>
        </nav>

<?php
    $mysqli = new mysqli("localhost", "root", "", "recommender");

    function getUserID($mysqli){
        $query = "SELECT user_id FROM users WHERE username=\"{$_SESSION['user']}\"";
        $result = mysqli_query($mysqli, $query);
        $row = mysqli_fetch_array($result);

        return $row[0];
    }

    function getMovieID($mysqli){
        $query = "SELECT id FROM movie WHERE title=\"{$_POST['movie']}\"";
        $result = mysqli_query($mysqli, $query);
        $row = mysqli_fetch_array($result);

        return $row[0];
    }

    $uID = getUserID($mysqli);
    $mID = getMovieID($mysqli);
    $query = "INSERT INTO ratings VALUES ({$uID}, {$mID}, {$_POST['rating']})";
    if (mysqli_query($mysqli, $query)) {
        echo "<h2 style=\"text-align:center;\">Movie rated successfully.</h2>";
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($mysqli);
    }
?>