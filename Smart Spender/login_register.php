<?php

session_start();
require_once 'config.php';

if (isset($_POST['signup-form'])) {
    $name = $_POST['first-name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['Password'], PASSWORD_DEFAULT);
    $SQ_oldest_sibling = password_hash($_POST['Q1'], PASSWORD_DEFAULT);
    $SQ_city = password_hash($_POST['Q2'], PASSWORD_DEFAULT);
    $SQ_dream_job = password_hash($_POST['Q3'], PASSWORD_DEFAULT);
    $SQ_first_job_title = password_hash($_POST['Q4'], PASSWORD_DEFAULT);

    $checkEmail = $conn->query("SELECT email FROM users WHERE email = '$email'");
    if ($checkEmail->num_rows > 0) {
        $_SESSION['register_error'] = 'Email is already registered';
        $_SESSION['active_form'] = 'register';
    } else {
        $conn->query("INSERT INTO users (name, email, password, role) VALUES ('$name','$email', '$password', '$SQ_oldest_siblin', '$SQ_city', '$SQ_dream_job', '$SQ_first_job_title')");
    }

    header("Location: index.php");
    exit();
}

if (isset($_POST['login]'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE email = '$email'");
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        
        exit();
    }
}

$_SESSION['login_error'] = 'Incorrect email or password';
$_SESSION['active_form'] = 'login';
header("Location: SS_Main Page_.php");
exit();

?>
