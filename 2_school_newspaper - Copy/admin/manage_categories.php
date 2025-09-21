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
        .category-card {
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        .category-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        .category-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            color: white;
            font-weight: bold;
            text-align: center;
            margin-bottom: 1rem;
        }
        .form-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            text-align: center;
        }
        .color-picker-container {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-top: 0.5rem;
        }
        .color-option {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: 3px solid transparent;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .color-option.selected {
            border-color: #333;
            transform: scale(1.2);
        }
    </style>
    <title>Category Management - Admin Panel</title>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <!-- Admin Hero Section -->
    <div class="admin-hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 font-weight-bold mb-3">
                        <i class="fas fa-tags mr-3"></i>
                        Category Management
                    </h1>
                    <p class="lead mb-0">
                        Organize your school publication by creating and managing article categories. 
                        Help readers find content that interests them most.
                    </p>
                </div>
                <div class="col-lg-4 text-center">
                    <i class="fas fa-sitemap fa-6x opacity-50"></i>
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
        <?php $categoryStats = $categoryObj->getCategoryStats(); ?>
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card">
                    <i class="fas fa-tags fa-3x text-primary mb-3"></i>
                    <h3 class="font-weight-bold"><?php echo $categoryStats['total_categories']; ?></h3>
                    <p class="text-muted mb-0">Total Categories</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <i class="fas fa-eye fa-3x text-success mb-3"></i>
                    <h3 class="font-weight-bold"><?php echo $categoryStats['active_categories']; ?></h3>
                    <p class="text-muted mb-0">Active Categories</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <i class="fas fa-newspaper fa-3x text-info mb-3"></i>
                    <h3 class="font-weight-bold"><?php echo $categoryStats['categorized_articles']; ?></h3>
                    <p class="text-muted mb-0">Categorized Articles</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <i class="fas fa-question-circle fa-3x text-warning mb-3"></i>
                    <h3 class="font-weight-bold"><?php echo $categoryStats['uncategorized_articles']; ?></h3>
                    <p class="text-muted mb-0">Uncategorized</p>
                </div>
            </div>
        </div>
        
        <div class="row">
            <!-- Left Sidebar - Create Category -->
            <div class="col-lg-4">
                <div class="form-card">
                    <h4 class="text-primary mb-4">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Create New Category
                    </h4>
                    
                    <form action="core/handleForms.php" method="POST" id="categoryForm">
                        <div class="form-group">
                            <label><i class="fas fa-palette mr-2"></i>Category Color</label>
                            <input type="color" class="form-control" name="category_color" value="#007bff" id="colorInput">
                            <div class="color-picker-container">
                                <div class="color-option selected" style="background: #007bff;" data-color="#007bff"></div>
                                <div class="color-option" style="background: #28a745;" data-color="#28a745"></div>
                                <div class="color-option" style="background: #dc3545;" data-color="#dc3545"></div>
                                <div class="color-option" style="background: #ffc107;" data-color="#ffc107"></div>
                                <div class="color-option" style="background: #17a2b8;" data-color="#17a2b8"></div>
                                <div class="color-option" style="background: #6f42c1;" data-color="#6f42c1"></div>
                                <div class="color-option" style="background: #e83e8c;" data-color="#e83e8c"></div>
                                <div class="color-option" style="background: #fd7e14;" data-color="#fd7e14"></div>
                            </div>
                        </div>
                        
                        <button type="submit" name="createCategoryBtn" class="btn btn-primary btn-lg btn-block">
                            <i class="fas fa-plus mr-2"></i>Create Category
                        </button>
                    </form>
                </div>
                
                <!-- Quick Actions -->
                <div class="form-card">
                    <h5 class="mb-3">
                        <i class="fas fa-bolt mr-2"></i>
                        Quick Actions
                    </h5>
                    
                    <button class="btn btn-outline-success btn-block mb-2" id="enableAllCategories">
                        <i class="fas fa-eye mr-2"></i>Enable All Categories
                    </button>
                    
                    <button class="btn btn-outline-warning btn-block mb-2" onclick="window.location.href='articles_from_students.php'">
                        <i class="fas fa-newspaper mr-2"></i>Manage Articles
                    </button>
                    
                    <button class="btn btn-outline-info btn-block" onclick="window.print()">
                        <i class="fas fa-print mr-2"></i>Print Category List
                    </button>
                </div>
            </div>
            
            <!-- Main Content - Categories List -->
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="font-weight-bold">
                        <i class="fas fa-list mr-2 text-primary"></i>
                        All Categories
                    </h3>
                    <div>
                        <input type="text" class="form-control" id="searchCategories" placeholder="Search categories..." style="width: 250px;">
                    </div>
                </div>
                
                <div id="categoriesContainer">
                    <?php $categories = $categoryObj->getCategories(false); // Get all categories including inactive ?>
                    <?php if (empty($categories)): ?>
                        <div class="text-center p-5">
                            <i class="fas fa-tags fa-5x text-muted mb-4"></i>
                            <h4 class="text-muted">No Categories Created</h4>
                            <p class="text-muted">Create your first category using the form on the left.</p>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($categories as $category): ?>
                            <div class="col-md-6 mb-4 category-item" data-name="<?php echo strtolower($category['category_name']); ?>">
                                <div class="card category-card h-100">
                                    <div class="card-body">
                                        <div class="category-badge" style="background-color: <?php echo $category['category_color']; ?>;">
                                            <i class="fas fa-tag mr-2"></i>
                                            <?php echo htmlspecialchars($category['category_name']); ?>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <small class="text-muted">
                                                <i class="fas fa-newspaper mr-1"></i>
                                                <strong><?php echo $category['article_count']; ?></strong> articles
                                            </small>
                                            <span class="mx-2">|</span>
                                            <small class="text-muted">
                                                <i class="fas fa-user mr-1"></i>
                                                Created by <?php echo htmlspecialchars($category['created_by_name']); ?>
                                            </small>
                                        </div>
                                        
                                        <?php if ($category['category_description']): ?>
                                            <p class="text-muted small mb-3">
                                                <?php echo htmlspecialchars($category['category_description']); ?>
                                            </p>
                                        <?php endif; ?>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <?php if ($category['is_active']): ?>
                                                    <span class="badge badge-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">Inactive</span>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-outline-primary edit-category-btn"
                                                        data-category-id="<?php echo $category['category_id']; ?>"
                                                        data-name="<?php echo htmlspecialchars($category['category_name']); ?>"
                                                        data-description="<?php echo htmlspecialchars($category['category_description']); ?>"
                                                        data-color="<?php echo $category['category_color']; ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                
                                                <button class="btn btn-sm btn-outline-<?php echo $category['is_active'] ? 'warning' : 'success'; ?> toggle-category-btn"
                                                        data-category-id="<?php echo $category['category_id']; ?>"
                                                        data-status="<?php echo $category['is_active'] ? '0' : '1'; ?>">
                                                    <i class="fas fa-<?php echo $category['is_active'] ? 'eye-slash' : 'eye'; ?>"></i>
                                                </button>
                                                
                                                <?php if ($category['article_count'] == 0): ?>
                                                <button class="btn btn-sm btn-outline-danger delete-category-btn"
                                                        data-category-id="<?php echo $category['category_id']; ?>"
                                                        data-name="<?php echo htmlspecialchars($category['category_name']); ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-3">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar mr-1"></i>
                                                Created: <?php echo date('M j, Y', strtotime($category['created_at'])); ?>
                                            </small>
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
    </div>

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-edit mr-2"></i>Edit Category
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form method="POST" action="core/handleForms.php">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Category Name</label>
                            <input type="text" class="form-control" name="category_name" id="editCategoryName" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" name="category_description" id="editCategoryDescription" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Category Color</label>
                            <input type="color" class="form-control" name="category_color" id="editCategoryColor">
                        </div>
                        <input type="hidden" name="category_id" id="editCategoryId">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" name="updateCategoryBtn" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Category Modal -->
    <div class="modal fade" id="deleteCategoryModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-trash mr-2"></i>Delete Category
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
                    <p>Are you sure you want to delete the category: <strong id="deleteCategoryName"></strong>?</p>
                    <p><small class="text-muted">Note: Articles in this category will become uncategorized.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteCategory">
                        <i class="fas fa-trash mr-2"></i>Delete Category
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentDeleteCategoryId = null;

        // Color picker functionality
        $('.color-option').on('click', function() {
            $('.color-option').removeClass('selected');
            $(this).addClass('selected');
            $('#colorInput').val($(this).data('color'));
        });

        // Custom color input
        $('#colorInput').on('change', function() {
            $('.color-option').removeClass('selected');
        });

        // Search categories
        $('#searchCategories').on('input', function() {
            const searchTerm = $(this).val().toLowerCase();
            $('.category-item').each(function() {
                const categoryName = $(this).data('name');
                if (categoryName.includes(searchTerm)) {
                    $(this).fadeIn(300);
                } else {
                    $(this).fadeOut(300);
                }
            });
        });

        // Edit category
        $('.edit-category-btn').on('click', function() {
            const categoryId = $(this).data('category-id');
            const name = $(this).data('name');
            const description = $(this).data('description');
            const color = $(this).data('color');

            $('#editCategoryId').val(categoryId);
            $('#editCategoryName').val(name);
            $('#editCategoryDescription').val(description);
            $('#editCategoryColor').val(color);
            $('#editCategoryModal').modal('show');
        });

        // Toggle category status
        $('.toggle-category-btn').on('click', function() {
            const categoryId = $(this).data('category-id');
            const newStatus = $(this).data('status');

            $.ajax({
                type: "POST",
                url: "core/handleForms.php",
                data: {
                    toggleCategoryStatus: 1,
                    category_id: categoryId,
                    is_active: newStatus
                },
                success: function(data) {
                    if (data) {
                        location.reload();
                    } else {
                        alert("Failed to update category status");
                    }
                }
            });
        });

        // Delete category
        $('.delete-category-btn').on('click', function() {
            currentDeleteCategoryId = $(this).data('category-id');
            const categoryName = $(this).data('name');
            $('#deleteCategoryName').text(categoryName);
            $('#deleteCategoryModal').modal('show');
        });

        // Confirm delete category
        $('#confirmDeleteCategory').on('click', function() {
            $.ajax({
                type: "POST",
                url: "core/handleForms.php",
                data: {
                    deleteCategoryBtn: 1,
                    category_id: currentDeleteCategoryId
                },
                success: function(data) {
                    $('#deleteCategoryModal').modal('hide');
                    if (data) {
                        location.reload();
                    } else {
                        alert("Failed to delete category");
                    }
                }
            });
        });

        // Enable all categories
        $('#enableAllCategories').on('click', function() {
            if (confirm('Enable all inactive categories?')) {
                $.ajax({
                    type: "POST",
                    url: "core/handleForms.php",
                    data: { enableAllCategories: 1 },
                    success: function(data) {
                        if (data) {
                            location.reload();
                        } else {
                            alert("Failed to enable categories");
                        }
                    }
                });
            }
        });

        // Form validation
        $('#categoryForm').on('submit', function(e) {
            const categoryName = $('input[name="category_name"]').val().trim();
            if (categoryName.length < 2) {
                e.preventDefault();
                alert('Category name must be at least 2 characters long.');
                return false;
            }
        });

        // Add loading state to forms
        $('form').on('submit', function() {
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Processing...');
            submitBtn.prop('disabled', true);
        });
    </script>

    <!-- Font Awesome and Bootstrap JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>">
                            <label><i class="fas fa-tag mr-2"></i>Category Name</label>
                            <input type="text" class="form-control" name="category_name" 
                                   placeholder="e.g., Sports, News, Opinion..." required>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-align-left mr-2"></i>Description</label>
                            <textarea class="form-control" name="category_description" rows="3" 
                                      placeholder="Brief description of this category..."></textarea>
                        </div>
                        
                        <div class="form-group