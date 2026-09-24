<?php
include('connection2.php');

session_start();

if(isset($_POST['register'])){
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $course_id = $_POST['course_id'];
    $hashed_password= sha1($password);

    if($name == ""){
        echo "Enter your name";
    }
    else if($phone == ""){
        echo "Enter your phone";
    }
    else if($email == ""){
        echo "Enter your email";
    }else if(strlen($password) < 6){
        echo "Your password is weak";
        $hashed_password=sha1($password);
    }else if($age == ""){
        echo "Enter your age";
    }else if($gender == ""){
        echo "Enter your gender";
    }else if($address == ""){
        echo "Enter your address";
    }else{

    

         $sql ="INSERT INTO students (name, email, password, phone, gender, age, course_id, address) 
         VALUES ('$name','$email','$hashed_password', '$phone', '$gender', '$age', '$course_id', '$address')";

        $query = mysqli_query($connection, $sql);

        if($query){
            echo "Registration Success";
        }else{
             echo "Registration Failed";
        }
    }
}


if(isset($_POST['login'])){
    
    $email = $_POST['email'];
    $password = $_POST['password'];

    $hashed_password= sha1($password);

    $sql ="SELECT * FROM students WHERE  email ='$email' AND password='$hashed_password'";

    $query = mysqli_query($connection, $sql);

    $num_rows = mysqli_num_rows($query);

    if($num_rows){
         echo "Login Success";
    $user = mysqli_fetch_assoc($query);

    $_SESSION['user']= $user['name'];
    header('location:dashboard2.php');

    // echo  $_SESSION['user'];
    //   echo "login success";
 }else{
       echo "Login Failed";
    }

}
?>  