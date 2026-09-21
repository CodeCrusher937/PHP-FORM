<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        #form{
            flex-direction: column;
            display: flex;
            background-color:brown;
            color: black;
            margin: 1px;
            padding: 40px;
            width:40%;
            height: 280px;
            font-size: 18px;
            border-radius: 10px;
        }

        input{
            padding:10px;
            border-radius:10px;
            border:none;
        }

        button{
         background-color: darkblue;
            color: black;
            border-radius:99px;
            padding: 20px;
            border: none;
        }

        button:hover{
            background-color: white;
            color: black;
            border-radius: 99px;
            padding:20px;
            border: none;
            font-size:18px;
            cursor: pointer;
        
        }
        body{
            background-color:gray;
        }
    </style>
</head>
<body>
     <form action="action.php" method="POST" id="register">
        <h1>Please Login</h1>
        <div id="form">
            <label for="name">email address</label>
            <input type="text" name="email"  id="email" >

            <label for="password">password</label>
            <input type="password" name="password" id="password" ><br>

             <button type="submit" name="Login">Login</button>
        </div>
       
    </form>
    
</body>
</html>