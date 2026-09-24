<?php
include('connection.php');

if(isset($_POST['update'])){
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $id = $_POST['id'];

    if (!$name || !$phone || !$email){
         echo "fill all filled";
    }
    $sql = "UPDATE users SET name = '$name', phone = '$phone', email = '$email' WHERE id = '$id'";

    $query = mysqli_query($connection,$sql);

    if($query){
        echo "update success";
    } else {
        echo "update fail";
    }
}
?>
