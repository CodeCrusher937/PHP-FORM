```php
<?php

include('connection2.php');

session_start();

if (!isset($_SESSION['user'])) {
    header("location: login.php");
    exit();
}

$username = $_SESSION['user'];

$message = "";
$error = "";


/* ==========================================
   UPDATE PROFILE
========================================== */

if (isset($_POST['update_profile'])) {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $gender = trim($_POST['gender']);
    $age = intval($_POST['age']);
    $address = trim($_POST['address']);


    if (
        empty($name) ||
        empty($email) ||
        empty($phone)
    ) {

        $error = "Please fill in all required fields.";

    } else {

        $update_sql = "UPDATE students
                       SET name = ?,
                           email = ?,
                           phone = ?,
                           gender = ?,
                           age = ?,
                           address = ?
                       WHERE name = ?";

        $update_stmt = mysqli_prepare(
            $connection,
            $update_sql
        );

        mysqli_stmt_bind_param(
            $update_stmt,
            "ssssiss",
            $name,
            $email,
            $phone,
            $gender,
            $age,
            $address,
            $username
        );


        if (mysqli_stmt_execute($update_stmt)) {

            /*
             * Update session because student's
             * name may have changed.
             */

            $_SESSION['user'] = $name;

            $username = $name;

            $message =
                "Profile updated successfully!";

        } else {

            $error =
                "Failed to update profile.";

        }

    }

}


/* ==========================================
   GET STUDENT INFORMATION
========================================== */

$sql = "SELECT
            students.*,
            courses.name AS course_name,
            courses.duration,
            departments.name AS department_name

        FROM students

        LEFT JOIN courses
        ON students.course_id = courses.course_id

        LEFT JOIN departments
        ON courses.department_id =
           departments.department_id

        WHERE students.name = ?";


$stmt = mysqli_prepare(
    $connection,
    $sql
);

mysqli_stmt_bind_param(
    $stmt,
    "s",
    $username
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$student = mysqli_fetch_assoc($result);


if (!$student) {

    echo "Student not found.";

    exit();

}

?>


<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>My Profile</title>


<style>


* {

    margin: 0;

    padding: 0;

    box-sizing: border-box;

    font-family:
        Arial,
        Helvetica,
        sans-serif;

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

    background:
        linear-gradient(
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

    background:
        rgba(255,255,255,.18);

}


/* ================= MAIN ================= */

.main {

    margin-left: 250px;

    padding: 30px;

}


/* ================= HEADER ================= */

.topbar {

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


/* ================= ALERT ================= */

.alert {

    padding: 15px 20px;

    border-radius: 10px;

    margin-bottom: 20px;

}


.success {

    background: #dcfce7;

    color: #15803d;

}


.error {

    background: #fee2e2;

    color: #b91c1c;

}


/* ================= PROFILE HEADER ================= */

.profile-header {

    background:
        linear-gradient(
            135deg,
            #2563eb,
            #4f46e5
        );

    color: white;

    padding: 35px;

    border-radius: 18px;

    display: flex;

    align-items: center;

    gap: 25px;

    margin-bottom: 25px;

    box-shadow:
        0 10px 30px
        rgba(37,99,235,.2);

}


.avatar {

    width: 90px;

    height: 90px;

    border-radius: 50%;

    background: white;

    color: #2563eb;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 38px;

    font-weight: bold;

}


.profile-header h2 {

    font-size: 27px;

    margin-bottom: 7px;

}


.profile-header p {

    opacity: .85;

}


/* ================= CONTENT ================= */

.profile-content {

    display: grid;

    grid-template-columns:
        1fr 1.5fr;

    gap: 25px;

}


/* ================= PERSONAL CARD ================= */

.card {

    background: white;

    padding: 25px;

    border-radius: 15px;

    box-shadow:
        0 5px 20px
        rgba(0,0,0,.05);

}


.card h2 {

    font-size: 20px;

    margin-bottom: 20px;

}


.info {

    padding: 15px 0;

    border-bottom:
        1px solid #f1f5f9;

}


.info:last-child {

    border-bottom: none;

}


.info span {

    display: block;

    color: #6b7280;

    font-size: 13px;

    margin-bottom: 5px;

}


.info strong {

    color: #111827;

}


/* ================= FORM ================= */

.form-grid {

    display: grid;

    grid-template-columns:
        1fr 1fr;

    gap: 18px;

}


.form-group {

    display: flex;

    flex-direction: column;

}


.form-group.full {

    grid-column:
        1 / -1;

}


.form-group label {

    font-size: 13px;

    font-weight: bold;

    margin-bottom: 7px;

    color: #374151;

}


.form-group input,
.form-group select {

    padding: 12px 14px;

    border: 1px solid #d1d5db;

    border-radius: 8px;

    outline: none;

    font-size: 14px;

    transition: .3s;

}


.form-group input:focus,
.form-group select:focus {

    border-color: #2563eb;

    box-shadow:
        0 0 0 3px
        rgba(37,99,235,.1);

}


/* ================= BUTTON ================= */

.update-btn {

    margin-top: 20px;

    padding: 13px 25px;

    border: none;

    background:
        linear-gradient(
            135deg,
            #2563eb,
            #4f46e5
        );

    color: white;

    border-radius: 8px;

    cursor: pointer;

    font-size: 14px;

    font-weight: bold;

    transition: .3s;

}


.update-btn:hover {

    transform: translateY(-2px);

    box-shadow:
        0 5px 15px
        rgba(37,99,235,.25);

}


/* ================= BACK ================= */

.back {

    display: inline-block;

    margin-top: 25px;

    padding: 12px 20px;

    background: #2563eb;

    color: white;

    text-decoration: none;

    border-radius: 8px;

}


.back:hover {

    background: #1d4ed8;

}


/* ================= RESPONSIVE ================= */

@media(max-width:900px) {

    .profile-content {

        grid-template-columns: 1fr;

    }

}


@media(max-width:700px) {

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


    .profile-header {

        flex-direction: column;

        text-align: center;

    }


    .form-grid {

        grid-template-columns: 1fr;

    }


    .form-group.full {

        grid-column: auto;

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

            <a href="courses.php">

                📚

                <span>My Courses</span>

            </a>

        </li>


        <li>

            <a href="payments.php">

                💳

                <span>Payments</span>

            </a>

        </li>


        <li>

            <a href="profile.php"
               class="active">

                👤

                <span>My Profile</span>

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


    <div class="topbar">

        <h1>My Profile</h1>

        <p>
            View and update your personal information
        </p>

    </div>


    <!-- ALERT -->

    <?php if (!empty($message)) { ?>

        <div class="alert success">

            ✓

            <?php
            echo htmlspecialchars($message);
            ?>

        </div>

    <?php } ?>


    <?php if (!empty($error)) { ?>

        <div class="alert error">

            ⚠

            <?php
            echo htmlspecialchars($error);
            ?>

        </div>

    <?php } ?>


    <!-- PROFILE HEADER -->

    <div class="profile-header">


        <div class="avatar">

            <?php

            echo strtoupper(
                substr(
                    $student['name'],
                    0,
                    1
                )
            );

            ?>

        </div>


        <div>

            <h2>

                <?php
                echo htmlspecialchars(
                    $student['name']
                );
                ?>

            </h2>


            <p>

                Student ID:

                #

                <?php
                echo $student['student_id'];
                ?>

            </p>

        </div>


    </div>


    <!-- CONTENT -->

    <div class="profile-content">


        <!-- ================= CURRENT INFORMATION ================= -->

        <div class="card">


            <h2>
                Student Information
            </h2>


            <div class="info">

                <span>
                    Student ID
                </span>

                <strong>

                    #

                    <?php
                    echo $student['student_id'];
                    ?>

                </strong>

            </div>


            <div class="info">

                <span>
                    Course
                </span>

                <strong>

                    <?php
                    echo htmlspecialchars(
                        $student['course_name']
                    );
                    ?>

                </strong>

            </div>


            <div class="info">

                <span>
                    Department
                </span>

                <strong>

                    <?php
                    echo htmlspecialchars(
                        $student['department_name']
                    );
                    ?>

                </strong>

            </div>


            <div class="info">

                <span>
                    Duration
                </span>

                <strong>

                    <?php
                    echo htmlspecialchars(
                        $student['duration']
                    );
                    ?>

                </strong>

            </div>


            <div class="info">

                <span>
                    Email
                </span>

                <strong>

                    <?php
                    echo htmlspecialchars(
                        $student['email']
                    );
                    ?>

                </strong>

            </div>


        </div>


        <!-- ================= EDIT PROFILE ================= -->

        <div class="card">


            <h2>
                Edit Profile
            </h2>


            <form method="POST">


                <div class="form-grid">


                    <!-- NAME -->

                    <div class="form-group">

                        <label>
                            Full Name
                        </label>

                        <input
                            type="text"
                            name="name"
                            value="<?php
                            echo htmlspecialchars(
                                $student['name']
                            );
                            ?>"
                            required
                        >

                    </div>


                    <!-- EMAIL -->

                    <div class="form-group">

                        <label>
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            value="<?php
                            echo htmlspecialchars(
                                $student['email']
                            );
                            ?>"
                            required
                        >

                    </div>


                    <!-- PHONE -->

                    <div class="form-group">

                        <label>
                            Phone
                        </label>

                        <input
                            type="text"
                            name="phone"
                            value="<?php
                            echo htmlspecialchars(
                                $student['phone']
                            );
                            ?>"
                            required
                        >

                    </div>


                    <!-- GENDER -->

                    <div class="form-group">

                        <label>
                            Gender
                        </label>

                        <select name="gender">


                            <option value="Male"
                                <?php

                                if (
                                    $student['gender']
                                    == 'Male'
                                ) {

                                    echo 'selected';

                                }

                                ?>>

                                Male

                            </option>


                            <option value="Female"
                                <?php

                                if (
                                    $student['gender']
                                    == 'Female'
                                ) {

                                    echo 'selected';

                                }

                                ?>>

                                Female

                            </option>


                        </select>

                    </div>


                    <!-- AGE -->

                    <div class="form-group">

                        <label>
                            Age
                        </label>

                        <input
                            type="number"
                            name="age"
                            min="15"
                            max="100"
                            value="<?php
                            echo $student['age'];
                            ?>"
                        >

                    </div>


                    <!-- ADDRESS -->

                    <div class="form-group">

                        <label>
                            Address
                        </label>

                        <input
                            type="text"
                            name="address"
                            value="<?php
                            echo htmlspecialchars(
                                $student['address']
                            );
                            ?>"
                        >

                    </div>


                    <!-- BUTTON -->

                    <div class="form-group full">

                        <button
                            type="submit"
                            name="update_profile"
                            class="update-btn"
                        >

                            💾 Save Changes

                        </button>

                    </div>


                </div>


            </form>


        </div>


    </div>


    <a
        href="dashboard2.php"
        class="back"
    >

        ← Back to Dashboard

    </a>


</div>


</body>

</html>


<?php

mysqli_stmt_close($stmt);

if (isset($update_stmt)) {
    mysqli_stmt_close($update_stmt);
}

mysqli_close($connection);

?>
```
