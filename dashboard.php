<?php

include ('connection.php');

session_start();


$sql = "SELECT * FROM users WHERE name = '$_SESSION[user]'";

// echo $_SESSION[user];

$query = mysqli_query($connection, $sql);

$rows = mysqli_fetch_assoc($query);

// echo $rows;
echo $rows['name'], "<br>";
echo $rows['phone'], "<br>";
echo $rows['email'], "<br>";
echo $rows['Age'],"<br>";


// session_destroy();

?>