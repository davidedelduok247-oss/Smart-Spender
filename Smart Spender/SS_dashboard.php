<?php
session_start();
require_once 'php.php';

if (empty($_SESSION['user_id'])) {
    header("Location: SS_Main_Page_.php");
    exit();
}

$user_id   = $_SESSION['user_id'];
$user_name = $_SESSION['name'] ?? 'User';

// Try to get income info (assuming your users table has these columns)
$income_frequency = '';
$annual_salary    = 0;

$result = $conn->query("SELECT income_frequency, annual_salary FROM users WHERE id = $user_id");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $income_frequency = $row['income_frequency'];
    $annual_salary    = (float)$row['annual_salary'];
}

$monthly_income = $annual_salary > 0 ? $annual_salary / 12 : 0;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Spending Tracker</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <nav> 
        <a href="SS_dashboard.php">Dashboard</a> |
        <a href="SS_basic_plan.php">Plan 1: Budget</a> |
        <a href="SS_detailed_plan.php">Plan 2: Expenses</a> |
        <a href="SS_Summary.php">Summary</a> |
        <a href="SS_logout.php">Logout</a>
    </nav>

    <h1>Welcome, <?php echo htmlspecialchars($user_name); ?>!</h1>

    <section>
        <h2>Your Income Summary</h2>
        <p><strong>Income Frequency:</strong> <?php echo htmlspecialchars($income_frequency ?: 'Not set'); ?></p>
        <p><strong>Annual Salary:</strong> $<?php echo number_format($annual_salary, 2); ?></p>
        <p><strong>Estimated Monthly Income:</strong> $<?php echo number_format($monthly_income, 2); ?></p>
    </section>

    <section>
        <h2>Choose a Plan</h2>
        <ul>
            <li>
                <a href="SS_basic_plan.php"><button type="button">Plan 1: Simple Monthly Budget</button></a>
                <p>Create a basic budget with a few main categories and compare to your monthly income.</p>
            </li>
            <li>
                <a href="SS_detailed_plan.php"><button type="button">Plan 2: Detailed Expense Tracker</button></a>
                <p>Add individual expenses and see where your money goes by category.</p>
            </li>
        </ul>
    </section>

    <p><a href="SS_Main_Page_.php">Back to Home</a></p>
</body>
</html>
