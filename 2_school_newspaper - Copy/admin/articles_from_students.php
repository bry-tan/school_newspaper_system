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
        .article-actions {
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
            margin-top: 15px;
        }
        .article-card {
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        .article-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .filter-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        .status-filter {
            display: inline-block;
            margin: 0.25rem;
            padding: 0.5rem 1rem;
            border: 2px solid #dee2e6;
            border-radius: 20px;
            background: white;
            color: #6c757d;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .status-filter.active {
            border-color: #008080;
            background: #008080;
            color: white;
        }
        .status-filter:hover {
            border-color: #008080;
            background: #e8f4f8;
        }
        .quick-actions {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        .stats-row {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        .stat-item {
            text-align: center;
            padding: 1rem;
        }
    </style>
    <title>Manage All Articles - Admin Panel</title>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <!-- Admin Hero Section -->
    <div class="admin-hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 font-weight-bold mb-3">
                        <i class="fas fa-newspaper mr-3"></i>
                        Article Management Center
                    </h1>
                    <p class="lead mb-0">
                        Review, approve, and manage all articles submitted by writers. 
                        Control publication status and maintain content quality.
                    </p>
                </div>
                <div class="col-lg-4 text-center">
                    <i class="fas fa-cogs fa-6x opacity-50"></i>
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
        
        <!-- Statistics Row -->
        <?php 
        $allArticles = $articleObj->getArticles();
        $totalArticles = count($allArticles);
        $pendingArticles = count(array_filter($allArticles, function($a) { return $a['is_active'] == 0; }));
        $publishedArticles = count(array_filter($allArticles, function($a) { return $a['is_active'] == 1; }));
        $adminArticles = count(array_filter($allArticles, function($a) { return $a['is_admin'] == 1; }));
        ?>
        
        <div class="stats-row">
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-item">
                        <i class="fas fa-newspaper fa-3x text-primary mb-3"></i>
                        <h3 class="font-weight-bold"><?php echo $totalArticles; ?></h3>
                        <p class="text-muted mb-0">Total Articles</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <i class="fas fa-clock fa-3x text-warning mb-3"></i>
                        <h3 class="font-weight-bold"><?php echo $pendingArticles; ?></h3>
                        <p class="text-muted mb-0">Pending Review</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h3 class="font-weight-bold"><?php echo $publishedArticles; ?></h3>
                        <p class="text-muted mb-0">Published</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <i class="fas fa-crown fa-3x text-info mb-3"></i>
                        <h3 class="font-weight-bold"><?php echo $adminArticles; ?></h3>
                        <p class="text-muted mb-0">Admin Posts</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <!-- Left Sidebar -->
            <div class="col-lg-4">
                <!-- Quick Actions -->
                <div class="quick-actions">
                    <h4 class="text-primary mb-4">
                        <i class="fas fa-bolt mr-2"></i>
                        Quick Actions
                    </h4>
                    
                    <div class="mb-3">
                        <button class="btn btn-success btn-block" id="approveAllBtn">
                            <i class="fas fa-check-double mr-2"></i>
                            Approve All Pending
                        </button>
                    </div>
                    
                    <div class="mb-3">
                        <button class="btn btn-outline-primary btn-block" onclick="window.print()">
                            <i class="fas fa-print mr-2"></i>
                            Print Article List
                        </button>
                    </div>
                    
                    <div class="mb-3">
                        <a href="articles_submitted.php" class="btn btn-outline-info btn-block">
                            <i class="fas fa-user-edit mr-2"></i>
                            My Admin Articles
                        </a>
                    </div>
                    
                    <div class="mb-3">
                        <a href="notifications.php" class="btn btn-outline-warning btn-block">
                            <i class="fas fa-bell mr-2"></i>
                            View Notifications
                            <?php 
                            $unreadCount = $notificationObj->getUnreadCount($_SESSION['user_id']);
                            if ($unreadCount > 0): 
                            ?>
                                <span class="badge badge-danger"><?php echo $unreadCount; ?></span>
                            <?php endif; ?>
                        </a>
                    </div>
                </div>
                
                <!-- Filter Options -->
                <div class="filter-card">
                    <h5 class="mb-3">
                        <i class="fas fa-filter mr-2"></i>
                        Filter Articles
                    </h5>
                    
                    <div class="mb-3">
                        <span class="status-filter active" data-filter="all">
                            <i class="fas fa-list mr-1"></i>All Articles
                        </span>
                        <span class="status-filter" data-filter="pending">
                            <i class="fas fa-clock mr-1"></i>Pending
                        </span>
                        <span class="status-filter" data-filter="published">
                            <i class="fas fa-check mr-1"></i>Published
                        </span>
                        <span class="status-filter" data-filter="admin">
                            <i class="fas fa-crown mr-1"></i>Admin Posts
                        </span>
                    </div>
                    
                    <div class="form-group">
                        <input type="text" class="form-control" id="searchArticles" placeholder="Search articles...">
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="font-weight-bold">
                        <i class="fas fa-newspaper mr-2 text-primary"></i>
                        All Articles
                    </h3>
                    <div>
                        <div class="btn-group" role="group">
                            <button class="btn btn-outline-secondary btn-sm" id="gridView">
                                <i class="fas fa-th"></i>
                            </button>
                            <button class="btn btn-outline-secondary btn-sm active" id="listView">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div id="articlesContainer">
                    <?php if (empty($allArticles)): ?>
                        <div class="text-center p-5">
                            <i class="fas fa-inbox fa-5x text-muted mb-4"></i>
                            <h4 class="text-muted">No Articles Submitted</h4>
                            <p class="text-muted">No articles have been submitted by writers yet.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($allArticles as $article): ?>
                        <div class="card article-card mb-4" 
                             data-status="<?php echo $article['is_active'] == 1 ? 'published' : 'pending'; ?>"
                             data-type="<?php echo $article['is_admin'] == 1 ? 'admin' : 'writer'; ?>"
                             data-title="<?php echo strtolower($article['title']); ?>"
                             data-author="<?php echo strtolower($article['username']); ?>">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h4 class="mb-0"><?php echo htmlspecialchars($article['title']); ?></h4>
                                    <div class="text-right">
                                        <?php if ($article['is_admin'] == 1): ?>
                                            <span class="badge admin-badge text-white mb-1">
                                                <i class="fas fa-crown mr-1"></i>ADMIN POST
                                            </span><br>
                                        <?php endif; ?>
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
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <small class="text-muted">
                                            <i class="fas fa-user mr-1"></i>
                                            <strong>Author:</strong> <?php echo htmlspecialchars($article['username']); ?>
                                        </small>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar mr-1"></i>
                                            <strong>Created:</strong> <?php echo date('M j, Y g:i A', strtotime($article['created_at'])); ?>
                                        </small>
                                    </div>
                                </div>
                                
                                <?php if (!empty($article['image_path'])): ?>
                                    <div class="mb-3">
                                        <img src="../<?php echo htmlspecialchars($article['image_path']); ?>" 
                                             class="img-fluid rounded" 
                                             alt="Article image"
                                             style="max-height: 200px; object-fit: cover; width: 100%;">
                                    </div>
                                <?php endif; ?>
                                
                                <p class="mb-3">
                                    <?php 
                                    $content = htmlspecialchars($article['content']);
                                    echo strlen($content) > 300 ? substr($content, 0, 300) . '...' : $content;
                                    ?>
                                </p>
                                
                                <!-- Article Actions -->
                                <div class="article-actions">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <!-- Status Control -->
                                            <div class="d-flex align-items-center">
                                                <select name="is_active" class="form-control form-control-sm is_active_select mr-2" 
                                                        article_id="<?php echo $article['article_id']; ?>" style="max-width: 150px;">
                                                    <option value="">Change Status</option>
                                                    <option value="0" <?php echo $article['is_active'] == 0 ? 'selected' : ''; ?>>Pending</option>
                                                    <option value="1" <?php echo $article['is_active'] == 1 ? 'selected' : ''; ?>>Published</option>
                                                </select>
                                                
                                                <button class="btn btn-info btn-sm edit-article-btn mr-2" 
                                                        data-article-id="<?php echo $article['article_id']; ?>">
                                                    <i class="fas fa-edit mr-1"></i>Edit
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-right">
                                            <button class="btn btn-danger btn-sm delete-with-reason-btn" 
                                                    data-article-id="<?php echo $article['article_id']; ?>"
                                                    data-article-title="<?php echo htmlspecialchars($article['title']); ?>"
                                                    data-author-id="<?php echo $article['author_id']; ?>">
                                                <i class="fas fa-trash mr-1"></i>Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Edit Form (Initially Hidden) -->
                                <div class="updateArticleForm d-none mt-4" id="editForm<?php echo $article['article_id']; ?>">
                                    <div class="border-top pt-4 bg-light p-3 rounded">
                                        <h5 class="text-primary mb-3">
                                            <i class="fas fa-edit mr-2"></i>Edit Article
                                        </h5>
                                        <form action="core/handleForms.php" method="POST" enctype="multipart/form-data">
                                            <div class="form-group">
                                                <label>Title</label>
                                                <input type="text" class="form-control" name="title" 
                                                       value="<?php echo htmlspecialchars($article['title']); ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Content</label>
                                                <textarea name="description" class="form-control" rows="6" required><?php echo htmlspecialchars($article['content']); ?></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label>Update Image (optional)</label>
                                                <input type="file" class="form-control-file" name="image" accept="image/*">
                                                <?php if (!empty($article['image_path'])): ?>
                                                    <small class="text-muted">Current image will be replaced if you upload a new one</small>
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
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete with Reason Modal -->
    <div class="modal fade" id="deleteReasonModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-trash mr-2"></i>Delete Article
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Important:</strong> The author will be notified about this deletion.
                    </div>
                    
                    <p>Are you sure you want to delete: <strong id="deleteArticleTitle"></strong>?</p>
                    
                    <div class="form-group">
                        <label>Reason for deletion (will be sent to author)</label>
                        <textarea class="form-control" id="deleteReason" rows="3" 
                                  placeholder="Please explain why this article is being deleted..."></textarea>
                    </div>
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

    <!-- Bulk Approve Modal -->
    <div class="modal fade" id="bulkApproveModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-check-double mr-2"></i>Bulk Approve Articles
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to approve all pending articles? This action will:</p>
                    <ul>
                        <li>Publish all pending articles</li>
                        <li>Make them visible to the public</li>
                        <li>Notify all authors about the approval</li>
                    </ul>
                    <p><strong>Total pending articles:</strong> <span id="pendingCount"><?php echo $pendingArticles; ?></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirmBulkApprove">
                        <i class="fas fa-check-double mr-2"></i>Approve All
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentDeleteArticleId = null;

        // Filter functionality
        $('.status-filter').on('click', function() {
            $('.status-filter').removeClass('active');
            $(this).addClass('active');
            
            const filter = $(this).data('filter');
            filterArticles(filter);
        });

        function filterArticles(filter) {
            $('.article-card').each(function() {
                const $card = $(this);
                let show = true;

                if (filter === 'pending') {
                    show = $card.data('status') === 'pending';
                } else if (filter === 'published') {
                    show = $card.data('status') === 'published';
                } else if (filter === 'admin') {
                    show = $card.data('type') === 'admin';
                }
                
                if (show) {
                    $card.fadeIn(300);
                } else {
                    $card.fadeOut(300);
                }
            });
        }

        // Search functionality
        $('#searchArticles').on('input', function() {
            const searchTerm = $(this).val().toLowerCase();
            
            $('.article-card').each(function() {
                const $card = $(this);
                const title = $card.data('title');
                const author = $card.data('author');
                
                if (title.includes(searchTerm) || author.includes(searchTerm)) {
                    $card.fadeIn(300);
                } else {
                    $card.fadeOut(300);
                }
            });
        });

        // Edit article toggle
        $('.edit-article-btn').on('click', function() {
            const articleId = $(this).data('article-id');
            const editForm = $('#editForm' + articleId);
            editForm.toggleClass('d-none');
            
            if (!editForm.hasClass('d-none')) {
                $('html, body').animate({
                    scrollTop: editForm.offset().top - 100
                }, 500);
            }
        });

        // Cancel edit
        $('.cancel-edit-btn').on('click', function() {
            $(this).closest('.updateArticleForm').addClass('d-none');
        });

        // Article visibility update
        $('.is_active_select').on('change', function(event) {
            event.preventDefault();
            const formData = {
                article_id: $(this).attr('article_id'),
                status: $(this).val(),
                updateArticleVisibility: 1
            };

            if (formData.article_id && formData.status !== '') {
                $.ajax({
                    type: "POST",
                    url: "core/handleForms.php",
                    data: formData,
                    success: function(data) {
                        if (data) {
                            location.reload();
                        } else {
                            alert("Status update failed");
                        }
                    }
                });
            }
        });

        // Delete with reason
        $('.delete-with-reason-btn').on('click', function() {
            currentDeleteArticleId = $(this).data('article-id');
            const articleTitle = $(this).data('article-title');
            
            $('#deleteArticleTitle').text(articleTitle);
            $('#deleteReason').val('');
            $('#deleteReasonModal').modal('show');
        });

        // Confirm delete
        $('#confirmDeleteBtn').on('click', function() {
            const reason = $('#deleteReason').val().trim();
            
            if (!reason) {
                alert('Please provide a reason for deletion.');
                return;
            }
            
            $.ajax({
                type: "POST",
                url: "core/handleForms.php",
                data: {
                    article_id: currentDeleteArticleId,
                    deleteArticleBtn: 1,
                    delete_reason: reason
                },
                success: function(data) {
                    $('#deleteReasonModal').modal('hide');
                    if (data) {
                        location.reload();
                    } else {
                        alert("Deletion failed");
                    }
                }
            });
        });

        // Bulk approve functionality
        $('#approveAllBtn').on('click', function() {
            $('#bulkApproveModal').modal('show');
        });

        $('#confirmBulkApprove').on('click', function() {
            // This would need to be implemented in handleForms.php
            $.ajax({
                type: "POST",
                url: "core/handleForms.php",
                data: { bulkApproveArticles: 1 },
                success: function(data) {
                    $('#bulkApproveModal').modal('hide');
                    if (data) {
                        location.reload();
                    } else {
                        alert("Bulk approval failed");
                    }
                }
            });
        });

        // View toggles (placeholder for future grid view)
        $('#listView').on('click', function() {
            $('.btn-group button').removeClass('active');
            $(this).addClass('active');
        });

        $('#gridView').on('click', function() {
            $('.btn-group button').removeClass('active');
            $(this).addClass('active');
            // Grid view implementation would go here
        });
    </script>

    <!-- Font Awesome and Bootstrap JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>