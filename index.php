<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        #form{
            flex-direction:column;
            display:flex;
            background-color:gray;
            color:black; 
            margin:5px;
            padding:20px;
            width:50%;
            height:auto;
            font-size:18px; 
            border-radius:10px;   
        }

        input{
            padding: 15px;
            height:10px;
            border-radius:10px;
            border:none;
        }

        .error{
            color:red;
        }

        button{
            background-color:lightblue;
            border-radius:10px;
            height:40px;
            width:fit-content;
            font-size:18px;
            border_color:white;
        }
        button:hover{
            
            background-color:darkblue;
            color: black;
            border-radius: 50px;
            height: 50px;
            width: fit-content;
            font-size:20px;
            border: none;
            cursor: pointer;
        
        }
        body{
            background-color:brown;
        }
    </style>
</head>
<body>
    <form action="action.php" method="POST" id="register">
        <h1>Welcome</h1>
        <div id="form">
            <label for="name">Your name</label>
            <input type="text" name="name" id="name">

            <label for="name">Your email address</label>
            <input type="text" name="email"  id="email">    

            <label for="name">Your phone number</label>
            <input type="number" name="phone" id="phone">

            <label for="password">password</label>
            <input type="password" name="password" id="password">

            <label for="name">your Age</label>
            <input type="number" name="age" id="age">
        </div>
        <br>
        <button type="submit" name="register">register</button>
    </form>    
</body>
</html>