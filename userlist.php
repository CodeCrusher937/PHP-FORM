<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        table{
            border-collapse:collapse;
        }
        th, td{
            padding:10px;
            border:1px solid;
        }
        th{
            background-color:pink;
            color:white;
            border-color:black;
            font: optional;
        }
    </style>
</head>
<body>
    <table>
        <tr>
            <thead>
                <th>id</th>
                <th>Name</th>
                <th>Email</th>
                <th>Age</th>
                <th>Action</th>
            </thead>
        </tr>

        <tr>
            <tbody>
                <?php

                include('connection.php');

                $sql = "SELECT * FROM users";

                $query = mysqli_query($connection, $sql);

                while ($row = mysqli_fetch_assoc($query)){
                    ?>
                    <tr>
                        <td><?php echo $row['id'];?></td>
                        <td><?php echo $row['name'];?></td>
                        <td><?php echo $row['email'];?></td>
                        <td><?php echo $row['Age'];?></td>
                        <td>
                            <form action="delete.php" method="POST">
                                <input type="number" hidden name="id" value="<?php echo $row['id'];?>">
                                <button name="delete">delete</button>
                            </form>
                        
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </tr>
    </table>
</body>
</html>