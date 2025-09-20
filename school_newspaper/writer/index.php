<?php require_once 'classloader.php'; ?>

<?php 
if (!$userObj->isLoggedIn()) {
  header("Location: login.php");
  exit;
}

if ($userObj->isAdmin()) {
  header("Location: ../admin/index.php");
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
        .writer-hero {
            background: linear-gradient(135deg, #355E3B 0%, #228B22 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
            text-align: center;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .quick-write {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        .article-card {
            transition: transform 0.2s ease;
        }
        .article-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .admin-message {
            background: linear-gradient(45deg, #007bff, #28a745);
            color: white;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        .btn-writer {
            background: linear-gradient(135deg, #355E3B 0%, #228B22 100%);
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
        }
        .collaboration-panel {
            background: #e8f5e8;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .inspiration-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            text-align: center;
        }
    </style>
    <title>Writer Dashboard - School Publication</title>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <!-- Writer Hero Section -->
    <div class="writer-hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 font-weight-bold mb-3">
                        <i class="fas fa-pen-fancy mr-3"></i>
                        Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!
                    </h1>
                    <p class="lead mb-4">
                        Ready to share your stories? Create engaging articles, collaborate with fellow writers, 
                        and contribute to our school's digital newspaper.
                    </p>
                    <div class="d-flex flex-wrap">
                        <span class="badge badge-light mr-2 mb-2">
                            <i class="fas fa-edit mr-1"></i>Article Creation
                        </span>
                        <span class="badge badge-light mr-2 mb-2">
                            <i class="fas fa-users mr-1"></i>Collaboration
                        </span>
                        <span class="badge badge-light mr-2 mb-2">
                            <i class="fas fa-images mr-1"></i>Rich Media
                        </span>
                        <span class="badge badge-light mr-2 mb-2">
                            <i class="fas fa-bell mr-1"></i>Notifications
                        </span>
                    </div>
                </div>
                <div class="col-lg-4 text-center">
                    <i class="fas fa-feather-alt fa-6x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container-fluid">
        <!-- Writer Statistics -->
        <div class="row mb-4">
            <?php 
            $myArticles = $articleObj->getArticlesByUserID($_SESSION['user_id']);
            $myActiveArticles = array_filter($myArticles, function($article) { return $article['is_active'] == 1; });
            $myPendingArticles = array_filter($myArticles, function($article) { return $article['is_active'] == 0; });
            $sharedWithMe = $sharedArticleObj->getSharedArticles($_SESSION['user_id']);
            $unreadNotifications = $notificationObj->getUnreadCount($_SESSION['user_id']);
            ?>
            
            <div class="col-md-3 mb-3">
                <div class="stats-card">
                    <i class="fas fa-newspaper fa-3x text-primary mb-3"></i>
                    <h3 class="font-weight-bold"><?php echo count($myArticles); ?></h3>
                    <p class="text-muted mb-0">My Articles</p>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="stats-card">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h3 class="font-weight-bold"><?php echo count($myActiveArticles); ?></h3>
                    <p class="text-muted mb-0">Published</p>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="stats-card">
                    <i class="fas fa-share-alt fa-3x text-info mb-3"></i>
                    <h3 class="font-weight-bold"><?php echo count($sharedWithMe); ?></h3>
                    <p class="text-muted mb-0">Shared with Me</p>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="stats-card">
                    <i class="fas fa-bell fa-3x text-warning mb-3"></i>
                    <h3 class="font-weight-bold"><?php echo $unreadNotifications; ?></h3>
                    <p class="text-muted mb-0">Notifications</p>
                </div>
            </div>
        </div>
        
        <div class="row">
            <!-- Quick Write Panel -->
            <div class="col-lg-5 mb-4">
                <div class="quick-write">
                    <h4 class="text-primary mb-4">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Quick Write
                    </h4>
                    
                    <form action="core/handleForms.php" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label><i class="fas fa-heading mr-2"></i>Title</label>
                            <input type="text" class="form-control" name="title" 
                                   placeholder="What's your story about?" required>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-align-left mr-2"></i>Content</label>
                            <textarea name="description" class="form-control" rows="4" 
                                      placeholder="Share your thoughts, experiences, or news..." required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-image mr-2"></i>Add Image (optional)</label>
                            <input type="file" class="form-control-file" name="image" accept="image/*">
                            <small class="form-text text-muted">Make your story more engaging with images!</small>
                        </div>
                        
                        <button type="submit" class="btn btn-writer btn-block" name="insertArticleBtn">
                            <i class="fas fa-paper-plane mr-2"></i>Submit Article
                        </button>
                    </form>
                </div>
                
                <!-- Collaboration Panel -->
                <div class="collaboration-panel">
                    <h5 class="text-success mb-3">
                        <i class="fas fa-handshake mr-2"></i>
                        Collaboration Hub
                    </h5>
                    
                    <div class="row text-center">
                        <div class="col-6">
                            <a href="shared_articles.php" class="btn btn-outline-success btn-sm btn-block mb-2">
                                <i class="fas fa-share-alt"></i><br>
                                <small>Shared Articles</small>
                            </a>
                            <span class="badge badge-success"><?php echo count($sharedWithMe); ?></span>
                        </div>
                        <div class="col-6">
                            <a href="articles_submitted.php#browse-articles" class="btn btn-outline-primary btn-sm btn-block mb-2">
                                <i class="fas fa-search"></i><br>
                                <small>Browse & Request</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Articles Feed -->
            <div class="col-lg-7">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="font-weight-bold">
                        <i class="fas fa-newspaper mr-2 text-primary"></i>
                        Latest Articles
                    </h3>
                    <div>
                        <a href="articles_submitted.php" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-user mr-1"></i>My Articles
                        </a>
                    </div>
                </div>
                
                <?php $articles = array_slice($articleObj->getActiveArticles(), 0, 4); ?>
                <?php if (empty($articles)): ?>
                    <div class="inspiration-card">
                        <i class="fas fa-lightbulb fa-4x mb-3"></i>
                        <h4>Be the First to Share!</h4>
                        <p class="mb-0">No articles published yet. Use the form on the left to create the first story for our school publication!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($articles as $article): ?>
                    <div class="card article-card shadow-sm mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="d-flex align-items-center">
                                    <?php if ($article['is_admin'] == 1): ?>
                                        <div class="admin-message mr-3">
                                            <small><i class="fas fa-crown mr-1"></i>Admin Message</small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if ($article['author_id'] != $_SESSION['user_id']): ?>
                                    <button class="btn btn-outline-primary btn-sm request-edit-btn" 
                                            data-article-id="<?php echo $article['article_id']; ?>"
                                            data-article-title="<?php echo htmlspecialchars($article['title']); ?>">
                                        <i class="fas fa-edit"></i> Request Edit
                                    </button>
                                <?php endif; ?>
                            </div>
                            
                            <h5 class="card-title font-weight-bold">
                                <?php echo htmlspecialchars($article['title']); ?>
                            </h5>
                            
                            <?php if (!empty($article['image_path'])): ?>
                                <img src="../<?php echo htmlspecialchars($article['image_path']); ?>" 
                                     class="img-fluid mb-3 rounded" 
                                     style="max-height: 200px; object-fit: cover; width: 100%;"
                                     alt="Article image">
                            <?php endif; ?>
                            
                            <p class="card-text">
                                <?php 
                                $content = htmlspecialchars($article['content']);
                                echo strlen($content) > 150 ? substr($content, 0, 150) . '...' : $content;
                                ?>
                            </p>
                            
                            <small class="text-muted">
                                <i class="fas fa-user mr-1"></i>
                                <strong><?php echo htmlspecialchars($article['username']); ?></strong>
                                <span class="mx-2">|</span>
                                <i class="fas fa-calendar mr-1"></i>
                                <?php echo date('M j, Y g:i A', strtotime($article['created_at'])); ?>
                            </small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <div class="text-center">
                        <a href="../index.php" class="btn btn-outline-secondary">
                            <i class="fas fa-eye mr-2"></i>View All Articles
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Edit Request Modal -->
    <div class="modal fade" id="editRequestModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit mr-2"></i>Request Edit Access
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form method="POST" action="core/handleForms.php">
                    <div class="modal-body">
                        <p>Request edit access for: <strong id="modalArticleTitle"></strong></p>
                        <div class="form-group">
                            <label>Message to Author (optional)</label>
                            <textarea class="form-control" name="message" rows="3" 
                                      placeholder="Why would you like to collaborate on this article?"></textarea>
                        </div>
                        <input type="hidden" name="article_id" id="modalArticleId">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" name="createEditRequest" class="btn btn-primary">
                            <i class="fas fa-paper-plane mr-1"></i>Send Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <?php if (isset($_SESSION['message'])): ?>
    <div style="position: fixed; top: 20px; right: 20px; z-index: 1050; max-width: 350px;">
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
        // Request edit access
        $('.request-edit-btn').on('click', function() {
            const articleId = $(this).data('article-id');
            const articleTitle = $(this).data('article-title');
            
            $('#modalArticleId').val(articleId);
            $('#modalArticleTitle').text(articleTitle);
            $('#editRequestModal').modal('show');
        });
        
        // Form submission loading state
        $('form').on('submit', function() {
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Processing...');
            submitBtn.prop('disabled', true);
        });
        
        // Auto-hide alerts
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
        
        // Smooth animations for stats cards
        $(window).on('load', function() {
            $('.stats-card').each(function(index) {
                $(this).delay(index * 100).animate({
                    opacity: 1
                }, 500);
            });
        });
    </script>

    <!-- Font Awesome and Bootstrap JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>