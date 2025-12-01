<?php
session_start();
require_once 'php.php';

if (empty($_SESSION['user_id'])) {
    header("Location: SS_Main_Page_.php");
    exit();
}

$user_id   = $_SESSION['user_id'];
$user_name = $_SESSION['name'] ?? 'User';

// 1) Get income info
$income_frequency = '';
$annual_salary    = 0;

$resUser = $conn->query("SELECT income_frequency, annual_salary FROM users WHERE id = $user_id");
if ($resUser && $resUser->num_rows > 0) {
    $u = $resUser->fetch_assoc();
    $income_frequency = $u['income_frequency'];
    $annual_salary    = (float)$u['annual_salary'];
}
$monthly_income = $annual_salary > 0 ? $annual_salary / 12 : 0;

// 2) Get basic budget categories (Plan 1)
$budgetByCat = [];
$total_budget = 0;

$resBudget = $conn->query("
    SELECT category, amount
    FROM budgets
    WHERE user_id = $user_id AND plan_type = 'basic'
");
if ($resBudget && $resBudget->num_rows > 0) {
    while ($row = $resBudget->fetch_assoc()) {
        $cat = $row['category'];
        $amt = (float)$row['amount'];
        $budgetByCat[$cat] = $amt;
        $total_budget += $amt;
    }
}

// 3) Get all expenses (Plan 2)
$spentByCat = [];
$total_spent = 0;

$resTxn = $conn->query("
    SELECT category, amount
    FROM transactions
    WHERE user_id = $user_id
");
if ($resTxn && $resTxn->num_rows > 0) {
    while ($row = $resTxn->fetch_assoc()) {
        $cat = $row['category'];
        $amt = (float)$row['amount'];
        $total_spent += $amt;
        if (!isset($spentByCat[$cat])) {
            $spentByCat[$cat] = 0;
        }
        $spentByCat[$cat] += $amt;
    }
}

// 4) Combine categories for table
$allCategories = array_unique(array_merge(array_keys($budgetByCat), array_keys($spentByCat)));
sort($allCategories);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Summary - Spending Tracker</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav>
        <a href="SS_dashboard.php">Dashboard</a> |
        <a href="SS_basic_plan.php">Plan 1: Budget</a> |
        <a href="SS_detailed_plan.php">Plan 2: Expenses</a> |
        <a href="SS_summary.php">Summary</a> |
        <a href="SS_logout.php">Logout</a>
    </nav>
    <hr>

    <h1>Overall Summary for <?php echo htmlspecialchars($user_name); ?></h1>

    <section>
        <h2>Income</h2>
        <p><strong>Income Frequency:</strong> <?php echo htmlspecialchars($income_frequency ?: 'Not set'); ?></p>
        <p><strong>Annual Salary:</strong> $<?php echo number_format($annual_salary, 2); ?></p>
        <p><strong>Estimated Monthly Income:</strong> $<?php echo number_format($monthly_income, 2); ?></p>
    </section>

    <section>
        <h2>Overall Budget vs Actual Spending</h2>
        <p><strong>Total Budgeted (all categories):</strong> $<?php echo number_format($total_budget, 2); ?></p>
        <p><strong>Total Spent (all time):</strong> $<?php echo number_format($total_spent, 2); ?></p>
        <p><strong>Difference (Income - Total Spent per Month is not exact)</strong></p>
    </section>

    <section>
        <h2>By Category</h2>
        <?php if (empty($allCategories)): ?>
            <p>No budget or expense data yet. Try creating a budget in Plan 1 and adding expenses in Plan 2.</p>
        <?php else: ?>
            <table border="1" cellpadding="5" cellspacing="0">
                <tr>
                    <th>Category</th>
                    <th>Budgeted</th>
                    <th>Spent</th>
                    <th>Difference (Budget - Spent)</th>
                </tr>
                <?php foreach ($allCategories as $cat): 
                    $b = $budgetByCat[$cat] ?? 0;
                    $s = $spentByCat[$cat] ?? 0;
                    $diff = $b - $s;
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($cat); ?></td>
                        <td>$<?php echo number_format($b, 2); ?></td>
                        <td>$<?php echo number_format($s, 2); ?></td>
                        <td>$<?php echo number_format($diff, 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </section>

    <p><a href="SS_dashboard.php">Back to Dashboard</a></p>
</body>
</html>
