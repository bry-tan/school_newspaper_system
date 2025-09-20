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
        }
        .article-actions {
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
            margin-top: 15px;
        }
    </style>
    <title>My Articles - Writer Panel</title>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container-fluid">
        <?php
        if (isset($_SESSION['message'])) {
            $alertClass = $_SESSION['status'] == '200' ? 'alert-success' : 'alert-danger';
            echo "<div class='alert {$alertClass} alert-dismissible fade show' role='alert'>
                    {$_SESSION['message']}
                    <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                        <span aria-hidden='true'>&times;</span>
                    </button>
                  </div>";
            unset($_SESSION['message']);
            unset($_SESSION['status']);
        }
        ?>
        
        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Submit New Article Form -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-plus"></i> Submit New Article</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="core/handleForms.php" enctype="multipart/form-data">
                            <div class="form-group">
                                <label><i class="fas fa-heading"></i> Title</label>
                                <input type="text" class="form-control" name="title" placeholder="Enter article title..." required>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-align-left"></i> Content</label>
                                <textarea class="form-control" name="content" rows="5" placeholder="Write your article content..." required></textarea>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-image"></i> Image (optional)</label>
                                <input type="file" class="form-control-file" name="article_image" accept="image/*">
                                <small class="form-text text-muted">Supported formats: JPG, JPEG, PNG, GIF, WEBP. Max size: 5MB</small>
                            </div>
                            <input type="hidden" name="form_type" value="submit_article">
                            <button type="submit" class="btn btn-primary btn-lg btn-block">
                                <i class="fas fa-paper-plane"></i> Submit Article
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Browse Articles for Edit Requests -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-search"></i> Browse Articles</h4>
                        <small>Request edit access to collaborate on other writers' articles</small>
                    </div>
                    <div class="card-body">
                        <button class="btn btn-outline-info" type="button" data-toggle="collapse" data-target="#browseArticles" aria-expanded="false">
                            <i class="fas fa-eye"></i> View Available Articles
                        </button>
                        
                        <div class="collapse mt-3" id="browseArticles">
                            <?php $availableArticles = $articleObj->getArticlesForEditRequest($_SESSION['user_id']); ?>
                            <?php if (empty($availableArticles)): ?>
                                <div class="alert alert-info">No articles available for edit requests.</div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Title</th>
                                                <th>Author</th>
                                                <th>Created</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($availableArticles as $article): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($article['title']); ?></strong>
                                                    <?php if (!empty($article['image_path'])): ?>
                                                        <i class="fas fa-image text-muted ml-1" title="Has image"></i>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($article['author_username']); ?></td>
                                                <td><?php echo date('M j, Y', strtotime($article['created_at'])); ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary request-edit-btn" 
                                                            data-article-id="<?php echo $article['article_id']; ?>"
                                                            data-article-title="<?php echo htmlspecialchars($article['title']); ?>">
                                                        <i class="fas fa-edit"></i> Request Edit
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- My Articles -->
                <div class="display-4 text-center mb-4">My Articles</div>
                
                <?php $articles = $articleObj->getArticlesByUserID($_SESSION['user_id']); ?>
                <?php if (empty($articles)): ?>
                    <div class="alert alert-info text-center">
                        <h4>No Articles Yet</h4>
                        <p>You haven't submitted any articles yet. Use the form above to submit your first article!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($articles as $article): ?>
                    <div class="card mt-4 shadow articleCard">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h2 class="mb-0"><?php echo htmlspecialchars($article['title']); ?></h2>
                                <div class="text-right">
                                    <?php if ($article['is_active'] == 0): ?>
                                        <span class="badge badge-warning badge-lg">PENDING</span>
                                    <?php else: ?>
                                        <span class="badge badge-success badge-lg">PUBLISHED</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <small class="text-muted">
                                <i class="fas fa-user"></i> <?php echo htmlspecialchars($article['username']); ?> | 
                                <i class="fas fa-calendar"></i> <?php echo $article['created_at']; ?>
                            </small>
                            
                            <?php if (!empty($article['image_path'])): ?>
                                <img src="../<?php echo htmlspecialchars($article['image_path']); ?>" 
                                     class="img-fluid mb-3 mt-3" 
                                     alt="Article image"
                                     style="max-height: 300px; object-fit: cover; width: 100%;">
                            <?php endif; ?>
                            
                            <p class="mt-3"><?php echo nl2br(htmlspecialchars($article['content'])); ?></p>
                            
                            <!-- Article Actions -->
                            <div class="article-actions">
                                <div class="row">
                                    <div class="col-md-6">
                                        <button class="btn btn-primary btn-sm edit-article-btn mb-2" 
                                                data-article-id="<?php echo $article['article_id']; ?>">
                                            <i class="fas fa-edit"></i> Edit Article
                                        </button>
                                        
                                        <!-- Show shared editors -->
                                        <?php $sharedEditors = $sharedArticleObj->getArticleEditors($article['article_id']); ?>
                                        <?php if (!empty($sharedEditors)): ?>
                                            <div class="mt-2">
                                                <small class="text-info">
                                                    <i class="fas fa-share-alt"></i> Shared with: 
                                                    <?php foreach ($sharedEditors as $editor): ?>
                                                        <span class="badge badge-info mr-1">
                                                            <?php echo htmlspecialchars($editor['editor_username']); ?>
                                                            <form method="POST" action="core/handleForms.php" class="d-inline">
                                                                <input type="hidden" name="article_id" value="<?php echo $article['article_id']; ?>">
                                                                <input type="hidden" name="editor_id" value="<?php echo $editor['editor_id']; ?>">
                                                                <button type="submit" name="revokeEditAccess" class="btn btn-link btn-sm text-white p-0 ml-1" 
                                                                        onclick="return confirm('Revoke edit access for this user?')" 
                                                                        title="Revoke access">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </form>
                                                        </span>
                                                    <?php endforeach; ?>
                                                </small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <form class="deleteArticleForm d-inline">
                                            <input type="hidden" name="article_id" value="<?php echo $article['article_id']; ?>" class="article_id">
                                            <button type="submit" class="btn btn-danger btn-sm deleteArticleBtn">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Edit Form (Initially Hidden) -->
                            <div class="updateArticleForm d-none mt-4" id="editForm<?php echo $article['article_id']; ?>">
                                <div class="border-top pt-4">
                                    <h5 class="text-primary"><i class="fas fa-edit"></i> Edit Article</h5>
                                    <form action="core/handleForms.php" method="POST" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label>Title</label>
                                            <input type="text" class="form-control" name="title" 
                                                   value="<?php echo htmlspecialchars($article['title']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Content</label>
                                            <textarea name="content" class="form-control" rows="6" required><?php echo htmlspecialchars($article['content']); ?></textarea>
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
                                            <button type="button" class="btn btn-secondary cancel-edit-btn">Cancel</button>
                                            <button type="submit" class="btn btn-success" name="editArticleBtn">
                                                <i class="fas fa-save"></i> Save Changes
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

    <!-- Edit Request Modal -->
    <div class="modal fade" id="editRequestModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Request Edit Access</h5>
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
                                      placeholder="Explain why you'd like to edit this article..."></textarea>
                        </div>
                        <input type="hidden" name="article_id" id="modalArticleId">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" name="createEditRequest" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Send Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
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

        // Delete article
        $('.deleteArticleForm').on('submit', function(event) {
            event.preventDefault();
            var formData = {
                article_id: $(this).find('.article_id').val(),
                deleteArticleBtn: 1
            }
            if (confirm("Are you sure you want to delete this article?")) {
                $.ajax({
                    type: "POST",
                    url: "core/handleForms.php",
                    data: formData,
                    success: function(data) {
                        if (data) {
                            location.reload();
                        } else {
                            alert("Deletion failed");
                        }
                    }
                })
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
    </script>

    <!-- Font Awesome and Bootstrap JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>