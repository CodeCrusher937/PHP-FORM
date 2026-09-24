<?php
session_start();
require_once 'connection2.php';


$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

if ($course_id <= 0) {
    die("No Course Selected.");
}


$sql = "
    SELECT m.module_id, m.name, m.code
    FROM course_modules cm
    INNER JOIN modules m ON cm.module_id = m.module_id
    WHERE cm.course_id = ?
    ORDER BY m.module_id ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $course_id);
$stmt->execute();

$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Modules</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f7fb;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 900px;
            margin: auto;
        }

        h1 {
            color: #222;
            margin-bottom: 25px;
        }

        .modules {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .module-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        .module-card h3 {
            margin: 0 0 10px;
            color: #222;
        }

        .module-code {
            color: #666;
            margin-bottom: 15px;
        }

        .open-module {
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 10px 16px;
            border-radius: 7px;
            text-decoration: none;
        }

        .open-module:hover {
            background: #0056b3;
        }

        .empty {
            background: white;
            padding: 20px;
            border-radius: 10px;
        }
    </style>
</head>

<body>

<div class="container">

    <h1>Course Modules</h1>

    <?php if ($result->num_rows > 0): ?>

        <div class="modules">

            <?php while ($module = $result->fetch_assoc()): ?>

                <div class="module-card">

                    <h3>
                        <?= htmlspecialchars($module['name']) ?>
                    </h3>

                    <div class="module-code">
                        Code: <?= htmlspecialchars($module['code']) ?>
                    </div>

                    <a
                        href="module_details.php?module_id=<?= $module['module_id'] ?>&course_id=<?= $course_id ?>"
                        class="open-module"
                    >
                        Open Module
                    </a>

                </div>

            <?php endwhile; ?>

        </div>

    <?php else: ?>

        <div class="empty">
            No Modules Available In This Course.!
        </div>

    <?php endif; ?>

</div>

</body>
</html>
