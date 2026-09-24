<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
    </head>
    <body>

        <table>
            <tr>
                <h1>Dashboard</>
                <form action="update.php" method="POST">
                    <?php 
                    
    include ('connection.php');

    include ('navigator.php');
    session_start();


    $sql = "SELECT * FROM users WHERE name = '$_SESSION[user]'";

    // echo $_SESSION[user];

    $query = mysqli_query($connection, $sql);

    $row = mysqli_fetch_assoc($query);



                    ?>

                    <label >Name</label>
                    <input type="text" name="name" value="<?php echo $row['name'] ?>"><br><br>
                
                    <label>Email</label>
                    <input type="text" name="email" value="<?php echo $row['email'] ?>"><br><br>

                    <label>phone</label>
                    <input type="phone" name="phone" value="<?php echo $row['phone'] ?>"><br><br>
                    <input type="number" name="id" style="display:none;" value="<?php echo $row['id'] ?>"><br><br>
                    

                    <button type="submit" name="update">update</button>
                </form>
            </tr>    
        </table>       
    </body>            
</html>
