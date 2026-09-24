<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <link rel="stylesheet" href="style.css">
</head>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: Arial, Helvetica, sans-serif;
        background: linear-gradient(135deg, #2563eb, #1e40af);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 30px 15px;
    }

    .container {
        width: 100%;
        max-width: 700px;
    }

    .form-box {
        background: #ffffff;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    h1 {
        text-align: center;
        color: #1e293b;
        margin-bottom: 8px;
    }

    .subtitle {
        text-align: center;
        color: #64748b;
        margin-bottom: 30px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    label {
        display: block;
        margin-bottom: 7px;
        color: #334155;
        font-weight: 600;
    }

    input,
    select,
    textarea {
        width: 100%;
        padding: 13px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 15px;
        outline: none;
        transition: 0.3s;
        background-color: #fff;
    }

    input:focus,
    select:focus,
    textarea:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    textarea {
        resize: vertical;
    }

    button {
        width: 100%;
        padding: 14px;
        background: #2563eb;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.3s;
    }

    button:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }

    @media (max-width: 600px) {
        .form-box {
            padding: 25px 20px;
        }

        .form-row {
            grid-template-columns: 1fr;
            gap: 0;
        }

        h1 {
            font-size: 25px;
        }
    }
</style>

<body>

    <?php
    include("connection2.php");

    $sql = "SELECT * FROM courses";
    $query = mysqli_query($connection, $sql);

    $courses = [];
    while ($row = mysqli_fetch_assoc($query)) {
        $courses[] = $row;
    }
    ?>

    <div class="container">
        <div class="form-box">

            <h1>Student Login</h1>
            <p class="subtitle">Please fill in your details below</p>

            <form action="action2.php" method="POST">


                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="example@email.com"
                        >
                    </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input
                        id="address"
                        name="password"
                        placeholder="Enter your password"
                        ></input>
                </div>

                <button type="submit" name="login">Login</button>

            </form>

        </div>
    </div>

</body>

</html>