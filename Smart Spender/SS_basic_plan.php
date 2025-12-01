<?php
session_start();
require_once 'php.php';

if (empty($_SESSION['user_id'])) {
    header("Location: SS_Main_Page_.php");
    exit();
}

$user_id   = $_SESSION['user_id'];
$user_name = $_SESSION['name'] ?? 'User';

$categories = ['Housing', 'Food', 'Transport', 'Entertainment', 'Other'];
$message = "";

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Delete old basic plan rows for this user
    $stmt = $conn->prepare("DELETE FROM budgets WHERE user_id = ? AND plan_type = 'basic'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // Insert new rows
    $stmt = $conn->prepare("INSERT INTO budgets (user_id, plan_type, category, amount) VALUES (?, 'basic', ?, ?)");
    foreach ($categories as $cat) {
        $field = strtolower($cat); // housing, food, etc.
        $amount = isset($_POST[$field]) ? (float)$_POST[$field] : 0;
        $stmt->bind_param("isd", $user_id, $cat, $amount);
        $stmt->execute();
    }
    $stmt->close();

    $message = "Your basic budget has been saved.";
}

// Load existing basic plan
$existing = [];
$result = $conn->query("SELECT category, amount FROM budgets WHERE user_id = $user_id AND plan_type = 'basic'");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $existing[$row['category']] = (float)$row['amount'];
    }
}

// Get monthly income
$income_frequency = '';
$annual_salary    = 0;
$resUser = $conn->query("SELECT income_frequency, annual_salary FROM users WHERE id = $user_id");
if ($resUser && $resUser->num_rows > 0) {
    $u = $resUser->fetch_assoc();
    $income_frequency = $u['income_frequency'];
    $annual_salary    = (float)$u['annual_salary'];
}
$monthly_income = $annual_salary > 0 ? $annual_salary / 12 : 0;

// Calculate total budget
$total_budget = 0;
foreach ($categories as $cat) {
    $total_budget += $existing[$cat] ?? 0;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Plan 1 - Simple Monthly Budget</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Plan 1: Simple Monthly Budget</h1>
    <p>Set a monthly budget for each category.</p>

    <?php if ($message): ?>
        <p style="color:green;"><?php echo $message; ?></p>
    <?php endif; ?>

    <h2>Monthly Income</h2>
    <p><strong>Estimated Monthly Income:</strong> $<?php echo number_format($monthly_income, 2); ?></p>

    <form action="SS_basic_plan.php" method="post">
        <?php foreach ($categories as $cat): 
            $field = strtolower($cat);
            $value = $existing[$cat] ?? 0;
        ?>
            <label><?php echo $cat; ?> Budget:
                <input type="number" step="0.01" name="<?php echo $field; ?>" value="<?php echo htmlspecialchars($value); ?>">
            </label><br><br>
        <?php endforeach; ?>

        <button type="submit">Save Budget</button>
    </form>

    <h2>Summary</h2>
    <p><strong>Total Budgeted:</strong> $<?php echo number_format($total_budget, 2); ?></p>
    <p><strong>Difference (Income - Budget):</strong> $<?php echo number_format($monthly_income - $total_budget, 2); ?></p>

    <p><a href="SS_dashboard.php">Back to Dashboard</a></p>
</body>
</html>
