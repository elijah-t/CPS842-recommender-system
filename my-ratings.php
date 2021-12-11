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
            $conn = mysqli_connect("localhost", "root", "", "recommender");

            function getUserID($mysqli){
                $query = "SELECT user_id FROM users WHERE username=\"{$_SESSION['user']}\"";
                $result = mysqli_query($mysqli, $query);
                $row = mysqli_fetch_array($result);
        
                return $row[0];
            }

            function getMovieTitle($mysqli, $movieID){
                $query = "SELECT title FROM movie WHERE id={$movieID}";
                $result = mysqli_query($mysqli, $query);
                $row = mysqli_fetch_array($result);

                return $row[0];
            }

            function getMoviePoster($mysqli, $movieID){
                $query = "SELECT url FROM movie WHERE id={$movieID}";
                $result = mysqli_query($mysqli, $query);
                $row = mysqli_fetch_array($result);

                return $row[0];
            }

            $res_per_page = 10;

            $res = mysqli_query($conn, "SELECT * FROM movie");
            $num_res = mysqli_num_rows($res);

            $num_pages = ceil($num_res / $res_per_page);

            if(!isset($_GET['page'])) {
                $page = 1;
            } else {
                $page = $_GET['page'];
            }

            $page_first_res = ($page-1) * $res_per_page;
            $uID = getUserID($conn);

            $query = "SELECT * FROM `ratings` WHERE userId={$uID} LIMIT " . " " . $page_first_res . "," . $res_per_page;
            $res = mysqli_query($conn, $query);
            
            echo "<table>
                <tr>
                <th>Poster</th>
                <th>Title</th>
                <th>Your Rating</th>
                </tr>";
            
            while($row = mysqli_fetch_array($res)) {
                echo "<tr>";
                echo "<td><img src=" . getMoviePoster($conn, $row['movieId']) . "></td>";
                echo "<td>" . getMovieTitle($conn, $row['movieId']) . "</td>";
                echo "<td>" . $row['rating'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";

            echo "<div class=\"page-num\">";
            if($page >= 2) {   
                echo "<a href='my-ratings.php?page=".($page-1)."'> Prev </a>";
            }

            for($pageNum = 1; $pageNum<=$num_pages; $pageNum++) {  
                echo '<a href="my-ratings.php?page=' . $pageNum . '"> '. $pageNum . ' </a>';  
            }
            if($page < $num_pages){
                echo "<a href='my-ratings.php?page=".($page+1)."'>Next</a>";   
            }
            echo "</div>"
        ?>