<?php
// session_start();
include('connection.php');
session_start();

if(isset($_POST['register'])){
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $age = $_POST['age'];
    $hashed_password= sha1($password);

    if($name == ""){
        echo "enter your name";
    }
    else if($phone == ""){
        echo "enter your phone";
    }
    else if($email == ""){
        echo "enter your email";
    }else if(strlen($password) < 8){
        echo "your password is weak";
        $hashed_password=sha1($password);
    }else if($age == ""){
        echo "enter your age";
    }else{
         $sql ="INSERT INTO users (name,phone,email,password,Age) VALUES ('$name','$phone','$email','$hashed_password','$age')";

        $query = mysqli_query($connection, $sql);

    

        if($query){
            echo "registration Success";
        }else{
             echo "registration Failed";
        }
    }
}

if(isset($_POST['Login'])){
    
    $email = $_POST['email'];
    $password = $_POST['password'];

    $hashed_password= sha1($password);

    $sql ="SELECT * FROM users WHERE  email ='$email' AND password ='$hashed_password'";

    $query = mysqli_query($connection, $sql);

    $num_rows = mysqli_num_rows($query);

    if($num_rows){
    //    echo "Login Success";
    $user = mysqli_fetch_assoc($query);
       $_SESSION['user']= $user['name'];
       header('location:dashboard.php');

       echo  $_SESSION['user'];
       echo "login success";


    }else{
       echo "Login Failed";
    }

}
?>  


