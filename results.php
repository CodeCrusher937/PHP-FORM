<?php
include('connection2.php');
session_start();

if (!isset($_SESSION['user'])) {
    header("location: login.php");
    exit();
}

$username = $_SESSION['user'];

/* Get student ID */
$sql = "SELECT student_id, name 
        FROM students 
        WHERE name = ?";

$stmt = mysqli_prepare($connection, $sql);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);

$query = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($query);

if (!$student) {
    die("Student not found.");
}

$student_id = $student['student_id'];


/* Get results */
$sql = "SELECT 
            results.result_id,
            modules.name AS module_name,
            modules.code,
            results.marks,
            results.grade,
            results.semester,
            results.academic_year
        FROM results
        INNER JOIN modules 
            ON results.module_id = modules.module_id
        WHERE results.student_id = ?
        ORDER BY results.academic_year DESC, results.semester ASC";

$stmt = mysqli_prepare($connection, $sql);
mysqli_stmt_bind_param($stmt, "i", $student_id);
mysqli_stmt_execute($stmt);

$result_query = mysqli_stmt_get_result($stmt);


/* Calculate average */
$average_sql = "SELECT AVG(marks) AS average_marks
                FROM results
                WHERE student_id = ?";

$stmt = mysqli_prepare($connection, $average_sql);
mysqli_stmt_bind_param($stmt, "i", $student_id);
mysqli_stmt_execute($stmt);

$average_query = mysqli_stmt_get_result($stmt);
$average_data = mysqli_fetch_assoc($average_query);

$average_marks = $average_data['average_marks'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Results - Student Portal</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f4f7fb;
            display: flex;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            height: 100vh;
            background: linear-gradient(180deg, #0d47a1, #1976d2);
            color: white;
            position: fixed;
            left: 0;
            top: 0;
            padding: 25px 15px;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 35px;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 14px 15px;
            margin-bottom: 8px;
            border-radius: 8px;
            transition: 0.3s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: rgba(255,255,255,0.18);
        }

        /* Main */
        .main {
            margin-left: 250px;
            width: calc(100% - 250px);
            min-height: 100vh;
        }

        /* Topbar */
        .topbar {
            height: 70px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .topbar h3 {
            color: #333;
        }

        .content {
            padding: 30px;
        }

        .welcome {
            background: linear-gradient(135deg, #1976d2, #42a5f5);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 25px;
        }

        .welcome h1 {
            margin-bottom: 8px;
        }

        /* Stats */
        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.06);
        }

        .stat-card h4 {
            color: #777;
            margin-bottom: 10px;
        }

        .stat-card h2 {
            color: #1976d2;
        }

        /* Results table */
        .results-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.06);
            overflow-x: auto;
        }

        .results-card h2 {
            margin-bottom: 20px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 750px;
        }

        th {
            background: #1976d2;
            color: white;
            padding: 14px;
            text-align: left;
        }

        td {
            padding: 14px;
            border-bottom: 1px solid #eee;
        }

        tr:hover {
            background: #f8fbff;
        }

        .grade {
            font-weight: bold;
            padding: 6px 10px;
            border-radius: 6px;
            background: #e3f2fd;
            color: #1976d2;
        }

        .no-results {
            text-align: center;
            padding: 30px;
            color: #777;
        }

        /* Mobile */
        @media (max-width: 768px) {

            .sidebar {
                width: 70px;
                padding: 20px 8px;
            }

            .sidebar h2 {
                font-size: 18px;
            }

            .sidebar a {
                text-align: center;
                font-size: 20px;
            }

            .sidebar a span {
                display: none;
            }

            .main {
                margin-left: 70px;
                width: calc(100% - 70px);
            }

            .content {
                padding: 20px;
            }

            .stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<!-- Sidebar -->
<div class="sidebar">

    <h2>🎓 Portal</h2>

    <a href="dashboard.php">
        🏠 <span>Dashboard</span>
    </a>

    <a href="courses.php">
        📚 <span>My Course</span>
    </a>

    <a href="profile.php">
        👤 <span>My Profile</span>
    </a>

    <a href="payments.php">
        💳 <span>Payments</span>
    </a>

    <a href="results.php" class="active">
        📊 <span>Results</span>
    </a>

    <a href="logout.php">
        🚪 <span>Logout</span>
    </a>

</div>


<!-- Main -->
<div class="main">

    <div class="topbar">
        <h3>Student Results</h3>

        <div>
            👤 <?php echo htmlspecialchars($student['name']); ?>
        </div>
    </div>


    <div class="content">

        <div class="welcome">
            <h1>Academic Results 📊</h1>
            <p>
                View your examination results and academic performance.
            </p>
        </div>


        <!-- Statistics -->
        <div class="stats">

            <div class="stat-card">
                <h4>Student</h4>
                <h2>
                    <?php echo htmlspecialchars($student['name']); ?>
                </h2>
            </div>

            <div class="stat-card">
                <h4>Average Marks</h4>
                <h2>
                    <?php echo number_format($average_marks, 2); ?>%
                </h2>
            </div>

            <div class="stat-card">
                <h4>Total Subjects</h4>
                <h2>
                    <?php echo mysqli_num_rows($result_query); ?>
                </h2>
            </div>

        </div>


        <!-- Results -->
        <div class="results-card">

            <h2>My Examination Results</h2>

            <?php if (mysqli_num_rows($result_query) > 0): ?>

                <table>

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Module</th>
                            <th>Code</th>
                            <th>Marks</th>
                            <th>Grade</th>
                            <th>Semester</th>
                            <th>Academic Year</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php
                    $number = 1;

                    while ($row = mysqli_fetch_assoc($result_query)):
                    ?>

                        <tr>

                            <td>
                                <?php echo $number++; ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['module_name']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['code']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['marks']); ?>%
                            </td>

                            <td>
                                <span class="grade">
                                    <?php echo htmlspecialchars($row['grade']); ?>
                                </span>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['semester']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['academic_year']); ?>
                            </td>

                        </tr>

                    <?php endwhile; ?>

                    </tbody>

                </table>

            <?php else: ?>

                <div class="no-results">
                    <h3>No Results Available</h3>
                    <p>Your examination results have not been uploaded yet.</p>
                </div>

            <?php endif; ?>

        </div>

    </div>

</div>

</body>
</html>