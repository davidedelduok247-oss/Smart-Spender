<?php
session_start();
require_once 'php.php';

if (empty($_SESSION['user_id'])) {
    header("Location: SS_Main_Page_.php");
    exit();
}

$user_id   = $_SESSION['user_id'];
$user_name = $_SESSION['name'] ?? 'User';

$message = "";

// Handle new transaction submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = trim($_POST['description'] ?? '');
    $category    = trim($_POST['category'] ?? '');
    $amount      = isset($_POST['amount']) ? (float)$_POST['amount'] : 0;
    $txn_date    = $_POST['txn_date'] ?? '';

    if ($description && $category && $amount > 0 && $txn_date) {
        $stmt = $conn->prepare("INSERT INTO transactions (user_id, description, category, amount, txn_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issds", $user_id, $description, $category, $amount, $txn_date);
        if ($stmt->execute()) {
            $message = "Expense added successfully.";
        } else {
            $message = "Error adding expense: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "Please fill in all fields with valid values.";
    }
}

// Load all transactions for this user
$transactions = [];
$result = $conn->query("SELECT * FROM transactions WHERE user_id = $user_id ORDER BY txn_date DESC, created_at DESC");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }
}

// Calculate totals
$total_spent = 0;
$by_category = [];

foreach ($transactions as $t) {
    $total_spent += $t['amount'];
    $cat = $t['category'];
    if (!isset($by_category[$cat])) {
        $by_category[$cat] = 0;
    }
    $by_category[$cat] += $t['amount'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Plan 2 - Detailed Expense Tracker</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Plan 2: Detailed Expense Tracker</h1>
    <p>Add individual expenses and see where your money goes.</p>

    <?php if ($message): ?>
        <p style="color:<?php echo strpos($message, 'Error') === 0 ? 'red' : 'green'; ?>;">
            <?php echo $message; ?>
        </p>
    <?php endif; ?>

    <h2>Add New Expense</h2>
    <form action="SS_detailed_plan.php" method="post">
        <label>Description:
            <input type="text" name="description" required>
        </label><br><br>

        <label>Category:
            <input type="text" name="category" required placeholder="e.g. Groceries, Rent, Fuel">
        </label><br><br>

        <label>Amount:
            <input type="number" step="0.01" name="amount" required>
        </label><br><br>

        <label>Date:
            <input type="date" name="txn_date" required>
        </label><br><br>

        <button type="submit">Add Expense</button>
    </form>

    <hr>

    <h2>Spending Summary</h2>
    <p><strong>Total Spent:</strong> $<?php echo number_format($total_spent, 2); ?></p>

    <?php if (!empty($by_category)): ?>
        <h3>By Category</h3>
        <ul>
            <?php foreach ($by_category as $cat => $amt): ?>
                <li><?php echo htmlspecialchars($cat); ?>: $<?php echo number_format($amt, 2); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <h2>All Expenses</h2>
    <?php if (empty($transactions)): ?>
        <p>No expenses added yet.</p>
    <?php else: ?>
        <table border="1" cellpadding="5" cellspacing="0">
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Category</th>
                <th>Amount</th>
            </tr>
            <?php foreach ($transactions as $t): ?>
                <tr>
                    <td><?php echo htmlspecialchars($t['txn_date']); ?></td>
                    <td><?php echo htmlspecialchars($t['description']); ?></td>
                    <td><?php echo htmlspecialchars($t['category']); ?></td>
                    <td>$<?php echo number_format($t['amount'], 2); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <p><a href="SS_dashboard.php">Back to Dashboard</a></p>
</body>
</html>
