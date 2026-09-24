```php
<?php

include('connection2.php');

session_start();

if (!isset($_SESSION['user'])) {
    header("location: login.php");
    exit();
}

$username = $_SESSION['user'];


/* =====================================
   GET STUDENT + COURSE INFORMATION
===================================== */

$sql = "SELECT
            students.student_id,
            students.name AS student_name,
            students.email,
            courses.course_id,
            courses.name AS course_name,
            courses.fee

        FROM students

        LEFT JOIN courses
        ON students.course_id = courses.course_id

        WHERE students.name = ?";

$stmt = mysqli_prepare($connection, $sql);

mysqli_stmt_bind_param($stmt, "s", $username);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$student = mysqli_fetch_assoc($result);


if (!$student) {

    echo "Student not found.";
    exit();

}


$student_id = $student['student_id'];

$course_fee = $student['fee'];


/* =====================================
   GET PAYMENT INFORMATION
===================================== */

$payment_sql = "SELECT
                    SUM(
                        CASE
                            WHEN status = 'Paid'
                            THEN amount
                            ELSE 0
                        END
                    ) AS total_paid,

                    COUNT(*) AS total_transactions

                FROM payments

                WHERE student_id = ?";

$payment_stmt = mysqli_prepare(
    $connection,
    $payment_sql
);

mysqli_stmt_bind_param(
    $payment_stmt,
    "i",
    $student_id
);

mysqli_stmt_execute($payment_stmt);

$payment_result = mysqli_stmt_get_result(
    $payment_stmt
);

$payment_summary =
    mysqli_fetch_assoc($payment_result);


$total_paid =
    $payment_summary['total_paid'] ?? 0;


$total_transactions =
    $payment_summary['total_transactions'] ?? 0;


/* =====================================
   CALCULATE BALANCE
===================================== */

$balance = $course_fee - $total_paid;

if ($balance < 0) {
    $balance = 0;
}


/* =====================================
   PAYMENT PERCENTAGE
===================================== */

if ($course_fee > 0) {

    $percentage =
        ($total_paid / $course_fee) * 100;

} else {

    $percentage = 0;

}


if ($percentage > 100) {
    $percentage = 100;
}


/* =====================================
   GET PAYMENT HISTORY
===================================== */

$history_sql = "SELECT
                    payment_id,
                    amount,
                    status,
                    payment_date

                FROM payments

                WHERE student_id = ?

                ORDER BY payment_date DESC";

$history_stmt = mysqli_prepare(
    $connection,
    $history_sql
);

mysqli_stmt_bind_param(
    $history_stmt,
    "i",
    $student_id
);

mysqli_stmt_execute($history_stmt);

$history_result =
    mysqli_stmt_get_result($history_stmt);

?>


<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Payments</title>


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


/* ================= STUDENT ================= */

.student-box {

    background: white;

    padding: 20px;

    border-radius: 15px;

    margin-bottom: 25px;

    box-shadow:
        0 5px 20px
        rgba(0,0,0,.05);

}


.student-box h2 {

    font-size: 20px;

    margin-bottom: 7px;

}


.student-box p {

    color: #6b7280;

}


/* ================= SUMMARY ================= */

.summary {

    display: grid;

    grid-template-columns:
        repeat(3, 1fr);

    gap: 20px;

    margin-bottom: 25px;

}


.card {

    background: white;

    padding: 25px;

    border-radius: 15px;

    box-shadow:
        0 5px 20px
        rgba(0,0,0,.05);

}


.card-icon {

    width: 45px;

    height: 45px;

    border-radius: 10px;

    background: #dbeafe;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 21px;

    margin-bottom: 15px;

}


.card h3 {

    font-size: 14px;

    color: #6b7280;

    margin-bottom: 7px;

}


.card p {

    font-size: 24px;

    font-weight: bold;

    color: #111827;

}


.paid {

    color: #16a34a !important;

}


.balance {

    color: #dc2626 !important;

}


/* ================= PROGRESS ================= */

.payment-progress {

    background: white;

    padding: 25px;

    border-radius: 15px;

    margin-bottom: 25px;

    box-shadow:
        0 5px 20px
        rgba(0,0,0,.05);

}


.progress-header {

    display: flex;

    justify-content: space-between;

    margin-bottom: 12px;

}


.progress-header strong {

    color: #2563eb;

}


.progress-bar {

    width: 100%;

    height: 13px;

    background: #e5e7eb;

    border-radius: 20px;

    overflow: hidden;

}


.progress-fill {

    height: 100%;

    background:
        linear-gradient(
            90deg,
            #2563eb,
            #4f46e5
        );

    border-radius: 20px;

}


/* ================= HISTORY ================= */

.history {

    background: white;

    padding: 25px;

    border-radius: 15px;

    box-shadow:
        0 5px 20px
        rgba(0,0,0,.05);

}


.history h2 {

    margin-bottom: 20px;

}


table {

    width: 100%;

    border-collapse: collapse;

}


th {

    text-align: left;

    background: #f8fafc;

    padding: 14px;

    font-size: 13px;

    color: #64748b;

}


td {

    padding: 15px 14px;

    border-bottom:
        1px solid #f1f5f9;

}


.status {

    padding: 6px 12px;

    border-radius: 20px;

    font-size: 12px;

    font-weight: bold;

}


.status-paid {

    background: #dcfce7;

    color: #15803d;

}


.status-pending {

    background: #fef3c7;

    color: #b45309;

}


.empty {

    text-align: center;

    padding: 35px;

    color: #6b7280;

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

    .summary {

        grid-template-columns:
            1fr;

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


    .history {

        overflow-x: auto;

    }


    table {

        min-width: 600px;

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

            <a href="dashboard2.php">

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

            <a href="#"
               class="active">

                💳

                <span>Payments</span>

            </a>

        </li>


        <li>

            <a href="#">

                👤

                <span>Profile</span>

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

        <h1>Payment Dashboard</h1>

        <p>
            Manage and track your college payments
        </p>

    </div>


    <!-- STUDENT -->

    <div class="student-box">

        <h2>

            <?php
            echo htmlspecialchars(
                $student['student_name']
            );
            ?>

        </h2>

        <p>

            Course:

            <?php
            echo htmlspecialchars(
                $student['course_name']
            );
            ?>

        </p>

    </div>


    <!-- SUMMARY -->

    <div class="summary">


        <div class="card">

            <div class="card-icon">

                💰

            </div>

            <h3>Course Fee</h3>

            <p>

                TZS
                <?php
                echo number_format(
                    $course_fee
                );
                ?>

            </p>

        </div>


        <div class="card">

            <div class="card-icon">

                ✅

            </div>

            <h3>Total Paid</h3>

            <p class="paid">

                TZS
                <?php
                echo number_format(
                    $total_paid
                );
                ?>

            </p>

        </div>


        <div class="card">

            <div class="card-icon">

                ⚠️

            </div>

            <h3>Balance</h3>

            <p class="balance">

                TZS
                <?php
                echo number_format(
                    $balance
                );
                ?>

            </p>

        </div>


    </div>


    <!-- PROGRESS -->

    <div class="payment-progress">

        <div class="progress-header">

            <span>
                Payment Progress
            </span>

            <strong>

                <?php
                echo number_format(
                    $percentage,
                    1
                );
                ?>%

            </strong>

        </div>


        <div class="progress-bar">

            <div
                class="progress-fill"
                style="width:
                <?php
                echo $percentage;
                ?>%;">

            </div>

        </div>

    </div>


    <!-- HISTORY -->

    <div class="history">

        <h2>
            Payment History
        </h2>


        <?php if (
            mysqli_num_rows($history_result) > 0
        ) { ?>


        <table>

            <thead>

                <tr>

                    <th>
                        Payment ID
                    </th>

                    <th>
                        Amount
                    </th>

                    <th>
                        Status
                    </th>

                    <th>
                        Date
                    </th>

                </tr>

            </thead>


            <tbody>


            <?php while (
                $payment =
                mysqli_fetch_assoc(
                    $history_result
                )
            ) { ?>


                <tr>

                    <td>

                        #<?php
                        echo $payment['payment_id'];
                        ?>

                    </td>


                    <td>

                        <strong>

                            TZS
                            <?php
                            echo number_format(
                                $payment['amount']
                            );
                            ?>

                        </strong>

                    </td>


                    <td>


                        <?php

                        if (
                            strtolower(
                                $payment['status']
                            ) == 'paid'
                        ) {

                            echo
                            '<span class="status status-paid">
                                ✓ Paid
                            </span>';

                        } else {

                            echo
                            '<span class="status status-pending">
                                ⏳ Pending
                            </span>';

                        }

                        ?>


                    </td>


                    <td>

                        <?php
                        echo date(
                            "d M Y",
                            strtotime(
                                $payment['payment_date']
                            )
                        );
                        ?>

                    </td>

                </tr>


            <?php } ?>


            </tbody>

        </table>


        <?php } else { ?>


            <div class="empty">

                <div style="font-size:45px;">
                    💳
                </div>

                <h3>
                    No Payments Yet
                </h3>

                <p>
                    Your payment history will
                    appear here.
                </p>

            </div>


        <?php } ?>


    </div>


    <a
        href="dashboard2.php"
        class="back">

        ← Back to Dashboard

    </a>


</div>


</body>

</html>


<?php

mysqli_stmt_close($stmt);

mysqli_stmt_close($payment_stmt);

mysqli_stmt_close($history_stmt);

mysqli_close($connection);

?>
```
