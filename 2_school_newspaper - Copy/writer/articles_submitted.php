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
        .hero-section {
            background: linear-gradient(135deg, #355E3B 0%, #228B22 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }
        .category-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: bold;
            color: white;
        }
        .article-card {
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        .article-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        .submission-card, .browse-card, .stats-mini {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        .browse-card {
            background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%);
            color: white;
        }
        .shared-indicator {
            background: linear-gradient(45deg, #17a2b8, #28a745);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: bold;
        }
        .stats-mini {
            text-align: center;
            padding: 1rem;
        }
        .category-filter {
            display: inline-block;
            margin: 0.25rem;
            padding: 0.5rem 1rem;
            border: 2px solid #dee2e6;
            border-radius: 20px;
            background: white;
            color: #6c757d;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        .category-filter:hover, .category-filter.active {
            text-decoration: none;
            color: white;
            transform: translateY(-2px);
        }
        .no-articles-card {
            background: white;
            border-radius: 15px;
            padding: 3rem;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
    </style>
    <title>My Articles - Writer Portal</title>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 font-weight-bold mb-3">
                        <i class="fas fa-newspaper mr-3"></i>
                        My Articles
                    </h1>
                    <p class="lead mb-0">
                        Manage your articles, choose categories, collaborate with other writers, and track your publishing journey.
                    </p>
                </div>
                <div class="col-lg-4 text-center">
                    <i class="fas fa-pen-alt fa-6x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container-fluid">
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
        
        <div class="row">
            <!-- Left Sidebar -->
            <div class="col-lg-4">
                <!-- Quick Stats -->
                <?php 
                $myArticles = $articleObj->getArticlesByUserID($_SESSION['user_id']);
                $publishedCount = count(array_filter($myArticles, function($a) { return $a['is_active'] == 1; }));
                $pendingCount = count(array_filter($myArticles, function($a) { return $a['is_active'] == 0; }));
                $sharedCount = count($sharedArticleObj->getSharedArticles($_SESSION['user_id']));
                ?>
                
                <div class="row mb-4">
                    <div class="col-4">
                        <div class="stats-mini">
                            <i class="fas fa-newspaper fa-2x text-primary mb-2"></i>
                            <h5 class="font-weight-bold mb-0"><?php echo count($myArticles); ?></h5>
                            <small class="text-muted">Total</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="stats-mini">
                            <i class="fas fa-check fa-2x text-success mb-2"></i>
                            <h5 class="font-weight-bold mb-0"><?php echo $publishedCount; ?></h5>
                            <small class="text-muted">Published</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="stats-mini">
                            <i class="fas fa-share fa-2x text-info mb-2"></i>
                            <h5 class="font-weight-bold mb-0"><?php echo $sharedCount; ?></h5>
                            <small class="text-muted">Shared</small>
                        </div>
                    </div>
                </div>

                <!-- Submit New Article Form -->
                <div class="submission-card">
                    <h4 class="text-primary mb-4">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Submit New Article
                    </h4>
                    
                    <form method="POST" action="core/handleForms.php" enctype="multipart/form-data">
                        <div class="form-group">
                            <label><i class="fas fa-heading mr-2"></i>Title</label>
                            <input type="text" class="form-control" name="title" placeholder="What's your article about?" required>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-tag mr-2"></i>Category</label>
                            <select class="form-control" name="category_id">
                                <option value="">Select category (optional)</option>
                                <?php $categories = $categoryObj->getCategories(); ?>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['category_id']; ?>">
                                        <?php echo htmlspecialchars($category['category_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-align-left mr-2"></i>Content</label>
                            <textarea class="form-control" name="content" rows="5" placeholder="Share your story..." required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-image mr-2"></i>Image (optional)</label>
                            <input type="file" class="form-control-file" name="article_image" accept="image/*">
                            <small class="form-text text-muted">JPG, PNG, GIF, WEBP. Max: 5MB</small>
                        </div>
                        
                        <input type="hidden" name="form_type" value="submit_article">
                        <button type="submit" class="btn btn-primary btn-lg btn-block">
                            <i class="fas fa-paper-plane mr-2"></i>Submit Article
                        </button>
                    </form>
                </div>

                <!-- Category Filters -->
                <div class="submission-card">
                    <h5 class="mb-3">
                        <i class="fas fa-filter mr-2"></i>
                        Filter by Category
                    </h5>
                    
                    <div class="mb-3">
                        <a href="#" class="category-filter active" data-category="all" style="border-color: #007bff; background: #007bff;">
                            <i class="fas fa-list mr-1"></i>All Articles
                        </a>
                        
                        <a href="#" class="category-filter" data-category="uncategorized" style="border-color: #6c757d;">
                            <i class="fas fa-question mr-1"></i>Uncategorized
                        </a>
                        
                        <?php foreach ($categories as $category): ?>
                            <a href="#" class="category-filter" 
                               data-category="<?php echo $category['category_id']; ?>"
                               style="border-color: <?php echo $category['category_color']; ?>;"
                               data-color="<?php echo $category['category_color']; ?>">
                                <i class="fas fa-tag mr-1"></i>
                                <?php echo htmlspecialchars($category['category_name']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Browse Articles for Collaboration -->
                <div class="browse-card" id="browse-articles">
                    <h4 class="mb-3">
                        <i class="fas fa-search mr-2"></i>
                        Find Collaboration Opportunities
                    </h4>
                    <p class="mb-3">Discover articles from other writers and request edit access to collaborate.</p>
                    
                    <button class="btn btn-light" type="button" data-toggle="collapse" data-target="#browseArticles" aria-expanded="false">
                        <i class="fas fa-eye mr-2"></i>Browse Available Articles
                    </button>
                    
                    <div class="collapse mt-3" id="browseArticles">
                        <?php $availableArticles = $articleObj->getArticlesForEditRequest($_SESSION['user_id']); ?>
                        <?php if (empty($availableArticles)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                No articles available for collaboration at the moment.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-dark">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Author</th>
                                            <th>Category</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (array_slice($availableArticles, 0, 8) as $article): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($article['title']); ?></strong>
                                                <?php if (!empty($article['image_path'])): ?>
                                                    <i class="fas fa-image text-info ml-1" title="Has image"></i>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($article['author_username']); ?></td>
                                            <td>
                                                <?php if ($article['category_name']): ?>
                                                    <span class="badge" style="background-color: <?php echo $article['category_color']; ?>;">
                                                        <?php echo htmlspecialchars($article['category_name']); ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">Uncategorized</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-light request-edit-btn" 
                                                        data-article-id="<?php echo $article['article_id']; ?>"
                                                        data-article-title="<?php echo htmlspecialchars($article['title']); ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                
                                <?php if (count($availableArticles) > 8): ?>
                                    <small class="text-light">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Showing 8 of <?php echo count($availableArticles); ?> available articles
                                    </small>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="font-weight-bold">
                        <i class="fas fa-newspaper mr-2 text-primary"></i>
                        My Published Articles
                    </h3>
                    <div>
                        <a href="shared_articles.php" class="btn btn-outline-info btn-sm mr-2">
                            <i class="fas fa-share-alt mr-1"></i>Shared Articles
                        </a>
                        <span class="badge badge-secondary"><?php echo count($myArticles); ?> total</span>
                    </div>
                </div>
                
                <?php if (empty($myArticles)): ?>
                    <div class="no-articles-card">
                        <i class="fas fa-pen-alt fa-5x text-muted mb-4"></i>
                        <h4 class="text-muted mb-3">No Articles Yet</h4>
                        <p class="text-muted mb-4">
                            You haven't submitted any articles yet. Use the form on the left to create your first article and share your story with the school community!
                        </p>
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-lightbulb mr-2 text-warning"></i>
                                            Article Ideas by Category
                                        </h6>
                                        <ul class="text-left small mb-0">
                                            <li><strong>News:</strong> School events, announcements, breaking news</li>
                                            <li><strong>Sports:</strong> Game results, athlete spotlights, team updates</li>
                                            <li><strong>Opinion:</strong> Editorial pieces, student perspectives</li>
                                            <li><strong>Arts & Culture:</strong> Performances, exhibitions, creative works</li>
                                            <li><strong>Academic:</strong> Study tips, educational content, achievements</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div id="articlesContainer">
                        <?php foreach ($myArticles as $article): ?>
                        <div class="card article-card mb-4" 
                             data-category="<?php echo $article['category_id'] ?? 'uncategorized'; ?>">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="flex-grow-1">
                                        <h4 class="mb-2"><?php echo htmlspecialchars($article['title']); ?></h4>
                                        
                                        <!-- Category Badge -->
                                        <?php if ($article['category_name']): ?>
                                            <span class="category-badge mr-2" style="background-color: <?php echo $article['category_color']; ?>;">
                                                <i class="fas fa-tag mr-1"></i>
                                                <?php echo htmlspecialchars($article['category_name']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="category-badge mr-2" style="background-color: #6c757d;">
                                                <i class="fas fa-question mr-1"></i>
                                                Uncategorized
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="text-right">
                                        <?php if ($article['is_active'] == 0): ?>
                                            <span class="badge badge-warning badge-lg">
                                                <i class="fas fa-clock mr-1"></i>PENDING
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-success badge-lg">
                                                <i class="fas fa-check mr-1"></i>PUBLISHED
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <small class="text-muted d-block mb-3">
                                    <i class="fas fa-user mr-1"></i><?php echo htmlspecialchars($article['username']); ?>
                                    <span class="mx-2">|</span>
                                    <i class="fas fa-calendar mr-1"></i><?php echo date('M j, Y g:i A', strtotime($article['created_at'])); ?>
                                </small>
                                
                                <?php if (!empty($article['image_path'])): ?>
                                    <img src="../<?php echo htmlspecialchars($article['image_path']); ?>" 
                                         class="img-fluid mb-3 rounded" 
                                         alt="Article image"
                                         style="max-height: 250px; object-fit: cover; width: 100%;">
                                <?php endif; ?>
                                
                                <p class="mb-3"><?php echo nl2br(htmlspecialchars($article['content'])); ?></p>
                                
                                <!-- Article Actions -->
                                <div class="border-top pt-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <button class="btn btn-primary btn-sm edit-article-btn mb-2" 
                                                    data-article-id="<?php echo $article['article_id']; ?>">
                                                <i class="fas fa-edit mr-1"></i>Edit Article
                                            </button>
                                            
                                            <!-- Show shared editors -->
                                            <?php $sharedEditors = $sharedArticleObj->getArticleEditors($article['article_id']); ?>
                                            <?php if (!empty($sharedEditors)): ?>
                                                <div class="mt-2">
                                                    <small class="text-info d-block mb-2">
                                                        <i class="fas fa-share-alt mr-1"></i>Shared with:
                                                    </small>
                                                    <?php foreach ($sharedEditors as $editor): ?>
                                                        <span class="shared-indicator mr-2 mb-1 d-inline-block">
                                                            <i class="fas fa-user mr-1"></i>
                                                            <?php echo htmlspecialchars($editor['editor_username']); ?>
                                                            <form method="POST" action="core/handleForms.php" class="d-inline ml-2">
                                                                <input type="hidden" name="article_id" value="<?php echo $article['article_id']; ?>">
                                                                <input type="hidden" name="editor_id" value="<?php echo $editor['editor_id']; ?>">
                                                                <button type="submit" name="revokeEditAccess" 
                                                                        class="btn btn-link btn-sm text-white p-0" 
                                                                        onclick="return confirm('Revoke edit access for <?php echo htmlspecialchars($editor['editor_username']); ?>?')" 
                                                                        title="Revoke access">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </form>
                                                        </span>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <form class="deleteArticleForm d-inline">
                                                <input type="hidden" name="article_id" value="<?php echo $article['article_id']; ?>" class="article_id">
                                                <button type="submit" class="btn btn-danger btn-sm deleteArticleBtn">
                                                    <i class="fas fa-trash mr-1"></i>Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Edit Form (Initially Hidden) -->
                                <div class="updateArticleForm d-none mt-4" id="editForm<?php echo $article['article_id']; ?>">
                                    <div class="border-top pt-4 bg-light p-3 rounded">
                                        <h5 class="text-primary mb-3">
                                            <i class="fas fa-edit mr-2"></i>Edit Your Article
                                        </h5>
                                        <form action="core/handleForms.php" method="POST" enctype="multipart/form-data">
                                            <div class="form-group">
                                                <label>Title</label>
                                                <input type="text" class="form-control" name="title" 
                                                       value="<?php echo htmlspecialchars($article['title']); ?>" required>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label>Category</label>
                                                <select class="form-control" name="category_id">
                                                    <option value="">No category</option>
                                                    <?php foreach ($categories as $category): ?>
                                                        <option value="<?php echo $category['category_id']; ?>"
                                                                <?php echo ($article['category_id'] == $category['category_id']) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($category['category_name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label>Content</label>
                                                <textarea name="content" class="form-control" rows="6" required><?php echo htmlspecialchars($article['content']); ?></textarea>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label>Update Image (optional)</label>
                                                <input type="file" class="form-control-file" name="image" accept="image/*">
                                                <?php if (!empty($article['image_path'])): ?>
                                                    <small class="text-muted d-block mt-1">
                                                        <i class="fas fa-info-circle mr-1"></i>
                                                        Current image will be replaced if you upload a new one
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <input type="hidden" name="article_id" value="<?php echo $article['article_id']; ?>">
                                            <div class="d-flex justify-content-between">
                                                <button type="button" class="btn btn-secondary cancel-edit-btn">
                                                    <i class="fas fa-times mr-2"></i>Cancel
                                                </button>
                                                <button type="submit" class="btn btn-success" name="editArticleBtn">
                                                    <i class="fas fa-save mr-2"></i>Save Changes
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Edit Request Modal -->
    <div class="modal fade" id="editRequestModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-edit mr-2"></i>Request Edit Access
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form method="POST" action="core/handleForms.php">
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-handshake mr-2"></i>
                            <strong>Collaboration Request</strong><br>
                            You're requesting edit access to collaborate on this article.
                        </div>
                        
                        <p>Article: <strong id="modalArticleTitle" class="text-primary"></strong></p>
                        
                        <div class="form-group">
                            <label>Message to Author (optional but recommended)</label>
                            <textarea class="form-control" name="message" rows="4" 
                                      placeholder="Introduce yourself and explain why you'd like to collaborate on this article. What perspective or expertise can you bring?"></textarea>
                        </div>
                        <input type="hidden" name="article_id" id="modalArticleId">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </button>
                        <button type="submit" name="createEditRequest" class="btn btn-primary">
                            <i class="fas fa-paper-plane mr-2"></i>Send Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-trash mr-2"></i>Confirm Deletion
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Warning:</strong> This action cannot be undone.
                    </div>
                    <p>Are you sure you want to delete this article? All content and associated data will be permanently removed.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="fas fa-trash mr-2"></i>Delete Article
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentDeleteArticleId = null;

        // Category filtering
        $('.category-filter').on('click', function(e) {
            e.preventDefault();
            $('.category-filter').removeClass('active');
            $(this).addClass('active');
            
            const category = $(this).data('category');
            const color = $(this).data('color') || '#007bff';
            
            // Update active filter styling
            $(this).css('background', color).css('color', 'white');
            $('.category-filter').not(this).css('background', 'white').css('color', '#6c757d');
            
            filterArticlesByCategory(category);
        });

        function filterArticlesByCategory(category) {
            $('.article-card').each(function() {
                const $card = $(this);
                const cardCategory = $card.data('category');
                
                let show = false;
                if (category === 'all') {
                    show = true;
                } else if (category === 'uncategorized') {
                    show = cardCategory === 'uncategorized' || cardCategory === '';
                } else {
                    show = cardCategory == category;
                }
                
                if (show) {
                    $card.fadeIn(300);
                } else {
                    $card.fadeOut(300);
                }
            });
        }

        // Edit article toggle
        $('.edit-article-btn').on('click', function() {
            const articleId = $(this).data('article-id');
            const editForm = $('#editForm' + articleId);
            editForm.toggleClass('d-none');
            
            if (!editForm.hasClass('d-none')) {
                $('html, body').animate({
                    scrollTop: editForm.offset().top - 100
                }, 500);
                
                // Focus on the first input
                editForm.find('input[name="title"]').focus();
            }
        });

        // Cancel edit
        $('.cancel-edit-btn').on('click', function() {
            $(this).closest('.updateArticleForm').addClass('d-none');
        });

        // Delete article with modal confirmation
        $('.deleteArticleForm').on('submit', function(event) {
            event.preventDefault();
            currentDeleteArticleId = $(this).find('.article_id').val();
            $('#deleteModal').modal('show');
        });

        // Confirm delete
        $('#confirmDeleteBtn').on('click', function() {
            if (currentDeleteArticleId) {
                $.ajax({
                    type: "POST",
                    url: "core/handleForms.php",
                    data: {
                        article_id: currentDeleteArticleId,
                        deleteArticleBtn: 1
                    },
                    success: function(data) {
                        $('#deleteModal').modal('hide');
                        if (data) {
                            // Show success message
                            $('body').prepend(`
                                <div class="alert alert-success alert-dismissible fade show" style="position: fixed; top: 20px; right: 20px; z-index: 1050;">
                                    <i class="fas fa-check-circle mr-2"></i>Article deleted successfully!
                                    <button type="button" class="close" data-dismiss="alert">
                                        <span>&times;</span>
                                    </button>
                                </div>
                            `);
                            // Reload after delay
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        } else {
                            alert("Deletion failed. Please try again.");
                        }
                    },
                    error: function() {
                        $('#deleteModal').modal('hide');
                        alert("An error occurred. Please try again.");
                    }
                });
            }
        });

        // Request edit access
        $('.request-edit-btn').on('click', function() {
            const articleId = $(this).data('article-id');
            const articleTitle = $(this).data('article-title');
            
            $('#modalArticleId').val(articleId);
            $('#modalArticleTitle').text(articleTitle);
            $('#editRequestModal').modal('show');
        });
        
        // Form submission loading states
        $('form').on('submit', function() {
            const submitBtn = $(this).find('button[type="submit"]');
            if (!submitBtn.hasClass('deleteArticleBtn')) { // Don't apply to delete buttons
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Processing...');
                submitBtn.prop('disabled', true);
            }
        });
        
        // Auto-hide success messages
        setTimeout(function() {
            $('.alert').not('.modal .alert').alert('close');
        }, 5000);
        
        // Smooth scroll for anchor links
        $('a[href^="#"]').on('click', function(event) {
            var target = $(this.getAttribute('href'));
            if (target.length) {
                event.preventDefault();
                $('html, body').stop().animate({
                    scrollTop: target.offset().top - 100
                }, 1000);
            }
        });

        // Add fade-in animation for article cards
        $(document).ready(function() {
            $('.article-card').each(function(index) {
                $(this).delay(index * 100).fadeIn(600);
            });
        });

        // Show success message for new article submissions
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('success') === '1') {
            $('body').prepend(`
                <div class="alert alert-success alert-dismissible fade show" style="position: fixed; top: 20px; right: 20px; z-index: 1050; max-width: 350px;">
                    <i class="fas fa-check-circle mr-2"></i>
                    <strong>Success!</strong> Your article has been submitted successfully!
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            `);
        } else if (urlParams.get('error') === '1') {
            $('body').prepend(`
                <div class="alert alert-danger alert-dismissible fade show" style="position: fixed; top: 20px; right: 20px; z-index: 1050; max-width: 350px;">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Error!</strong> Failed to submit article. Please try again.
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            `);
        }
    </script>

    <!-- Font Awesome and Bootstrap JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>