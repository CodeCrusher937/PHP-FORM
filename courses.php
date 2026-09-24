```php
<?php

include('connection2.php');

session_start();

if (!isset($_SESSION['user'])) {
    header("location: login.php");
    exit();
}

$username = $_SESSION['user'];


/* ============================
   GET STUDENT + COURSE
============================ */

$sql = "SELECT 
            students.student_id,
            students.name AS student_name,
            students.email,
            courses.course_id,
            courses.name AS course_name,
            courses.description,
            courses.fee,
            courses.duration,
            departments.name AS department_name
        FROM students

        LEFT JOIN courses
        ON students.course_id = courses.course_id

        LEFT JOIN departments
        ON courses.department_id = departments.department_id

        WHERE students.name = ?";

$stmt = mysqli_prepare($connection, $sql);

mysqli_stmt_bind_param($stmt, "s", $username);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$student = mysqli_fetch_assoc($result);


/* ============================
   GET MODULES
============================ */

$modules = [];

if ($student && $student['course_id']) {

    $module_sql = "SELECT 
                        modules.module_id,
                        modules.name AS module_name,
                        modules.code

                   FROM course_modules

                   INNER JOIN modules
                   ON course_modules.module_id = modules.module_id

                   WHERE course_modules.course_id = ?

                   ORDER BY modules.name ASC";

    $module_stmt = mysqli_prepare($connection, $module_sql);

    mysqli_stmt_bind_param(
        $module_stmt,
        "i",
        $student['course_id']
    );

    mysqli_stmt_execute($module_stmt);

    $module_result = mysqli_stmt_get_result($module_stmt);

    while ($row = mysqli_fetch_assoc($module_result)) {
        $modules[] = $row;
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>My Courses</title>


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


        /* ================= SIDEBAR ================= */

        .sidebar {

            position: fixed;

            left: 0;
            top: 0;

            width: 250px;
            height: 100vh;

            background: linear-gradient(
                180deg,
                #2563eb,
                #1e40af
            );

            color: white;

            padding: 25px 20px;
        }


        .logo {
            text-align: center;
            margin-bottom: 40px;
        }


        .logo h2 {
            font-size: 25px;
        }


        .logo span {
            font-size: 12px;
            opacity: .8;
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

            transition: .3s;
        }


        .menu a:hover,
        .menu .active {

            background: rgba(255,255,255,.18);
        }


        /* ================= MAIN ================= */

        .main {

            margin-left: 250px;

            padding: 30px;
        }


        /* ================= TOPBAR ================= */

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


        /* ================= COURSE HEADER ================= */

        .course-header {

            background: linear-gradient(
                135deg,
                #2563eb,
                #4f46e5
            );

            color: white;

            padding: 30px;

            border-radius: 18px;

            margin-bottom: 25px;

            box-shadow:
                0 10px 30px
                rgba(37,99,235,.2);
        }


        .course-header h2 {

            font-size: 27px;

            margin-bottom: 10px;
        }


        .course-header p {

            opacity: .9;

            line-height: 1.6;
        }


        .course-info {

            display: flex;

            gap: 30px;

            margin-top: 20px;

            flex-wrap: wrap;
        }


        .info {

            background: rgba(255,255,255,.15);

            padding: 12px 18px;

            border-radius: 10px;
        }


        .info small {

            display: block;

            opacity: .8;

            margin-bottom: 5px;
        }


        .info strong {

            font-size: 15px;
        }


        /* ================= MODULES ================= */

        .section-title {

            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 20px;
        }


        .section-title h2 {

            font-size: 22px;
        }


        .module-count {

            background: #dbeafe;

            color: #2563eb;

            padding: 7px 14px;

            border-radius: 20px;

            font-size: 13px;

            font-weight: bold;
        }


        .modules {

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 20px;
        }


        .module-card {

            background: white;

            padding: 22px;

            border-radius: 15px;

            box-shadow:
                0 5px 20px
                rgba(0,0,0,.05);

            transition: .3s;

            border-left: 4px solid #2563eb;
        }


        .module-card:hover {

            transform: translateY(-5px);

            box-shadow:
                0 10px 25px
                rgba(0,0,0,.1);
        }


        .module-icon {

            width: 45px;

            height: 45px;

            border-radius: 10px;

            background: #dbeafe;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 20px;

            margin-bottom: 15px;
        }


        .module-card h3 {

            font-size: 17px;

            margin-bottom: 8px;
        }


        .module-code {

            display: inline-block;

            background: #f1f5f9;

            color: #475569;

            padding: 5px 9px;

            border-radius: 6px;

            font-size: 12px;

            font-weight: bold;
        }


        /* ================= EMPTY ================= */

        .empty {

            background: white;

            padding: 50px;

            border-radius: 15px;

            text-align: center;

            color: #6b7280;
        }


        .empty-icon {

            font-size: 50px;

            margin-bottom: 15px;
        }


        /* ================= BACK BUTTON ================= */

        .back {

            display: inline-block;

            margin-top: 25px;

            padding: 12px 20px;

            background: #2563eb;

            color: white;

            text-decoration: none;

            border-radius: 8px;

            transition: .3s;
        }


        .back:hover {

            background: #1d4ed8;
        }


        /* ================= RESPONSIVE ================= */

        @media (max-width: 1000px) {

            .modules {

                grid-template-columns:
                    repeat(2, 1fr);
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


            .modules {

                grid-template-columns: 1fr;
            }


            .course-info {

                flex-direction: column;

                gap: 10px;
            }

        }

    </style>

</head>


<body>


<!-- ================= SIDEBAR ================= -->

<div class="sidebar">

    <div class="logo">

        <h2>COLLEGE</h2>

        <span>Student Portal</span>

    </div>


    <ul class="menu">

        <li>

            <a href="dashboard.php">

                🏠

                <span>Dashboard</span>

            </a>

        </li>


        <li>

            <a href="#"
               class="active">

                📚

                <span>My Courses</span>

            </a>

        </li>


        <li>

            <a href="#">

                👤

                <span>My Profile</span>

            </a>

        </li>


        <li>

            <a href="#">

                💳

                <span>Payments</span>

            </a>

        </li>


        <li>

            <a href="#">

                📊

                <span>Results</span>

            </a>

        </li>


        <li>

            <a href="logout.php">

                🚪

                <span>Logout</span>

            </a>

        </li>

    </ul>

</div>


<!-- ================= MAIN ================= -->

<div class="main">


    <!-- TOPBAR -->

    <div class="topbar">

        <div>

            <h1>My Courses</h1>

            <p>
                View your course and registered modules
            </p>

        </div>

    </div>


<?php if ($student && $student['course_id']) { ?>


    <!-- ================= COURSE ================= -->

    <div class="course-header">

        <h2>

            <?php
            echo htmlspecialchars(
                $student['course_name']
            );
            ?>

        </h2>


        <p>

            <?php
            echo htmlspecialchars(
                $student['description']
            );
            ?>

        </p>


        <div class="course-info">


            <div class="info">

                <small>Department</small>

                <strong>

                    <?php
                    echo htmlspecialchars(
                        $student['department_name']
                    );
                    ?>

                </strong>

            </div>


            <div class="info">

                <small>Duration</small>

                <strong>

                    <?php
                    echo htmlspecialchars(
                        $student['duration']
                    );
                    ?>

                </strong>

            </div>


            <div class="info">

                <small>Course Fee</small>

                <strong>

                    TZS
                    <?php
                    echo number_format(
                        $student['fee']
                    );
                    ?>

                </strong>

            </div>


            <div class="info">

                <small>Student</small>

                <strong>

                    <?php
                    echo htmlspecialchars(
                        $student['student_name']
                    );
                    ?>

                </strong>

            </div>


        </div>

    </div>


    <!-- ================= MODULES ================= -->

    <div class="section-title">

        <h2>Course Modules</h2>


        <span class="module-count">

            <?php
            echo count($modules);
            ?>

            Modules

        </span>

    </div>


    <?php if (count($modules) > 0) { ?>


        <div class="modules">


        <?php foreach ($modules as $module) { ?>


            <div class="module-card">


                <div class="module-icon">

                    📖

                </div>


                <h3>

                    <?php
                    echo htmlspecialchars(
                        $module['module_name']
                    );
                    ?>

                </h3>


                <span class="module-code">

                    <?php
                    echo htmlspecialchars(
                        $module['code']
                    );
                    ?>

                </span>


            </div>


        <?php } ?>


        </div>


    <?php } else { ?>


        <div class="empty">

            <div class="empty-icon">
                📚
            </div>

            <h3>
                No Modules Found
            </h3>

            <p>
                No modules have been assigned
                to this course yet.
            </p>

        </div>


    <?php } ?>


<?php } else { ?>


    <div class="empty">

        <div class="empty-icon">
            🎓
        </div>

        <h3>
            No Course Assigned
        </h3>

        <p>
            You currently don't have a course
            assigned to your account.
        </p>

    </div>


<?php } ?>


    <a href="dashboard2.php" class="back">

        ← Back to Dashboard

    </a>


</div>


</body>

</html>


<?php

mysqli_stmt_close($stmt);

if (isset($module_stmt)) {
    mysqli_stmt_close($module_stmt);
}

mysqli_close($connection);

?>
```
