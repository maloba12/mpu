<?php
session_start();
require_once 'db_connect.php';

if (!isset($_GET['id'])) {
    header("Location: list_users.php");
    exit();
}

$user_id = $_GET['id'];

// Fetch user data
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $_SESSION['message'] = "User not found.";
        $_SESSION['message_type'] = "error";
        header("Location: list_users.php");
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['message'] = "Error fetching user data: " . $e->getMessage();
    $_SESSION['message_type'] = "error";
    header("Location: list_users.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $status = isset($_POST['status']) ? 'active' : 'inactive';
    $new_password = trim($_POST['new_password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    // Validate required fields
    if (empty($first_name) || empty($last_name) || empty($email)) {
        $_SESSION['message'] = "First name, last name, and email are required.";
        $_SESSION['message_type'] = "error";
        header("Location: edit_user.php?id=" . $user_id);
        exit();
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = "Please enter a valid email address.";
        $_SESSION['message_type'] = "error";
        header("Location: edit_user.php?id=" . $user_id);
        exit();
    }

    // Validate password if provided
    if (!empty($new_password)) {
        if (strlen($new_password) < 8) {
            $_SESSION['message'] = "New password must be at least 8 characters long.";
            $_SESSION['message_type'] = "error";
            header("Location: edit_user.php?id=" . $user_id);
            exit();
        }

        if ($new_password !== $confirm_password) {
            $_SESSION['message'] = "Passwords do not match.";
            $_SESSION['message_type'] = "error";
            header("Location: edit_user.php?id=" . $user_id);
            exit();
        }
    }

    try {
        // Check if email already exists for other users
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $user_id]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['message'] = "Email already exists. Please use a different email.";
            $_SESSION['message_type'] = "error";
            header("Location: edit_user.php?id=" . $user_id);
            exit();
        }

        // Prepare update query
        $query = "UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, status = ?";
        $params = [$first_name, $last_name, $email, $phone, $status];

        // Add password update if provided
        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $query .= ", password = ?";
            $params[] = $hashed_password;
        }

        $query .= " WHERE id = ?";
        $params[] = $user_id;

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);

        $_SESSION['message'] = "User updated successfully!";
        $_SESSION['message_type'] = "success";
        header("Location: list_users.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['message'] = "Error updating user: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
        header("Location: edit_user.php?id=" . $user_id);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - User Management System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>User Management System</h1>
            <nav>
                <a href="index.php" class="nav-link">Dashboard</a>
                <a href="add_user.php" class="nav-link">Add User</a>
                <a href="list_users.php" class="nav-link">View Users</a>
            </nav>
        </header>

        <main>
            <div class="form-container">
                <h2>Edit User</h2>

                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert <?php echo $_SESSION['message_type']; ?>">
                        <?php
                        echo $_SESSION['message'];
                        unset($_SESSION['message']);
                        unset($_SESSION['message_type']);
                        ?>
                    </div>
                <?php endif; ?>

                <form action="" method="POST" id="userForm">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <option value="active" <?php echo $user['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $user['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="new_password">New Password (leave blank to keep current)</label>
                        <input type="password" id="new_password" name="new_password">
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password">
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <a href="list_users.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
    <script src="js/script.js"></script>
</body>
</html>
