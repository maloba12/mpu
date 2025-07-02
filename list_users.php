// ===== list_users.php =====
<?php
session_start();
require_once 'db_connect.php';

// Handle user deletion
if (isset($_GET['delete'])) {
    $user_id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $_SESSION['message'] = "User deleted successfully!";
        $_SESSION['message_type'] = "success";
    } catch (PDOException $e) {
        $_SESSION['message'] = "Error deleting user: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
    }
    header("Location: list_users.php");
    exit();
}

// Fetch all users
try {
    $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $users = [];
    $_SESSION['message'] = "Error fetching users: " . $e->getMessage();
    $_SESSION['message_type'] = "error";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users List - User Management System</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="container">
        <header>
            <h1>User Management System</h1>
            <nav>
                <a href="index.php" class="nav-link">Dashboard</a>
                <a href="add_user.php" class="nav-link">Add User</a>
                <a href="list_users.php" class="nav-link active">View Users</a>
            </nav>
        </header>

        <main>
            <div class="users-container">
                <div class="users-header">
                    <h2>All Users</h2>
                    <a href="add_user.php" class="btn btn-primary">Add New User</a>
                </div>

                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert <?php echo $_SESSION['message_type']; ?>">
                        <?php
                        echo $_SESSION['message'];
                        unset($_SESSION['message']);
                        unset($_SESSION['message_type']);
                        ?>
                    </div>
                <?php endif; ?>

                <div class="table-container">
                    <?php if (empty($users)): ?>
                        <p class="no-users">No users found. <a href="add_user.php">Add the first user</a></p>
                    <?php else: ?>
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                                        <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo htmlspecialchars($user['phone'] ?: 'N/A'); ?></td>
                                        <td>
                                            <div class="status-container">
                                                <span class="status-badge <?php echo htmlspecialchars($user['status'] ?? 'active'); ?>">
                                                    <?php echo ucfirst($user['status'] ?? 'active'); ?>
                                                </span>
                                                <button class="status-toggle-btn" onclick="toggleStatus(<?php echo $user['id']; ?>, '<?php echo $user['status']; ?>')">
                                                    <i class="fas fa-toggle-<?php echo $user['status'] === 'active' ? 'on' : 'off'; ?>"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td><?php echo date('M j, Y g:i A', strtotime($user['created_at'])); ?></td>
                                        <td class="actions">
                                            <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                            <button onclick="deleteUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>')" class="btn btn-danger btn-sm">Delete</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    <script src="js/script.js"></script>
<script>
    function toggleStatus(userId, currentStatus) {
        const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
        
        fetch('toggle_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${userId}&status=${newStatus}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error updating status: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating status');
        });
    }
</script>
</body>

</html>