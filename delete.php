<?php
include('connection.php');
    if(isset($_POST['delete'])){
        $id = $_POST['id'];

        $sql = "DELETE FROM users WHERE  id = '$id'";

        $query = mysqli_query($connection,$sql);

        if($query){
        echo "delete success";
        } else {
            echo "delete fail";
        }
    }   
    
?>