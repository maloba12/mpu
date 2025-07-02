// ===== insert_user.php =====
<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $status = isset($_POST['status']) ? 'active' : 'inactive';

    // Validate input
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        $_SESSION['message'] = "First name, last name, email, and password are required.";
        $_SESSION['message_type'] = "error";
        header("Location: add_user.php");
        exit();
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = "Please enter a valid email address.";
        $_SESSION['message_type'] = "error";
        header("Location: add_user.php");
        exit();
    }

    // Validate password
    if (strlen($password) < 8) {
        $_SESSION['message'] = "Password must be at least 8 characters long.";
        $_SESSION['message_type'] = "error";
        header("Location: add_user.php");
        exit();
    }

    if ($password !== $confirm_password) {
        $_SESSION['message'] = "Passwords do not match.";
        $_SESSION['message_type'] = "error";
        header("Location: add_user.php");
        exit();
    }

    try {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['message'] = "Email already exists. Please use a different email.";
            $_SESSION['message_type'] = "error";
            header("Location: add_user.php");
            exit();
        }

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, phone, password, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$first_name, $last_name, $email, $phone, $hashed_password, $status]);

        $_SESSION['message'] = "User added successfully!";
        $_SESSION['message_type'] = "success";
        header("Location: list_users.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['message'] = "Error adding user: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
        header("Location: add_user.php");
        exit();
    }
} else {
    header("Location: add_user.php");
    exit();
}
?>