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
        $mysqli = new mysqli("localhost", "root", "", "recommender");

        function getColumn($data, $mysqli) {
            $user = $mysqli->prepare("SELECT {$data} FROM users;");
            $user->execute();
            
            $arr = [];

            foreach ($user->get_result() as $row){
                $arr[] = $row["{$data}"];
            }

            return $arr;
        }

        $logins = array_combine(getColumn("username", $mysqli), getColumn("password", $mysqli));

        function redirect($url, $permanent = false) {
            if (headers_sent() === false) header('Location: ' . $url, true, ($permanent === true) ? 301 : 302);
            exit();
        }

        if(isset($_POST["user"]) and isset($_POST["pass"])){
            if(array_key_exists($_POST["user"], $logins) and $_POST["pass"] == $logins[$_POST["user"]]) {
                $_SESSION["user"] = $_POST["user"];
                $_SESSION["pass"] = $_POST["pass"];
            } else {
                session_unset();
                session_destroy();
                redirect("incorrect_login.html");
            }
        } elseif(array_key_exists($_POST["user"], $logins) and $_POST["pass"] == $logins[$_POST["user"]]) {
            while(true){
                break;
            }
        } else {
            session_unset();
            session_destroy();
            redirect("incorrect.html");
        }
    ?>

        <?php
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
            echo "<h2 style=\"text-align:center;\">Thank you for logging in, {$_POST["user"]}!</h2>"
        ?>
    </body>
</html>