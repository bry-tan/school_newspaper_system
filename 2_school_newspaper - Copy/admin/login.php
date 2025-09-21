<?php
require_once 'classloader.php';

// If user is already logged in, redirect to appropriate page
if ($userObj->isLoggedIn()) {
    if ($userObj->isAdmin()) {
        header("Location: admin/index.php");
    } else {
        header("Location: writer/index.php");
    }
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Login - School Publication</title>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Arial', sans-serif;
        }
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
        }
        .login-header {
            background: linear-gradient(135deg, #008080 0%, #20B2AA 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .login-body {
            padding: 2rem;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #20B2AA;
            box-shadow: 0 0 0 0.2rem rgba(32, 178, 170, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #008080 0%, #20B2AA 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: transform 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .input-group-text {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-right: none;
        }
        .input-group .form-control {
            border-left: none;
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .register-link {
            text-align: center;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #e9ecef;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h2 class="mb-0">
                    <i class="fas fa-user-circle fa-2x mb-3"></i><br>
                    Welcome Back
                </h2>
                <p class="mb-0 opacity-75">Sign in to your account</p>
            </div>
            
            <div class="login-body">
                <!-- Display Messages -->
                <?php if (isset($_SESSION['message'])): ?>
                <div class="alert <?php echo $_SESSION['status'] == '200' ? 'alert-success' : 'alert-danger'; ?> alert-dismissible fade show" role="alert">
                    <i class="fas <?php echo $_SESSION['status'] == '200' ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?> mr-2"></i>
                    <?php echo htmlspecialchars($_SESSION['message']); ?>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
                <?php 
                unset($_SESSION['message']);
                unset($_SESSION['status']);
                endif; 
                ?>
                
                <!-- Login Form -->
                <form action="core/handleForms.php" method="POST" id="loginForm">
                    <div class="form-group">
                        <label for="email" class="font-weight-bold">
                            <i class="fas fa-envelope mr-2 text-muted"></i>Email Address
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-envelope text-muted"></i>
                                </span>
                            </div>
                            <input type="email" 
                                   class="form-control" 
                                   id="email" 
                                   name="email" 
                                   placeholder="Enter your email"
                                   required
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password" class="font-weight-bold">
                            <i class="fas fa-lock mr-2 text-muted"></i>Password
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-lock text-muted"></i>
                                </span>
                            </div>
                            <input type="password" 
                                   class="form-control" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Enter your password"
                                   required>
                            <div class="input-group-append">
                                <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
                                    <i class="fas fa-eye text-muted"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" name="loginUserBtn" class="btn btn-login btn-block text-white">
                        <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                    </button>
                </form>
                
                <div class="register-link">
                    <p class="mb-0 text-muted">
                        Don't have an account? 
                        <a href="register.php" class="text-decoration-none font-weight-bold" style="color: #20B2AA;">
                            Create Account
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Toggle password visibility
        $('#togglePassword').click(function() {
            const passwordField = $('#password');
            const icon = $(this).find('i');
            
            if (passwordField.attr('type') === 'password') {
                passwordField.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                passwordField.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });
        
        // Form submission loading state
        $('#loginForm').on('submit', function() {
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            
            submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Signing In...');
            submitBtn.prop('disabled', true);
            
            // Re-enable button after 5 seconds in case of server issues
            setTimeout(function() {
                submitBtn.html(originalText);
                submitBtn.prop('disabled', false);
            }, 5000);
        });
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
        
        // Form validation
        $('#loginForm').on('submit', function(e) {
            const email = $('#email').val().trim();
            const password = $('#password').val().trim();
            
            if (!email || !password) {
                e.preventDefault();
                
                // Create and show error alert
                const errorAlert = `
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Please fill in all required fields.
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                `;
                
                $('.login-body').prepend(errorAlert);
                
                // Reset button state
                const submitBtn = $(this).find('button[type="submit"]');
                submitBtn.html('<i class="fas fa-sign-in-alt mr-2"></i>Sign In');
                submitBtn.prop('disabled', false);
                
                return false;
            }
        });
    </script>
</body>
</html>