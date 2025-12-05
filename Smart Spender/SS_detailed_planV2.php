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
        $stmt = $conn->prepare("
            INSERT INTO transactions (user_id, description, category, amount, txn_date)
            VALUES (?, ?, ?, ?, ?)
        ");
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
$result = $conn->query("
    SELECT *
    FROM transactions
    WHERE user_id = $user_id
    ORDER BY txn_date DESC, created_at DESC
");
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

    <!-- Top navigation bar -->
    <nav class="topnav">
        <div class="container topnav-inner">
            <div class="brand">
                <span class="logo">&#128178;</span>
                <span class="name"><strong>Smart</strong> Spender</span>
            </div>
            <div class="topnav-links">
                <a href="SS_dashboard.php">Dashboard</a>
                <a href="SS_basic_plan.php">Plan 1: Budget</a>
                <a href="SS_detailed_plan.php" class="active">Plan 2: Expenses</a>
                <a href="SS_Summary.php">Summary</a>
                <a href="SS_logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Main content -->
    <div class="container plan-page">
        <h1>Plan 2: Detailed Expense Tracker</h1>
        <p>Add individual expenses and see where your money goes.</p>

        <?php if ($message): ?>
            <?php $isError = (strpos($message, 'Error') === 0); ?>
            <p class="<?php echo $isError ? 'error-message' : 'success-message'; ?>">
                <?php echo $message; ?>
            </p>
        <?php endif; ?>

        <section class="plan-section">
            <h2>Add New Expense</h2>
            <form class="plan-form" action="SS_detailed_plan.php" method="post">
                <div class="plan-field">
                    <label for="description">Description</label>
                    <input id="description" type="text" name="description" required>
                </div>

                <div class="plan-field">
                    <label for="category">Category</label>
                    <input id="category" type="text" name="category" required placeholder="e.g. Groceries, Rent, Fuel">
                </div>

                <div class="plan-field">
                    <label for="amount">Amount</label>
                    <input id="amount" type="number" step="0.01" name="amount" required>
                </div>

                <div class="plan-field">
                    <label for="txn_date">Date</label>
                    <input id="txn_date" type="date" name="txn_date" required>
                </div>

                <button class="btn" type="submit">Add Expense</button>
            </form>
        </section>

        <section class="plan-section">
            <h2>Spending Summary</h2>
            <p><strong>Total Spent:</strong> $<?php echo number_format($total_spent, 2); ?></p>

            <?php if (!empty($by_category)): ?>
                <h3>By Category</h3>
                <ul class="category-list">
                    <?php foreach ($by_category as $cat => $amt): ?>
                        <li><?php echo htmlspecialchars($cat); ?>: $<?php echo number_format($amt, 2); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>

        <section class="plan-section">
            <h2>All Expenses</h2>
            <?php if (empty($transactions)): ?>
                <p>No expenses added yet.</p>
            <?php else: ?>
                <table class="summary-table">
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
        </section>

        <p><a href="SS_dashboard.php">Back to Dashboard</a></p>
    </div>
</body>
</html>
