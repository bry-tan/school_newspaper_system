<?php require_once 'classloader.php'; ?>

<?php 
if (!$userObj->isLoggedIn()) {
  header("Location: login.php");
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
        .hero-section {
            background: linear-gradient(135deg, #355E3B 0%, #228B22 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }
        .collaboration-badge {
            background: linear-gradient(45deg, #17a2b8, #28a745);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: bold;
        }
        .article-card {
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        .article-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        .edit-access-indicator {
            background: linear-gradient(45deg, #007bff, #28a745);
            color: white;
            padding: 0.75rem;
            border-radius: 10px;
            margin-bottom: 1rem;
        }
        .shared-article-meta {
            background: #e8f4f8;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        .no-articles-illustration {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
    </style>
    <title>Shared Articles - Writer Portal</title>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 font-weight-bold mb-3">
                        <i class="fas fa-share-alt mr-3"></i>
                        Shared Articles
                    </h1>
                    <p class="lead mb-0">
                        Collaborate on articles where other writers have granted you edit access. 
                        Work together to create amazing content for our school publication.
                    </p>
                </div>
                <div class="col-lg-4 text-center">
                    <i class="fas fa-users fa-6x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <?php
                if (isset($_SESSION['message'])) {
                    $alertClass = $_SESSION['status'] == '200' ? 'alert-success' : 'alert-danger';
                    $iconClass = $_SESSION['status'] == '200' ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle';
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
                
                <?php $sharedArticles = $sharedArticleObj->getSharedArticles($_SESSION['user_id']); ?>
                
                <?php if (empty($sharedArticles)): ?>
                    <div class="no-articles-illustration">
                        <i class="fas fa-handshake fa-5x text-muted mb-4"></i>
                        <h3 class="text-muted mb-3">No Shared Articles Yet</h3>
                        <p class="text-muted mb-4">
                            You don't have edit access to any articles yet. To collaborate with other writers:
                        </p>
                        
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <i class="fas fa-lightbulb mr-2 text-warning"></i>
                                            How to Get Started
                                        </h5>
                                        <ol class="text-left">
                                            <li>Browse articles from other writers</li>
                                            <li>Click "Request Edit" on articles you'd like to collaborate on</li>
                                            <li>Wait for the author to approve your request</li>
                                            <li>Once approved, articles will appear here for editing</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <a href="index.php" class="btn btn-primary btn-lg mr-3">
                                <i class="fas fa-search mr-2"></i>Browse Articles
                            </a>
                            <a href="articles_submitted.php" class="btn btn-outline-success btn-lg">
                                <i class="fas fa-plus mr-2"></i>Create Article
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Collaboration Stats -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="fas fa-share-alt fa-3x text-primary mb-3"></i>
                                    <h4 class="font-weight-bold"><?php echo count($sharedArticles); ?></h4>
                                    <p class="text-muted mb-0">Articles Shared</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="fas fa-users fa-3x text-success mb-3"></i>
                                    <h4 class="font-weight-bold"><?php echo count(array_unique(array_column($sharedArticles, 'author_id'))); ?></h4>
                                    <p class="text-muted mb-0">Collaborating Authors</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="fas fa-edit fa-3x text-info mb-3"></i>
                                    <h4 class="font-weight-bold">Active</h4>
                                    <p class="text-muted mb-0">Edit Access</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row justify-content-center">
                        <div class="col-lg-10">
                            <?php foreach ($sharedArticles as $article): ?>
                            <div class="article-card card mb-4" data-article-id="<?php echo $article['article_id']; ?>">
                                <div class="card-body">
                                    <!-- Collaboration Info -->
                                    <div class="edit-access-indicator">
                                        <div class="row align-items-center">
                                            <div class="col-md-8">
                                                <i class="fas fa-user-friends mr-2"></i>
                                                <strong>Collaborative Article</strong>
                                                - You have edit access to this article
                                            </div>
                                            <div class="col-md-4 text-right">
                                                <span class="collaboration-badge">
                                                    <i class="fas fa-crown mr-1"></i>
                                                    Author: <?php echo htmlspecialchars($article['author_username']); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h2 class="mb-0"><?php echo htmlspecialchars($article['title']); ?></h2>
                                        <div class="text-right">
                                            <?php if ($article['is_active'] == 0): ?>
                                                <span class="badge badge-warning badge-lg">PENDING APPROVAL</span>
                                            <?php else: ?>
                                                <span class="badge badge-success badge-lg">PUBLISHED</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="shared-article-meta">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar mr-1"></i>
                                                    <strong>Created:</strong> <?php echo date('M j, Y g:i A', strtotime($article['created_at'])); ?>
                                                </small>
                                            </div>
                                            <div class="col-md-6">
                                                <small class="text-muted">
                                                    <i class="fas fa-share mr-1"></i>
                                                    <strong>Shared:</strong> <?php echo date('M j, Y g:i A', strtotime($article['granted_at'])); ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <?php if (!empty($article['image_path'])): ?>
                                        <img src="../<?php echo htmlspecialchars($article['image_path']); ?>" 
                                             class="img-fluid mb-3" 
                                             alt="Article image"
                                             style="max-height: 300px; object-fit: cover; width: 100%; border-radius: 8px;">
                                    <?php endif; ?>
                                    
                                    <p class="mt-3 lead"><?php echo nl2br(htmlspecialchars($article['content'])); ?></p>
                                    
                                    <div class="mt-4">
                                        <button class="btn btn-primary btn-lg editSharedArticleBtn" 
                                                data-article-id="<?php echo $article['article_id']; ?>">
                                            <i class="fas fa-edit mr-2"></i>Edit This Article
                                        </button>
                                        <small class="text-muted ml-3">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            Changes will be visible to the original author and require their review
                                        </small>
                                    </div>
                                </div>
                                
                                <!-- Edit Form (Initially Hidden) -->
                                <div class="editArticleForm d-none" id="editForm<?php echo $article['article_id']; ?>">
                                    <div class="card-body border-top bg-light">
                                        <h5 class="text-primary mb-4">
                                            <i class="fas fa-edit mr-2"></i>
                                            Edit Shared Article
                                        </h5>
                                        
                                        <div class="alert alert-info">
                                            <i class="fas fa-lightbulb mr-2"></i>
                                            <strong>Collaboration Tip:</strong> You're editing a shared article. 
                                            Your changes will be saved and the original author will be notified.
                                        </div>
                                        
                                        <form action="core/handleForms.php" method="POST" enctype="multipart/form-data">
                                            <div class="form-group">
                                                <label><i class="fas fa-heading mr-2"></i>Title</label>
                                                <input type="text" class="form-control" name="title" 
                                                       value="<?php echo htmlspecialchars($article['title']); ?>" required>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label><i class="fas fa-align-left mr-2"></i>Content</label>
                                                <textarea name="content" class="form-control" rows="8" required><?php echo htmlspecialchars($article['content']); ?></textarea>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label><i class="fas fa-image mr-2"></i>Update Image (optional)</label>
                                                <input type="file" class="form-control-file" name="image" accept="image/*">
                                                <?php if (!empty($article['image_path'])): ?>
                                                    <small class="text-muted">
                                                        <i class="fas fa-info-circle mr-1"></i>
                                                        Current image will be replaced if you upload a new one
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <input type="hidden" name="article_id" value="<?php echo $article['article_id']; ?>">
                                            
                                            <div class="d-flex justify-content-between">
                                                <button type="button" class="btn btn-secondary btn-lg cancelEditBtn">
                                                    <i class="fas fa-times mr-2"></i>Cancel
                                                </button>
                                                <button type="submit" class="btn btn-success btn-lg" name="editArticleBtn">
                                                    <i class="fas fa-save mr-2"></i>Save Changes
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Collaboration Tips -->
                    <div class="row mt-5">
                        <div class="col-md-12">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-users mr-2"></i>
                                        Collaboration Tips
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <h6><i class="fas fa-handshake mr-2"></i>Respectful Editing</h6>
                                            <p class="small mb-0">Always respect the original author's vision and style when making edits.</p>
                                        </div>
                                        <div class="col-md-4">
                                            <h6><i class="fas fa-comments mr-2"></i>Communicate</h6>
                                            <p class="small mb-0">Use the notification system to discuss major changes with the author.</p>
                                        </div>
                                        <div class="col-md-4">
                                            <h6><i class="fas fa-history mr-2"></i>Track Changes</h6>
                                            <p class="small mb-0">All edits are tracked and authors can review your contributions.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Toggle edit form
        $('.editSharedArticleBtn').on('click', function() {
            const articleId = $(this).data('article-id');
            const editForm = $('#editForm' + articleId);
            editForm.toggleClass('d-none');
            
            if (!editForm.hasClass('d-none')) {
                $('html, body').animate({
                    scrollTop: editForm.offset().top - 100
                }, 500);
                
                // Focus on first input
                editForm.find('input[name="title"]').focus();
            }
        });
        
        // Cancel edit
        $('.cancelEditBtn').on('click', function() {
            $(this).closest('.editArticleForm').addClass('d-none');
        });
        
        // Form submission loading state
        $('form').on('submit', function() {
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Saving...');
            submitBtn.prop('disabled', true);
        });
        
        // Show success message if redirected with success parameter
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('success') === '1') {
            $('body').prepend(`
                <div class="alert alert-success alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 1050; max-width: 350px;">
                    <i class="fas fa-check-circle mr-2"></i>
                    <strong>Success!</strong> Article updated successfully.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `);
            
            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000);
        }
        
        // Add smooth animations
        $(document).ready(function() {
            $('.article-card').each(function(index) {
                $(this).delay(index * 200).fadeIn(800);
            });
        });
    </script>

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>