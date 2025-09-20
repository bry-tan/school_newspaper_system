<?php require_once 'classloader.php'; ?>

<?php 
if (!$userObj->isLoggedIn()) {
  header("Location: login.php");
  exit;
}

if (!$userObj->isAdmin()) {
  header("Location: ../writer/index.php");
  exit;
}  
?>
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
            background-color: #f8f9fa;
        }
        .admin-hero {
            background: linear-gradient(135deg, #008080 0%, #20B2AA 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }
        .admin-badge {
            background: linear-gradient(45deg, #007bff, #28a745);
        }
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .article-card {
            transition: transform 0.2s ease;
        }
        .article-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .quick-actions {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        .form-control {
            border-radius: 8px;
        }
        .btn-admin {
            background: linear-gradient(135deg, #008080 0%, #20B2AA 100%);
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
        }
        .notification-panel {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
            max-width: 350px;
        }
    </style>
    <title>Admin Dashboard - School Publication</title>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <!-- Admin Hero Section -->
    <div class="admin-hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 font-weight-bold mb-3">
                        <i class="fas fa-user-shield mr-3"></i>
                        Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!
                    </h1>
                    <p class="lead mb-4">
                        Manage your school publication with powerful administrative tools. 
                        Review articles, moderate content, and keep your community informed.
                    </p>
                </div>
                <div class="col-lg-4 text-center">
                    <i class="fas fa-chart-line fa-6x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container-fluid">
        <!-- Statistics Dashboard -->
        <div class="row mb-4">
            <?php 
            $totalArticles = count($articleObj->getArticles());
            $activeArticles = count($articleObj->getActiveArticles());
            $pendingArticles = $totalArticles - $activeArticles;
            $unreadNotifications = $notificationObj->getUnreadCount($_SESSION['user_id']);
            ?>
            
            <div class="col-md-3 mb-3">
                <div class="stats-card text-center">
                    <i class="fas fa-newspaper fa-3x text-primary mb-3"></i>
                    <h3 class="font-weight-bold"><?php echo $totalArticles; ?></h3>
                    <p class="text-muted mb-0">Total Articles</p>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="stats-card text-center">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h3 class="font-weight-bold"><?php echo $activeArticles; ?></h3>
                    <p class="text-muted mb-0">Published</p>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="stats-card text-center">
                    <i class="fas fa-clock fa-3x text-warning mb-3"></i>
                    <h3 class="font-weight-bold"><?php echo $pendingArticles; ?></h3>
                    <p class="text-muted mb-0">Pending Review</p>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="stats-card text-center">
                    <i class="fas fa-bell fa-3x text-info mb-3"></i>
                    <h3 class="font-weight-bold"><?php echo $unreadNotifications; ?></h3>
                    <p class="text-muted mb-0">Notifications</p>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="quick-actions">
                    <h4 class="text-primary mb-4">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Create Admin Article
                    </h4>
                    
                    <form action="core/handleForms.php" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label><i class="fas fa-heading mr-2"></i>Title</label>
                            <input type="text" class="form-control" name="title" 
                                   placeholder="Enter announcement title..." required>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-align-left mr-2"></i>Content</label>
                            <textarea name="description" class="form-control" rows="4" 
                                      placeholder="Write your admin message or announcement..." required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-image mr-2"></i>Image (optional)</label>
                            <input type="file" class="form-control-file" name="image" accept="image/*">
                            <small class="form-text text-muted">Max size: 5MB. Formats: JPG, PNG, GIF, WEBP</small>
                        </div>
                        
                        <button type="submit" class="btn btn-admin btn-block" name="insertAdminArticleBtn">
                            <i class="fas fa-paper-plane mr-2"></i>Publish Now
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="col-lg-8">
                <!-- Recent Articles -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="font-weight-bold">
                        <i class="fas fa-newspaper mr-2 text-primary"></i>
                        Recent Articles
                    </h3>
                    <div>
                        <a href="articles_from_students.php" class="btn btn-outline-primary btn-sm mr-2">
                            <i class="fas fa-cog mr-1"></i>Manage All
                        </a>
                        <a href="articles_submitted.php" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-user mr-1"></i>My Articles
                        </a>
                    </div>
                </div>
                
                <?php $articles = array_slice($articleObj->getActiveArticles(), 0, 6); ?>
                <?php if (empty($articles)): ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                        <h5>No Articles Yet</h5>
                        <p class="mb-0">Create your first admin announcement using the form on the left!</p>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($articles as $article): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card article-card shadow-sm h-100">
                                <?php if (!empty($article['image_path'])): ?>
                                    <img src="../<?php echo htmlspecialchars($article['image_path']); ?>" 
                                         class="card-img-top" 
                                         style="height: 200px; object-fit: cover;"
                                         alt="Article image">
                                <?php endif; ?>
                                
                                <div class="card-body d-flex flex-column">
                                    <div class="mb-2">
                                        <?php if ($article['is_admin'] == 1): ?>
                                            <span class="badge admin-badge text-white">
                                                <i class="fas fa-crown mr-1"></i>ADMIN
                                            </span>
                                        <?php endif; ?>
                                        
                                        <?php if ($article['is_active'] == 1): ?>
                                            <span class="badge badge-success">PUBLISHED</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">PENDING</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <h5 class="card-title font-weight-bold">
                                        <?php echo htmlspecialchars($article['title']); ?>
                                    </h5>
                                    
                                    <p class="card-text flex-grow-1">
                                        <?php 
                                        $content = htmlspecialchars($article['content']);
                                        echo strlen($content) > 100 ? substr($content, 0, 100) . '...' : $content;
                                        ?>
                                    </p>
                                    
                                    <small class="text-muted">
                                        <i class="fas fa-user mr-1"></i>
                                        <?php echo htmlspecialchars($article['username']); ?>
                                        <span class="mx-2">|</span>
                                        <i class="fas fa-calendar mr-1"></i>
                                        <?php echo date('M j, Y', strtotime($article['created_at'])); ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="articles_from_students.php" class="btn btn-outline-primary">
                            <i class="fas fa-eye mr-2"></i>View All Articles
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Success/Error Notifications -->
    <?php if (isset($_SESSION['message'])): ?>
    <div class="notification-panel">
        <div class="alert <?php echo $_SESSION['status'] == '200' ? 'alert-success' : 'alert-danger'; ?> alert-dismissible fade show" role="alert">
            <i class="fas <?php echo $_SESSION['status'] == '200' ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?> mr-2"></i>
            <?php echo $_SESSION['message']; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
    <?php 
    unset($_SESSION['message']);
    unset($_SESSION['status']);
    endif; 
    ?>

    <script>
        // Auto-hide notifications after 5 seconds
        setTimeout(function() {
            $('.notification-panel .alert').alert('close');
        }, 5000);
        
        // Add loading state to form submission
        $('form').on('submit', function() {
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Publishing...');
            submitBtn.prop('disabled', true);
        });
        
        // Smooth scroll for internal links
        $('a[href^="#"]').on('click', function(event) {
            var target = $(this.getAttribute('href'));
            if (target.length) {
                event.preventDefault();
                $('html, body').stop().animate({
                    scrollTop: target.offset().top - 100
                }, 1000);
            }
        });
    </script>

    <!-- Font Awesome and Bootstrap JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>