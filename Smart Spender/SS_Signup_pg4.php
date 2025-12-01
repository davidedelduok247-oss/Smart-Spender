<?php
session_start();
require_once 'php.php';

if (empty($_SESSION['signup_full_name']) || empty($_SESSION['signup_password']) ||
    empty($_SESSION['signup_Q1']) || empty($_SESSION['signup_Q2']) ||
    empty($_SESSION['signup_Q3']) || empty($_SESSION['signup_Q4'])) {
    header('Location: signup1.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $income_frequency = $_POST['income_frequency'] ?? '';
    $annual_salary    = $_POST['annual_salary'] ?? '';

    if ($income_frequency === '' || $annual_salary === '') {
        $error = "Please fill in all fields.";
    } else {
        $name     = $_SESSION['signup_full_name'];
        $email    = $_SESSION['signup_email'];
        $password = $_SESSION['signup_password'];
        $Q1       = $_SESSION['signup_Q1'];
        $Q2       = $_SESSION['signup_Q2'];
        $Q3       = $_SESSION['signup_Q3'];
        $Q4       = $_SESSION['signup_Q4'];

        // Hash password + security answers
        $password_hash      = password_hash($password, PASSWORD_DEFAULT);
        $SQ_oldest_sibling  = password_hash($Q1, PASSWORD_DEFAULT);
        $SQ_city            = password_hash($Q2, PASSWORD_DEFAULT);
        $SQ_dream_job       = password_hash($Q3, PASSWORD_DEFAULT);
        $SQ_first_job_title = password_hash($Q4, PASSWORD_DEFAULT);

        // Check if email already registered
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $_SESSION['register_error'] = 'Email is already registered.';
            $stmt->close();
            header("Location: SS_Main_Page_.php");
            exit();
        }
        $stmt->close();

        // Insert user
        $stmt = $conn->prepare("
            INSERT INTO users
            (name, email, password_hash,
             sq_oldest_sibling, sq_city, sq_dream_job, sq_first_job_title,
             income_frequency, annual_salary)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "ssssssssd",
            $name,
            $email,
            $password_hash,
            $SQ_oldest_sibling,
            $SQ_city,
            $SQ_dream_job,
            $SQ_first_job_title,
            $income_frequency,
            $annual_salary
        );

        if ($stmt->execute()) {
            // Clear signup data
            unset($_SESSION['signup_full_name'], $_SESSION['signup_email'], $_SESSION['signup_password'],
                  $_SESSION['signup_Q1'], $_SESSION['signup_Q2'], $_SESSION['signup_Q3'], $_SESSION['signup_Q4']);

            $_SESSION['name']  = $name;
            $_SESSION['email'] = $email;

            $stmt->close();
            header("Location: SS_Main_Page_.php");
            exit();
        } else {
            $error = "Error saving user: " . $stmt->error;
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
