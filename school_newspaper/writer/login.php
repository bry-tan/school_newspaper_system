<?php session_start(); ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <style>
        body {
            font-family: "Arial";
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #355E3B 0%, #228B22 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .login-body {
            padding: 2.5rem;
        }
        .form-group label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e0e6ed;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #355E3B;
            box-shadow: 0 0 0 0.2rem rgba(53, 94, 59, 0.25);
            transform: translateY(-2px);
        }
        .btn-login {
            background: linear-gradient(135deg, #355E3B 0%, #228B22 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(53, 94, 59, 0.4);
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .writer-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.9;
        }
        .link-secondary {
            color: #355E3B !important;
            text-decoration: none;
            font-weight: 500;
        }
        .link-secondary:hover {
            color: #228B22 !important;
            text-decoration: underline;
        }
        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #dee2e6, transparent);
            margin: 1.5rem 0;
        }
        .feature-badge {
            display: inline-block;
            background: rgba(53, 94, 59, 0.1);
            color: #355E3B;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            margin: 0.25rem;
        }
    </style>
    <title>Writer Login - School Publication</title>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="login-container">
                    <div class="login-header">
                        <i class="fas fa-pen-fancy writer-icon"></i>
                        <h2 class="mb-0">Writer Portal</h2>
                        <p class="mb-0 mt-2 opacity-75">Share your stories with the world</p>
                        
                        <div class="mt-3">
                            <span class="feature-badge">
                                <i class="fas fa-edit"></i> Article Creation
                            </span>
                            <span class="feature-badge">
                                <i class="fas fa-users"></i> Collaboration
                            </span>
                            <span class="feature-badge">
                                <i class="fas fa-images"></i> Rich Media
                            </span>
                        </div>
                    </div>
                    
                    <div class="login-body">
                        <?php  
                        if (isset($_SESSION['message']) && isset($_SESSION['status'])) {
                            $alertClass = $_SESSION['status'] == "200" ? 'alert-success' : 'alert-danger';
                            $iconClass = $_SESSION['status'] == "200" ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle';
                            echo "<div class='alert {$alertClass} alert-dismissible fade show' role='alert'>
                                    <i class='{$iconClass} mr-2'></i>
                                    {$_SESSION['message']}
                                    <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                        <span aria-hidden='true'>&times;</span>
                                    </button>
                                  </div>";
                            unset($_SESSION['message']);
                            unset($_SESSION['status']);
                        }
                        ?>
                        
                        <form action="core/handleForms.php" method="POST">
                            <div class="form-group">
                                <label for="email">
                                    <i class="fas fa-envelope mr-2"></i>Email Address
                                </label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       placeholder="Enter your email address..." required>
                            </div>
                            
                            <div class="form-group">
                                <label for="password">
                                    <i class="fas fa-lock mr-2"></i>Password
                                </label>
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="Enter your password..." required>
                            </div>
                            
                            <div class="form-group mb-4">
                                <button type="submit" class="btn btn-primary btn-login btn-block" name="loginUserBtn">
                                    <i class="fas fa-sign-in-alt mr-2"></i>Start Writing
                                </button>
                            </div>
                        </form>
                        
                        <div class="divider"></div>
                        
                        <div class="text-center">
                            <p class="mb-3">
                                <i class="fas fa-user-plus mr-2"></i>
                                New writer?
                                <a href="register.php" class="link-secondary">Join our community</a>
                            </p>
                            
                            <p class="mb-0">
                                <i class="fas fa-home mr-2"></i>
                                <a href="../index.php" class="link-secondary">Return to Homepage</a>
                            </p>
                        </div>
                        
                        <!-- Quick Info -->
                        <div class="mt-4 p-3" style="background: #f8f9fa; border-radius: 10px;">
                            <h6 class="mb-2">
                                <i class="fas fa-info-circle text-primary mr-2"></i>
                                What you can do as a writer:
                            </h6>
                            <ul class="list-unstyled mb-0 small">
                                <li><i class="fas fa-check text-success mr-2"></i>Create and publish articles</li>
                                <li><i class="fas fa-check text-success mr-2"></i>Add images to your stories</li>
                                <li><i class="fas fa-check text-success mr-2"></i>Request to edit other writers' articles</li>
                                <li><i class="fas fa-check text-success mr-2"></i>Collaborate on shared projects</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Loading Animation -->
    <script>
        $('form').on('submit', function() {
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Logging in...');
            submitBtn.prop('disabled', true);
            
            // Re-enable after 3 seconds in case of error
            setTimeout(function() {
                submitBtn.html(originalText);
                submitBtn.prop('disabled', false);
            }, 3000);
        });
        
        // Add floating label effect
        $('.form-control').on('focus blur', function() {
            $(this).toggleClass('focused');
        });
    </script>
    
    <!-- Font Awesome and Bootstrap JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>