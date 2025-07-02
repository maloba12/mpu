// ===== js/script.js =====
// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const userForm = document.getElementById('userForm');
    
    if (userForm) {
        userForm.addEventListener('submit', function(e) {
            const firstName = document.getElementById('first_name').value.trim();
            const lastName = document.getElementById('last_name').value.trim();
            const email = document.getElementById('email').value.trim();
            
            if (!firstName || !lastName || !email) {
                e.preventDefault();
                alert('Please fill in all required fields.');
                return false;
            }
            
            if (!isValidEmail(email)) {
                e.preventDefault();
                alert('Please enter a valid email address.');
                return false;
            }
        });
    }
    
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.style.display = 'none';
            }, 300);
        }, 5000);
    });
});

// Email validation function
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Delete user confirmation
function deleteUser(userId, userName) {
    if (confirm(`Are you sure you want to delete ${userName}? This action cannot be undone.`)) {
        window.location.href = `list_users.php?delete=${userId}`;
    }
}

// Add smooth scrolling for navigation
document.querySelectorAll('.nav-link').forEach(function(link) {
    link.addEventListener('click', function(e) {
        // Add loading effect
        link.style.opacity = '0.7';
        setTimeout(function() {
            link.style.opacity = '1';
        }, 200);
    });
});

// Add form field animations
document.querySelectorAll('input').forEach(function(input) {
    input.addEventListener('focus', function() {
        this.parentNode.classList.add('focused');
    });
    
    input.addEventListener('blur', function() {
        this.parentNode.classList.remove('focused');
    });
});

// Table row highlight
document.querySelectorAll('.users-table tr').forEach(function(row) {
    row.addEventListener('mouseenter', function() {
        this.style.transform = 'scale(1.01)';
        this.style.transition = 'transform 0.2s ease';
    });
    
    row.addEventListener('mouseleave', function() {
        this.style.transform = 'scale(1)';
    });
});

// Dashboard statistics animation
document.addEventListener('DOMContentLoaded', function() {
    const statNumbers = document.querySelectorAll('.stat-number');
    
    statNumbers.forEach(function(stat) {
        const finalNumber = parseInt(stat.textContent);
        let currentNumber = 0;
        const increment = Math.ceil(finalNumber / 20);
        
        const timer = setInterval(function() {
            currentNumber += increment;
            if (currentNumber >= finalNumber) {
                currentNumber = finalNumber;
                clearInterval(timer);
            }
            stat.textContent = currentNumber;
        }, 50);
    });
});