<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'db_connect.php';

// Get user information
try {
    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        session_destroy();
        header("Location: login.php");
        exit();
    }
} catch (PDOException $e) {
    error_log("Dashboard error: " . $e->getMessage());
    $error_message = "Database error occurred.";
}

// Get statistics
try {
    // Total users
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $totalUsers = $stmt->fetchColumn();

    // Recent users (last 7 days)
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
    $recentUsers = $stmt->fetchColumn();

    // Users this month
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $monthlyUsers = $stmt->fetchColumn();

    // Get recent users for display
    $stmt = $pdo->query("SELECT id, username, email, created_at FROM users ORDER BY created_at DESC LIMIT 5");
    $recentUsersList = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Stats error: " . $e->getMessage());
    $totalUsers = $recentUsers = $monthlyUsers = 0;
    $recentUsersList = [];
}

$success_message = '';
$error_message = '';

// Handle session messages
if (isset($_SESSION['message'])) {
    if ($_SESSION['message_type'] == 'success') {
        $success_message = $_SESSION['message'];
    } else {
        $error_message = $_SESSION['message'];
    }
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - User Management System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header */
        .header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .logo h1 {
            color: white;
            font-size: 1.5em;
            font-weight: 600;
        }

        .nav-links {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 20px;
            transition: all 0.3s ease;
            font-weight: 500;
            border: 1px solid rgba(255, 255, 255, 0.2);
            font-size: 0.9em;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .nav-link.active {
            background: rgba(255, 255, 255, 0.9);
            color: #667eea;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info {
            color: white;
            font-weight: 500;
        }

        .logout-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 8px 16px;
            border-radius: 20px;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.9em;
        }

        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        /* Main Content */
        .main-content {
            padding: 40px 0;
        }

        .dashboard-header {
            text-align: center;
            color: white;
            margin-bottom: 40px;
        }

        .dashboard-header h1 {
            font-size: 2.5em;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .dashboard-header p {
            font-size: 1.2em;
            opacity: 0.9;
        }

        /* Alerts */
        .alert {
            margin-bottom: 20px;
            padding: 15px 20px;
            border-radius: 10px;
            font-weight: 500;
            animation: slideIn 0.3s ease;
        }

        .alert-success {
            background: rgba(72, 187, 120, 0.1);
            color: #22543d;
            border: 1px solid rgba(72, 187, 120, 0.3);
            backdrop-filter: blur(10px);
        }

        .alert-error {
            background: rgba(245, 101, 101, 0.1);
            color: #742a2a;
            border: 1px solid rgba(245, 101, 101, 0.3);
            backdrop-filter: blur(10px);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .stat-card h3 {
            color: #666;
            font-size: 1em;
            margin-bottom: 15px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-number {
            font-size: 3em;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 10px;
        }

        .stat-change {
            font-size: 0.9em;
            color: #48bb78;
            font-weight: 500;
        }

        /* Quick Actions */
        .quick-actions {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 40px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .quick-actions h3 {
            color: #333;
            font-size: 1.5em;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .action-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s ease;
            font-weight: 500;
            border: none;
            cursor: pointer;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .action-btn span {
            font-size: 1.2em;
        }

        /* Recent Users */
        .recent-users {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .recent-users h3 {
            color: #333;
            font-size: 1.5em;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .users-table {
            width: 100%;
            border-collapse: collapse;
        }

        .users-table th,
        .users-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        .users-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #555;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .users-table tr:hover {
            background: #f8f9fa;
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.9em;
        }

        .date-badge {
            background: #e2e8f0;
            color: #555;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8em;
            font-weight: 500;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
            }

            .nav-links {
                justify-content: center;
            }

            .dashboard-header h1 {
                font-size: 2em;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .actions-grid {
                grid-template-columns: 1fr;
            }

            .users-table {
                font-size: 0.9em;
            }

            .users-table th,
            .users-table td {
                padding: 8px;
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: slideIn 0.6s ease;
        }
    </style>
</head>

<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1>User Management System</h1>
                </div>
                <nav class="nav-links">
                    <a href="dashboard.php" class="nav-link active">Dashboard</a>
                    <a href="add_user.php" class="nav-link">Add User</a>
                    <a href="list_users.php" class="nav-link">View Users</a>
                </nav>
                <div class="user-menu">
                    <span class="user-info">Welcome, <?php echo htmlspecialchars($user['username']); ?></span>
                    <a href="logout.php" class="logout-btn">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <div class="dashboard-header fade-in">
                <h1>Dashboard</h1>
                <p>Overview of your user management system</p>
            </div>

            <?php if ($success_message): ?>
                <div class="alert alert-success fade-in">
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="alert alert-error fade-in">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <div class="stats-grid fade-in">
                <div class="stat-card">
                    <h3>Total Users</h3>
                    <div class="stat-number"><?php echo number_format($totalUsers); ?></div>
                    <div class="stat-change">All registered users</div>
                </div>
                <div class="stat-card">
                    <h3>This Month</h3>
                    <div class="stat-number"><?php echo number_format($monthlyUsers); ?></div>
                    <div class="stat-change">Last 30 days</div>
                </div>
                <div class="stat-card">
                    <h3>This Week</h3>
                    <div class="stat-number"><?php echo number_format($recentUsers); ?></div>
                    <div class="stat-change">Last 7 days</div>
                </div>
                <div class="stat-card">
                    <h3>Active Sessions</h3>
                    <div class="stat-number">1</div>
                    <div class="stat-change">Currently online</div>
                </div>
            </div>

            <div class="quick-actions fade-in">
                <h3>Quick Actions</h3>
                <div class="actions-grid">
                    <a href="add_user.php" class="action-btn">
                        <span>👤</span>
                        Add New User
                    </a>
                    <a href="list_users.php" class="action-btn">
                        <span>📋</span>
                        View All Users
                    </a>
                    <a href="search_users.php" class="action-btn">
                        <span>🔍</span>
                        Search Users
                    </a>
                    <a href="export_users.php" class="action-btn">
                        <span>📊</span>
                        Export Data
                    </a>
                </div>
            </div>

            <div class="recent-users fade-in">
                <h3>Recent Users</h3>
                <?php if (!empty($recentUsersList)): ?>
                    <table class="users-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentUsersList as $recentUser): ?>
                                <tr>
                                    <td>
                                        <div class="user-avatar">
                                            <?php echo strtoupper(substr($recentUser['username'], 0, 1)); ?>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($recentUser['username']); ?></td>
                                    <td><?php echo htmlspecialchars($recentUser['email'] ?? 'N/A'); ?></td>
                                    <td>
                                        <span class="date-badge">
                                            <?php echo date('M j, Y', strtotime($recentUser['created_at'])); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="text-align: center; color: #666; padding: 20px;">No users found.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(function() {
                    alert.remove();
                }, 300);
            });
        }, 5000);

        // Add animation delay to cards
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.stat-card');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
            });
        });

        // Real-time clock (optional)
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString();
            const dateString = now.toLocaleDateString();

            // You can add a time display element if needed
            // document.getElementById('current-time').textContent = timeString;
        }

        // Update time every second
        setInterval(updateTime, 1000);
        updateTime(); // Initial call

        // Smooth hover effects for action buttons
        document.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-3px) scale(1.02)';
            });

            btn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Add loading state to action buttons when clicked
        document.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const originalText = this.innerHTML;
                this.innerHTML = '<span>⏳</span> Loading...';
                this.style.opacity = '0.7';
                this.style.pointerEvents = 'none';

                // Restore after a short delay if navigation doesn't occur
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.style.opacity = '1';
                    this.style.pointerEvents = 'auto';
                }, 3000);
            });
        });
    </script>
</body>

</html>