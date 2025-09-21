<?php require_once 'writer/classloader.php'; ?>
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
            padding: 4rem 0;
        }
        .card-link {
            text-decoration: none;
            color: inherit;
            display: block;
            transition: transform 0.3s ease;
        }
        .card-link:hover {
            color: inherit;
            text-decoration: none;
            transform: translateY(-5px);
        }
        .card-link:hover .card {
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .article-card {
            transition: transform 0.2s ease;
        }
        .article-card:hover {
            transform: translateY(-2px);
        }
        .admin-badge {
            background: linear-gradient(45deg, #007bff, #28a745);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .category-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: bold;
            color: white;
        }
        .article-image {
            height: 200px;
            object-fit: cover;
            border-radius: 0.5rem;
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
        .categories-section {
            background: white;
            padding: 3rem 0;
            margin: 2rem 0;
        }
    </style>
    <title>School Publication - Homepage</title>
</head>
<body>
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 font-weight-bold mb-4">
                        <i class="fas fa-newspaper"></i> School Publication
                    </h1>
                    <p class="lead mb-4">
                        Welcome to our digital newspaper platform where students and administrators 
                        collaborate to share news, stories, and insights from our school community.
                    </p>
                    <div class="mb-3">
                        <span class="badge badge-light mr-2">
                            <i class="fas fa-users"></i> Collaborative Writing
                        </span>
                        <span class="badge badge-light mr-2">
                            <i class="fas fa-images"></i> Rich Media Support
                        </span>
                        <span class="badge badge-light mr-2">
                            <i class="fas fa-tags"></i> Organized Categories
                        </span>
                        <span class="badge badge-light">
                            <i class="fas fa-bell"></i> Real-time Notifications
                        </span>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <i class="fas fa-graduation-cap fa-10x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Cards -->
    <div class="container my-5">
        <div class="row">
            <div class="col-md-6 mb-4">
                <a href="writer/index.php" class="card-link">
                    <div class="card shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-pen fa-4x text-primary"></i>
                            </div>
                            <h3 class="card-title">Writer Portal</h3>
                            <p class="card-text">
                                Join as a writer to create engaging articles, choose from various categories, 
                                collaborate with peers, and contribute to our school's digital newspaper.
                            </p>
                            <div class="mt-3">
                                <span class="badge badge-primary">Article Creation</span>
                                <span class="badge badge-info">Image Upload</span>
                                <span class="badge badge-success">Categories</span>
                                <span class="badge badge-warning">Collaboration</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 mb-4">
                <a href="admin/index.php" class="card-link">
                    <div class="card shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-user-shield fa-4x text-success"></i>
                            </div>
                            <h3 class="card-title">Admin Portal</h3>
                            <p class="card-text">
                                Administrative access for managing articles, creating categories, 
                                moderating content, and overseeing the publication process.
                            </p>
                            <div class="mt-3">
                                <span class="badge badge-warning">Content Management</span>
                                <span class="badge badge-danger">Moderation</span>
                                <span class="badge badge-info">Category Management</span>
                                <span class="badge badge-secondary">Analytics</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Categories Section -->
    <div class="categories-section">
        <div class="container">
            <div class="text-center mb-4">
                <h2 class="font-weight-bold text-dark">Browse by Category</h2>
                <p class="text-muted">Discover articles organized by topics that interest you</p>
            </div>
            
            <div class="text-center mb-4">
                <a href="#" class="category-filter active" data-category="all" style="border-color: #007bff; background: #007bff;">
                    <i class="fas fa-list mr-1"></i>All Articles
                </a>
                
                <?php $categories = $categoryObj->getCategories(); ?>
                <?php foreach ($categories as $category): ?>
                    <a href="#" class="category-filter" 
                       data-category="<?php echo $category['category_id']; ?>"
                       style="border-color: <?php echo $category['category_color']; ?>;"
                       data-color="<?php echo $category['category_color']; ?>">
                        <i class="fas fa-tag mr-1"></i>
                        <?php echo htmlspecialchars($category['category_name']); ?>
                        <span class="badge badge-light ml-1"><?php echo $category['article_count']; ?></span>
                    </a>
                <?php endforeach; ?>
                
                <a href="#" class="category-filter" data-category="uncategorized" style="border-color: #6c757d;" data-color="#6c757d">
                    <i class="fas fa-question mr-1"></i>Uncategorized
                </a>
            </div>
        </div>
    </div>

    <!-- Articles Section -->
    <div class="container-fluid py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 font-weight-bold text-dark">Latest Articles</h2>
                <p class="lead text-muted">Discover the latest stories and news from our school community</p>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <?php $articles = $articleObj->getActiveArticles(); ?>
                    <?php if (empty($articles)): ?>
                        <div class="alert alert-info text-center">
                            <div class="mb-3">
                                <i class="fas fa-newspaper fa-3x text-muted"></i>
                            </div>
                            <h4>No Articles Yet</h4>
                            <p class="mb-0">Be the first to contribute to our school publication!</p>
                        </div>
                    <?php else: ?>
                        <div class="row" id="articlesContainer">
                            <?php foreach ($articles as $index => $article): ?>
                            <div class="col-md-6 mb-4 article-item" 
                                 data-category="<?php echo $article['category_id'] ?? 'uncategorized'; ?>">
                                <div class="card article-card shadow-sm h-100">
                                    <?php if (!empty($article['image_path'])): ?>
                                        <img src="<?php echo htmlspecialchars($article['image_path']); ?>" 
                                             class="card-img-top article-image" 
                                             alt="<?php echo htmlspecialchars($article['title']); ?>">
                                    <?php endif; ?>
                                    
                                    <div class="card-body d-flex flex-column">
                                        <div class="mb-3">
                                            <?php if ($article['is_admin'] == 1): ?>
                                                <span class="admin-badge mb-2 d-inline-block">
                                                    <i class="fas fa-crown mr-1"></i>Admin Post
                                                </span>
                                            <?php endif; ?>
                                            
                                            <!-- Category Badge -->
                                            <?php if ($article['category_name']): ?>
                                                <span class="category-badge" style="background-color: <?php echo $article['category_color']; ?>;">
                                                    <i class="fas fa-tag mr-1"></i>
                                                    <?php echo htmlspecialchars($article['category_name']); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="category-badge" style="background-color: #6c757d;">
                                                    <i class="fas fa-question mr-1"></i>
                                                    Uncategorized
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <h4 class="card-title font-weight-bold">
                                            <?php echo htmlspecialchars($article['title']); ?>
                                        </h4>
                                        
                                        <p class="card-text flex-grow-1">
                                            <?php 
                                            $content = htmlspecialchars($article['content']);
                                            echo strlen($content) > 200 ? substr($content, 0, 200) . '...' : $content;
                                            ?>
                                        </p>
                                        
                                        <div class="card-text">
                                            <small class="text-muted d-flex justify-content-between align-items-center">
                                                <span>
                                                    <i class="fas fa-user"></i>
                                                    <strong><?php echo htmlspecialchars($article['username']); ?></strong>
                                                </span>
                                                <span>
                                                    <i class="fas fa-calendar"></i>
                                                    <?php echo date('M j, Y', strtotime($article['created_at'])); ?>
                                                </span>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Load More Button -->
                        <?php if (count($articles) > 8): ?>
                        <div class="text-center mt-4">
                            <button class="btn btn-outline-primary btn-lg" id="loadMoreBtn">
                                <i class="fas fa-plus"></i> Load More Articles
                            </button>
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="container-fluid bg-light py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h3 class="font-weight-bold">Platform Features</h3>
            </div>
            <div class="row">
                <div class="col-md-3 text-center mb-4">
                    <div class="mb-3">
                        <i class="fas fa-tags fa-3x text-primary"></i>
                    </div>
                    <h5>Organized Categories</h5>
                    <p class="text-muted">Articles are organized into categories like News, Sports, Opinion, and more for easy browsing.</p>
                </div>
                <div class="col-md-3 text-center mb-4">
                    <div class="mb-3">
                        <i class="fas fa-edit fa-3x text-success"></i>
                    </div>
                    <h5>Collaborative Editing</h5>
                    <p class="text-muted">Writers can request edit access to collaborate on articles with approval from authors.</p>
                </div>
                <div class="col-md-3 text-center mb-4">
                    <div class="mb-3">
                        <i class="fas fa-bell fa-3x text-info"></i>
                    </div>
                    <h5>Smart Notifications</h5>
                    <p class="text-muted">Stay updated with real-time notifications for article approvals, edit requests, and deletions.</p>
                </div>
                <div class="col-md-3 text-center mb-4">
                    <div class="mb-3">
                        <i class="fas fa-images fa-3x text-warning"></i>
                    </div>
                    <h5>Rich Media Support</h5>
                    <p class="text-muted">Enhance your articles with images and multimedia content for engaging storytelling.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <div class="row">
                <div class="col-12">
                    <h5><i class="fas fa-graduation-cap"></i> School Publication System</h5>
                    <p class="text-muted mb-0">Empowering student voices through digital journalism with organized categories</p>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Category filtering functionality
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
            $('.article-item').each(function() {
                const $item = $(this);
                const itemCategory = $item.data('category');
                
                let show = false;
                if (category === 'all') {
                    show = true;
                } else if (category === 'uncategorized') {
                    show = itemCategory === 'uncategorized' || itemCategory === '';
                } else {
                    show = itemCategory == category;
                }
                
                if (show) {
                    $item.fadeIn(300);
                } else {
                    $item.fadeOut(300);
                }
            });
        }

        // Smooth scrolling for any anchor links
        $('a[href^="#"]').on('click', function(event) {
            var target = $(this.getAttribute('href'));
            if (target.length) {
                event.preventDefault();
                $('html, body').stop().animate({
                    scrollTop: target.offset().top - 100
                }, 1000);
            }
        });

        // Add some visual enhancements
        $(document).ready(function() {
            // Fade in articles on scroll
            $(window).scroll(function() {
                $('.article-card').each(function() {
                    var elementTop = $(this).offset().top;
                    var elementBottom = elementTop + $(this).outerHeight();
                    var viewportTop = $(window).scrollTop();
                    var viewportBottom = viewportTop + $(window).height();
                    
                    if (elementBottom > viewportTop && elementTop < viewportBottom) {
                        $(this).addClass('animate__fadeInUp');
                    }
                });
            });

            // Initialize with all articles shown
            filterArticlesByCategory('all');
        });
    </script>

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>