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
            <a href="">My Ratings</a>
            <a href="">Recommend a Movie</a>
        </nav>

        <div class="searchbar">
            <input type="text" placeholder=" Search for movies..." name="search" id="search">
        </div>
        
        <br>

        <?php
            $conn = mysqli_connect("localhost", "root", "", "recommender");

            $res_per_page = 5;

            $res = mysqli_query($conn, "SELECT * FROM movie");
            $num_res = mysqli_num_rows($res);

            $num_pages = ceil($num_res / $res_per_page);

            if(!isset($_GET['page'])) {
                $page = 1;
            } else {
                $page = $_GET['page'];
            }

            $page_first_res = ($page-1) * $res_per_page;

            $query = "SELECT * FROM movie ORDER BY title LIMIT" . " " . $page_first_res . "," . $res_per_page;
            $res = mysqli_query($conn, $query);
            
            echo "<table>
                <tr>
                <th>Poster</th>
                <th>Title</th>
                <th>Your Rating</th>
                </tr>";
            
            while($row = mysqli_fetch_array($res)) {
                echo "<tr>";
                echo "<td><img src=" . $row['url'] . "></td>";
                echo "<td>" . $row['title'] . "</td>";
                echo "<td>
                    <form action=\"rating-submit.php\" method=\"post\">
                        <select name=\"rating\">
                            <option value=\"0\">0</option>
                            <option value=\"1\">1</option>
                            <option value=\"2\">2</option>
                            <option value=\"3\">3</option>
                            <option value=\"4\">4</option>
                            <option value=\"5\">5</option>
                        </select>
                        <input type=\"hidden\" name=\"movie\"value=\"{$row['title']}\"
                        <br>
                        <br>
                        <br>
                        <input type=\"submit\">
                    </form>
                </td>";
                
                echo "</tr>";
            }
            echo "</table>";

            echo "<div class=\"page-num\">";
            if($page >= 2) {   
                echo "<a href='index.php?page=".($page-1)."'> Prev </a>";
            }

            for($pageNum = 1; $pageNum<=$num_pages; $pageNum++) {  
                echo '<a href="index.php?page=' . $pageNum . '"> '. $pageNum . ' </a>';  
            }
            if($page < $num_pages){
                echo "<a href='index.php?page=".($page+1)."'>Next</a>";   
            }
            echo "</div>"
        ?>

    </body>
</html>