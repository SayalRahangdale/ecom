<?php
include("Config.php");

if(isset($_GET['query'])){
    $search_query = $_GET['query'];
    $sql = "SELECT * FROM tblproduct WHERE PName LIKE '%$search_query%' OR PCategory LIKE '%$search_query%'";
    $result = mysqli_query($conn, $sql);

    echo "<h2> Search Result for '$search_query':</h2>";
    if(mysqli_num_rows($result) >0){
        while ($row = mysqli_fetch_assoc($result)){
            echo "<div class='card' style='width: 18rem;'>";
            echo "<img src='/admin/product/" .$row['PImage'] ."'class='card-img-top' height='200px'>";
            echo "<div class='card-body'>";
            echo "<h5 class='card-title'>" . $row['PName'] . "</h5>";
            echo "<p class='card-text'>Price: Rs" . $row['PPrice'] . "</p>";
            echo "</div></div>";

        }
    } else {
        echo "<p>No products found.</p>";
    }
    }

?>