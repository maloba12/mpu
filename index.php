<?php
session_start();

// If user is logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management System</title>
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
            overflow-x: hidden;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header Styles */
        header {
            padding: 20px 0;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
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
            font-size: 1.8em;
            font-weight: 600;
        }

        .nav-links {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 25px;
            transition: all 0.3s ease;
            font-weight: 500;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            transform: translateY(-2px);
        }

        .nav-link.btn-primary {
            background: rgba(255, 255, 255, 0.9);
            color: #667eea;
            border: none;
        }

        .nav-link.btn-primary:hover {
            background: white;
            color: #5a67d8;
            box-shadow: 0 5px 15px rgba(255, 255, 255, 0.3);
        }

        /* Hero Section */
        .hero {
            text-align: center;
            padding: 80px 0;
            color: white;
        }

        .hero h1 {
            font-size: 3.5em;
            font-weight: 700;
            margin-bottom: 20px;
            background: linear-gradient(45deg, #fff, #f0f0f0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero p {
            font-size: 1.3em;
            margin-bottom: 40px;
            opacity: 0.9;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
        }

        .hero-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 15px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 1.1em;
            display: inline-block;
        }

        .btn-primary {
            background: white;
            color: #667eea;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(255, 255, 255, 0.3);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-3px);
        }

        /* Features Section */
        .features {
            padding: 80px 0;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
        }

        .features h2 {
            text-align: center;
            color: white;
            font-size: 2.5em;
            margin-bottom: 60px;
            font-weight: 600;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
            margin-bottom: 60px;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 20px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .feature-icon {
            font-size: 3em;
            margin-bottom: 20px;
            display: block;
        }

        .feature-card h3 {
            color: white;
            font-size: 1.5em;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .feature-card p {
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.6;
        }

        /* Stats Section */
        .stats {
            padding: 60px 0;
            text-align: center;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
        }

        .stat-item {
            color: white;
        }

        .stat-number {
            font-size: 3em;
            font-weight: 700;
            margin-bottom: 10px;
            background: linear-gradient(45deg, #fff, #f0f0f0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-label {
            font-size: 1.1em;
            opacity: 0.9;
            font-weight: 500;
        }

        /* Footer */
        footer {
            padding: 40px 0;
            background: rgba(0, 0, 0, 0.2);
            text-align: center;
            color: rgba(255, 255, 255, 0.8);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                text-align: center;
            }

            .hero h1 {
                font-size: 2.5em;
            }

            .hero p {
                font-size: 1.1em;
            }

            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                width: 250px;
            }

            .features h2 {
                font-size: 2em;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero {
            animation: fadeInUp 0.8s ease;
        }

        .feature-card {
            animation: fadeInUp 0.8s ease;
        }

        .feature-card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .feature-card:nth-child(3) {
            animation-delay: 0.4s;
        }
    </style>
</head>

<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1>User Management System</h1>
                </div>
                <nav class="nav-links">
                    <a href="#features" class="nav-link">Features</a>
                    <a href="#about" class="nav-link">About</a>
                    <a href="login.php" class="nav-link btn-primary">Login</a>
                </nav>
            </div>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="container">
                <h1>Manage Users Like a Pro</h1>
                <p>A modern, secure, and intuitive user management system designed to streamline your administrative tasks with style and efficiency.</p>
                <div class="hero-buttons">
                    <a href="login.php" class="btn btn-primary">Get Started</a>
                    <a href="#features" class="btn btn-secondary">Learn More</a>
                </div>
            </div>
        </section>

        <section id="features" class="features">
            <div class="container">
                <h2>Powerful Features</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <span class="feature-icon">👥</span>
                        <h3>User Management</h3>
                        <p>Add, edit, and manage users with an intuitive interface. Complete CRUD operations with real-time validation and feedback.</p>
                    </div>
                    <div class="feature-card">
                        <span class="feature-icon">🔐</span>
                        <h3>Secure Authentication</h3>
                        <p>Industry-standard security with password hashing, session management, and protection against common vulnerabilities.</p>
                    </div>
                    <div class="feature-card">
                        <span class="feature-icon">📊</span>
                        <h3>Analytics Dashboard</h3>
                        <p>Real-time statistics and insights about your user base with beautiful charts and comprehensive reporting.</p>
                    </div>
                </div>

                <div class="stats">
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-number">100%</div>
                            <div class="stat-label">Secure</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">24/7</div>
                            <div class="stat-label">Available</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">∞</div>
                            <div class="stat-label">Scalable</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">0</div>
                            <div class="stat-label">Downtime</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 User Management System. Built with ❤️ for better user management.</p>
        </div>
    </footer>

    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add scroll effect to header
        window.addEventListener('scroll', function() {
            const header = document.querySelector('header');
            if (window.scrollY > 100) {
                header.style.background = 'rgba(255, 255, 255, 0.15)';
            } else {
                header.style.background = 'rgba(255, 255, 255, 0.1)';
            }
        });
    </script>
</body>

</html>