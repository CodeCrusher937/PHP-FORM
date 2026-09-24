```php
<?php

include('connection2.php');

session_start();

if (!isset($_SESSION['user'])) {
    header("location: login2.php");
    exit();
}

$username = $_SESSION['user'];

$sql = "SELECT * FROM students WHERE name = ?";
$stmt = mysqli_prepare($connection, $sql);

mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);

$query = mysqli_stmt_get_result($stmt);
$rows = mysqli_fetch_assoc($query);

if (!$rows) {
    echo "Student not found.";
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Student Dashboard</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            background: #f5f7fb;
            color: #1f2937;
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            height: 100vh;
            background: linear-gradient(180deg, #2563eb, #1e40af);
            color: white;
            padding: 25px 20px;
        }

        .logo {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo h2 {
            font-size: 25px;
            letter-spacing: 1px;
        }

        .logo span {
            font-size: 12px;
            opacity: 0.8;
        }

        .menu {
            list-style: none;
        }

        .menu li {
            margin: 10px 0;
        }

        .menu a {
            display: block;
            padding: 14px 16px;
            color: white;
            text-decoration: none;
            border-radius: 10px;
            transition: 0.3s;
        }

        .menu a:hover,
        .menu .active {
            background: rgba(255,255,255,0.18);
        }

        .main {
            margin-left: 250px;
            padding: 30px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .topbar h1 {
            font-size: 28px;
            color: #111827;
        }

        .topbar p {
            color: #6b7280;
            margin-top: 5px;
        }

        .profile-mini {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .profile-mini img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: #dbeafe;
        }

        .profile-mini strong {
            display: block;
        }

        .profile-mini small {
            color: #6b7280;
        }

        .welcome {
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            color: white;
            padding: 30px;
            border-radius: 18px;
            margin-bottom: 25px;
            box-shadow: 0 10px 30px rgba(37, 99, 235, 0.2);
        }

        .welcome h2 {
            font-size: 26px;
            margin-bottom: 8px;
        }

        .welcome p {
            opacity: 0.9;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            padding: 22px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .card-icon {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: #dbeafe;
            color: #2563eb;
            font-size: 20px;
            margin-bottom: 15px;
        }

        .card h3 {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 7px;
        }

        .card p {
            font-size: 22px;
            font-weight: bold;
            color: #111827;
        }

        .content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
        }

        .box {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }

        .box h2 {
            margin-bottom: 20px;
            font-size: 20px;
        }

        .profile {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 25px;
        }

        .profile-avatar {
            width: 75px;
            height: 75px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            font-weight: bold;
        }

        .profile h3 {
            font-size: 20px;
        }

        .profile p {
            color: #6b7280;
            margin-top: 5px;
        }

        .details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .detail {
            background: #f8fafc;
            padding: 15px;
            border-radius: 10px;
        }

        .detail span {
            display: block;
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 5px;
        }

        .detail strong {
            color: #111827;
        }


        .actions {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .action {
            padding: 15px;
            border-radius: 10px;
            background: #f8fafc;
            text-decoration: none;
            color: #1f2937;
            transition: 0.3s;
        }

        .action:hover {
            background: #dbeafe;
            color: #2563eb;
        }

        @media (max-width: 1000px) {

            .cards {
                grid-template-columns: repeat(2, 1fr);
            }

            .content {
                grid-template-columns: 1fr;
            }

        }

        @media (max-width: 700px) {

            .sidebar {
                width: 70px;
                padding: 20px 10px;
            }

            .logo h2,
            .logo span,
            .menu a span {
                display: none;
            }

            .main {
                margin-left: 70px;
                padding: 20px;
            }

            .cards {
                grid-template-columns: 1fr;
            }

            .topbar {
                align-items: flex-start;
            }

            .details {
                grid-template-columns: 1fr;
            }

        }

    </style>

</head>

<body>

    <div class="sidebar">

        <div class="logo">
            <h2>COLLEGE</h2>
            <span>Student Portal</span>
        </div>

        <ul class="menu">

            <li>
                <a href="#" class="active">
                    🏠 <span>Dashboard</span>
                </a>
            </li>

            <li>
                <a href="profile.php">
                    👤 <span>My Profile</span>
                </a>
            </li>

            <li>
                <a href="courses.php">
                    📚 <span>My Courses</span>
                </a>
            </li>

            <li>
                <a href="modules.php">
                    📖 <span>Modules</span>
                </a>
            </li>

            <li>
                <a href="payment.php">
                    💳 <span>Payments</span>
                </a>
            </li>

            <li>
                <a href="results.php">
                    📊 <span>Results</span>
                </a>
            </li>

            <li>
                <a href="logout.php">
                    🚪 <span>Logout</span>
                </a>
            </li>

        </ul>

    </div>


    <div class="main">

        <!-- TOP BAR -->

        <div class="topbar">

            <div>
                <h1>Student Dashboard</h1>
                <p>Welcome back to your student portal</p>
            </div>

            <div class="profile-mini">

                <div>
                    <strong>
                        <?php echo htmlspecialchars($rows['name']); ?>
                    </strong>

                    <small>Student</small>
                </div>

            </div>

        </div>

        <div class="welcome">

            <h2>
                Hello, <?php echo htmlspecialchars($rows['name']); ?> 👋
            </h2>

            <p>
                Welcome to your college dashboard. Manage your studies,
                profile and payments from here.
            </p>

        </div>

        <div class="cards">

            <div class="card">

                <div class="card-icon">
                    📚
                </div>

                <h3>My Course</h3>

                <p>
                    <?php echo htmlspecialchars($rows['course_id']); ?>
                </p>

            </div>


            <div class="card">

                <div class="card-icon">
                    📅
                </div>

                <h3>Academic Year</h3>

                <p>2026</p>

            </div>


            <div class="card">

                <div class="card-icon">
                    💳
                </div>

                <h3>Payment Status</h3>

                <p style="color: #16a34a;">
                    Active
                </p>

            </div>


            <div class="card">

                <div class="card-icon">
                    🎓
                </div>

                <h3>Student Status</h3>

                <p style="color: #16a34a;">
                    Active
                </p>

            </div>

        </div>


        <!-- CONTENT -->

        <div class="content">


            <!-- PROFILE INFORMATION -->

            <div class="box">

                <h2>My Profile</h2>

                <div class="profile">

                    <div class="profile-avatar">

                        <?php
                        echo strtoupper(substr($rows['name'], 0, 1));
                        ?>

                    </div>

                    <div>

                        <h3>
                            <?php echo htmlspecialchars($rows['name']); ?>
                        </h3>

                        <p>
                            Student Account
                        </p>

                    </div>

                </div>


                <div class="details">

                    <div class="detail">

                        <span>Full Name</span>

                        <strong>
                            <?php echo htmlspecialchars($rows['name']); ?>
                        </strong>

                    </div>


                    <div class="detail">

                        <span>Phone Number</span>

                        <strong>
                            <?php echo htmlspecialchars($rows['phone']); ?>
                        </strong>

                    </div>


                    <div class="detail">

                        <span>Email Address</span>

                        <strong>
                            <?php echo htmlspecialchars($rows['email']); ?>
                        </strong>

                    </div>


                    <div class="detail">

                        <span>Gender</span>

                        <strong>
                            <?php echo htmlspecialchars($rows['gender']); ?>
                        </strong>

                    </div>


                    <div class="detail">

                        <span>Age</span>

                        <strong>
                            <?php echo htmlspecialchars($rows['age']); ?>
                        </strong>

                    </div>


                    <div class="detail">

                        <span>Address</span>

                        <strong>
                            <?php echo htmlspecialchars($rows['address']); ?>
                        </strong>

                    </div>

                </div>

            </div>

            <div class="box">

                <h2>Quick Actions</h2>

                <div class="actions">

                    <a href="profile.php" class="action">
                        👤 &nbsp; View Profile
                    </a>

                    <a href="courses.php" class="action">
                        📚 &nbsp; View Courses
                    </a>

                    <a href="payment.php" class="action">
                        💳 &nbsp; View Payments
                    </a>

                    <a href="#" class="action">
                        📊 &nbsp; View Results
                    </a>

                    <a href="#" class="action">
                        ⚙️ &nbsp; Account Settings
                    </a>

                </div>

            </div>

        </div>

    </div>

</body>

</html>

<?php

mysqli_stmt_close($stmt);
mysqli_close($connection);

?>