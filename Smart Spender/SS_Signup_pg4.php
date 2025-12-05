<?php
session_start();

// Show PHP errors so we can see issues
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Your DB connection file
require_once 'php.php';

$error = "";
$success = "";
$debug  = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // These are **NOT** saved to DB (your table has no columns for them),
    // but they are part of your assignment questions.
    $income_frequency = $_POST['income_frequency'] ?? "";
    $annual_salary    = $_POST['annual_salary'] ?? "";

    // Pull data from previous pages (stored in sessions)
    $full_name = $_SESSION['signup_full_name'] ?? "";
    $email     = $_SESSION['signup_email'] ?? "";
    $password  = $_SESSION['signup_password'] ?? "";

    $Q1 = $_SESSION['signup_Q1'] ?? "";
    $Q2 = $_SESSION['signup_Q2'] ?? "";
    $Q3 = $_SESSION['signup_Q3'] ?? "";
    $Q4 = $_SESSION['signup_Q4'] ?? "";

    // Debug (you'll see this printed on page 4)
    $debug .= "<pre>";
    $debug .= "Name: "  . htmlspecialchars($full_name) . "\n";
    $debug .= "Email: " . htmlspecialchars($email) . "\n";
    $debug .= "Q1: "    . htmlspecialchars($Q1) . "\n";
    $debug .= "Q2: "    . htmlspecialchars($Q2) . "\n";
    $debug .= "Q3: "    . htmlspecialchars($Q3) . "\n";
    $debug .= "Q4: "    . htmlspecialchars($Q4) . "\n";
    $debug .= "Income freq (not saved): " . htmlspecialchars($income_frequency) . "\n";
    $debug .= "Annual salary (not saved): " . htmlspecialchars($annual_salary) . "\n";
    $debug .= "</pre>";

    // Make sure we have the **core** info needed for the DB insert
    if ($full_name === "" || $email === "" || $password === "" ||
        $Q1 === "" || $Q2 === "" || $Q3 === "" || $Q4 === "") {

        $error = "Some signup information is missing. Please restart the signup (pg1 → pg2 → pg3 → pg4).";

    } else {
        // Hash the password and security question answers
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $sq1_hash      = password_hash($Q1, PASSWORD_DEFAULT); // oldest sibling
        $sq2_hash      = password_hash($Q2, PASSWORD_DEFAULT); // city
        $sq3_hash      = password_hash($Q3, PASSWORD_DEFAULT); // dream job
        $sq4_hash      = password_hash($Q4, PASSWORD_DEFAULT); // first job title

        // Insert into YOUR existing table:
        // ID, name, email, password, SQ_oldest_sibling, SQ_city, SQ_dream_job, SQ_first_job_title

        $stmt = $conn->prepare("
            INSERT INTO users
            (name, email, password,
             SQ_oldest_sibling, SQ_city, SQ_dream_job, SQ_first_job_title)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        if (!$stmt) {
            $error = "Prepare failed: " . $conn->error;
        } else {
            $stmt->bind_param(
                "sssssss",
                $full_name,
                $email,
                $password_hash,
                $sq1_hash,
                $sq2_hash,
                $sq3_hash,
                $sq4_hash
            );

            if ($stmt->execute()) {
                $success = "✅ Account created and saved to the database.";

                // Optional: clear signup session data
                unset(
                    $_SESSION['signup_full_name'],
                    $_SESSION['signup_email'],
                    $_SESSION['signup_password'],
                    $_SESSION['signup_Q1'],
                    $_SESSION['signup_Q2'],
                    $_SESSION['signup_Q3'],
                    $_SESSION['signup_Q4']
                );
            } else {
                $error = "Insert failed: " . $stmt->error;
            }

            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up - Smart Spender</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <!-- Logo top-left -->
  <header class="signup-header">
    <div class="brand">
      <span class="logo">&#128178;</span>
      <span class="name"><strong>Smart</strong> <br> 
        <strong>Spender</strong> </span>
    </div>
  </header>

  <!-- Main content -->
  <main class="signup-main container">
    <h1>Simple Quiz</h1>

        <?php if ($error): ?>
        <p style="color:red;"><?php echo $error; ?></p><?php endif; ?>

    <form class="signup-form" action="page3.html" method="get">
        
        <h2 id="Question">How often do you recieve income?</h2>
            <button class ="qbtn">Monthly</button>
            <button class ="qbtn">Biweekly</button>
            <button class ="qbtn">Weekly</button>
            <button class ="qbtn">Irregular</button>

         <h3 id="Question">What is your annual salary?</h2>      
            <input id="salary" name="salary" type="salary" placeholder="Annual salary" required>

      <button class="btn" type="submit">Next</button>
    </form>
  </main>
</body>
</html>

