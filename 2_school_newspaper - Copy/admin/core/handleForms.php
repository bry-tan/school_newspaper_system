<?php  
require_once '../classloader.php';

// User registration
if (isset($_POST['insertNewUserBtn'])) {
    $username = htmlspecialchars(trim($_POST['username']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (!empty($username) && !empty($email) && !empty($password) && !empty($confirm_password)) {
        if ($password == $confirm_password) {
            if (!$userObj->usernameExists($username)) {
                $is_admin = isset($_POST['is_admin']) ? true : false;
                if ($userObj->registerUser($username, $email, $password, $is_admin)) {
                    header("Location: ../login.php");
                    exit;
                } else {
                    $_SESSION['message'] = "An error occurred with the query!";
                    $_SESSION['status'] = '400';
                    header("Location: ../register.php");
                    exit;
                }
            } else {
                $_SESSION['message'] = $username . " as username is already taken";
                $_SESSION['status'] = '400';
                header("Location: ../register.php");
                exit;
            }
        } else {
            $_SESSION['message'] = "Please make sure both passwords are equal";
            $_SESSION['status'] = '400';
            header("Location: ../register.php");
            exit;
        }
    } else {
        $_SESSION['message'] = "Please make sure there are no empty input fields";
        $_SESSION['status'] = '400';
        header("Location: ../register.php");
        exit;
    }
}

// User login
if (isset($_POST['loginUserBtn'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        if ($userObj->loginUser($email, $password)) {
            header("Location: ../index.php");
            exit;
        } else {
            $_SESSION['message'] = "Username/password invalid";
            $_SESSION['status'] = "400";
            header("Location: ../login.php");
            exit;
        }
    } else {
        $_SESSION['message'] = "Please make sure there are no empty input fields";
        $_SESSION['status'] = '400';
        header("Location: ../login.php");
        exit;
    }
}

// User logout
if (isset($_GET['logoutUserBtn'])) {
    $userObj->logout();
    header("Location: ../index.php");
    exit;
}

// Create category (Admin only)
if (isset($_POST['createCategoryBtn'])) {
    if ($userObj->isAdmin()) {
        $category_name = trim($_POST['category_name']);
        $category_description = trim($_POST['category_description']);
        $category_color = $_POST['category_color'];
        $created_by = $_SESSION['user_id'];
        
        if (!empty($category_name)) {
            if (!$categoryObj->categoryNameExists($category_name)) {
                if ($categoryObj->createCategory($category_name, $category_description, $category_color, $created_by)) {
                    $_SESSION['message'] = "Category '{$category_name}' created successfully!";
                    $_SESSION['status'] = '200';
                } else {
                    $_SESSION['message'] = "Failed to create category.";
                    $_SESSION['status'] = '400';
                }
            } else {
                $_SESSION['message'] = "Category name already exists.";
                $_SESSION['status'] = '400';
            }
        } else {
            $_SESSION['message'] = "Category name is required.";
            $_SESSION['status'] = '400';
        }
    }
    header("Location: ../manage_categories.php");
    exit;
}

// Update category
if (isset($_POST['updateCategoryBtn'])) {
    if ($userObj->isAdmin()) {
        $category_id = $_POST['category_id'];
        $category_name = trim($_POST['category_name']);
        $category_description = trim($_POST['category_description']);
        $category_color = $_POST['category_color'];
        
        if (!empty($category_name)) {
            if (!$categoryObj->categoryNameExists($category_name, $category_id)) {
                if ($categoryObj->updateCategory($category_id, $category_name, $category_description, $category_color)) {
                    $_SESSION['message'] = "Category updated successfully!";
                    $_SESSION['status'] = '200';
                } else {
                    $_SESSION['message'] = "Failed to update category.";
                    $_SESSION['status'] = '400';
                }
            } else {
                $_SESSION['message'] = "Category name already exists.";
                $_SESSION['status'] = '400';
            }
        } else {
            $_SESSION['message'] = "Category name is required.";
            $_SESSION['status'] = '400';
        }
    }
    header("Location: ../manage_categories.php");
    exit;
}

// Toggle category status
if (isset($_POST['toggleCategoryStatus'])) {
    if ($userObj->isAdmin()) {
        $category_id = $_POST['category_id'];
        $is_active = $_POST['is_active'];
        
        echo $categoryObj->toggleCategoryStatus($category_id, $is_active);
    }
    exit;
}

// Delete category
if (isset($_POST['deleteCategoryBtn'])) {
    if ($userObj->isAdmin()) {
        $category_id = $_POST['category_id'];
        echo $categoryObj->deleteCategory($category_id);
    }
    exit;
}

// Enable all categories
if (isset($_POST['enableAllCategories'])) {
    if ($userObj->isAdmin()) {
        $categories = $categoryObj->getCategories(false);
        $success = true;
        foreach ($categories as $category) {
            if (!$category['is_active']) {
                if (!$categoryObj->toggleCategoryStatus($category['category_id'], 1)) {
                    $success = false;
                }
            }
        }
        echo $success ? 1 : 0;
    }
    exit;
}

// Create article (Admin) with category - FIXED REDIRECT
if (isset($_POST['insertAdminArticleBtn'])) {
    $title = $_POST['title'];
    $content = $_POST['description'];
    $author_id = $_SESSION['user_id'];
    $category_id = !empty($_POST['category_id']) ? $_POST['category_id'] : null;
    $image = isset($_FILES['image']) ? $_FILES['image'] : null;
    
    if ($articleObj->createArticle($title, $content, $author_id, $category_id, 1, $image)) {
        $_SESSION['message'] = "Admin article created successfully!";
        $_SESSION['status'] = '200';
        // Get the current page from HTTP_REFERER or default to admin articles page
        $redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../index.php';
        header("Location: " . $redirect_url);
        exit;
    } else {
        $_SESSION['message'] = "Failed to create article.";
        $_SESSION['status'] = '400';
        // Get the current page from HTTP_REFERER or default to admin articles page
        $redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../index.php';
        header("Location: " . $redirect_url);
        exit;
    }
}

// Create article (Writer) with category
if (isset($_POST['insertArticleBtn'])) {
    $title = $_POST['title'];
    $content = $_POST['description'];
    $author_id = $_SESSION['user_id'];
    $category_id = !empty($_POST['category_id']) ? $_POST['category_id'] : null;
    $image = isset($_FILES['image']) ? $_FILES['image'] : null;
    
    if ($articleObj->createArticle($title, $content, $author_id, $category_id, 0, $image)) {
        $_SESSION['message'] = "Article submitted successfully!";
        $_SESSION['status'] = '200';
        header("Location: ../index.php");
        exit;
    } else {
        $_SESSION['message'] = "Failed to submit article.";
        $_SESSION['status'] = '400';
        header("Location: ../index.php");
        exit;
    }
}

// Submit article with image (Writer form) with category
if (isset($_POST['form_type']) && $_POST['form_type'] === 'submit_article') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];
    $category_id = !empty($_POST['category_id']) ? $_POST['category_id'] : null;
    $image = isset($_FILES['article_image']) ? $_FILES['article_image'] : null;
    
    if ($articleObj->createArticle($title, $content, $user_id, $category_id, 0, $image)) {
        header('Location: ../articles_submitted.php?success=1');
    } else {
        header('Location: ../articles_submitted.php?error=1');
    }
    exit;
}

// Edit article with category - FIXED REDIRECT
if (isset($_POST['editArticleBtn'])) {
    $title = $_POST['title'];
    $content = isset($_POST['description']) ? $_POST['description'] : $_POST['content'];
    $article_id = $_POST['article_id'];
    $category_id = !empty($_POST['category_id']) ? $_POST['category_id'] : null;
    $image = isset($_FILES['image']) ? $_FILES['image'] : null;
    
    if ($articleObj->updateArticle($article_id, $title, $content, $category_id, $image)) {
        $_SESSION['message'] = "Article updated successfully!";
        $_SESSION['status'] = '200';
        // Get the current page from HTTP_REFERER or default to articles submitted page
        $redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../articles_submitted.php';
        header("Location: " . $redirect_url);
        exit;
    } else {
        $_SESSION['message'] = "Failed to update article.";
        $_SESSION['status'] = '400';
        // Get the current page from HTTP_REFERER or default to articles submitted page
        $redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../articles_submitted.php';
        header("Location: " . $redirect_url);
        exit;
    }
}

// Delete article
if (isset($_POST['deleteArticleBtn'])) {
    $article_id = $_POST['article_id'];
    $admin_id = $userObj->isAdmin() ? $_SESSION['user_id'] : null;
    $reason = isset($_POST['delete_reason']) ? $_POST['delete_reason'] : null;
    
    echo $articleObj->deleteArticle($article_id, $admin_id, $reason);
    exit;
}

// Update article visibility (Admin only)
if (isset($_POST['updateArticleVisibility'])) {
    if ($userObj->isAdmin()) {
        $article_id = $_POST['article_id'];
        $status = $_POST['status'];
        echo $articleObj->updateArticleVisibility($article_id, $status);
    }
    exit;
}

// Bulk approve articles
if (isset($_POST['bulkApproveArticles'])) {
    if ($userObj->isAdmin()) {
        $sql = "UPDATE articles SET is_active = 1 WHERE is_active = 0";
        echo $databaseObj->executeNonQuery($sql);
    }
    exit;
}

// Create edit request
if (isset($_POST['createEditRequest'])) {
    $article_id = $_POST['article_id'];
    $message = $_POST['message'];
    $requester_id = $_SESSION['user_id'];
    
    // Get article info to find author
    $article = $articleObj->getArticles($article_id);
    if ($article) {
        if ($editRequestObj->createEditRequest($article_id, $requester_id, $article['author_id'], $message)) {
            // Create notification for the author
            $requester_username = $_SESSION['username'];
            $notificationObj->createNotification(
                $article['author_id'],
                'edit_request',
                'Edit Request',
                "{$requester_username} has requested edit access to your article '{$article['title']}'",
                $article_id,
                $requester_id
            );
            
            $_SESSION['message'] = "Edit request sent successfully!";
            $_SESSION['status'] = '200';
        } else {
            $_SESSION['message'] = "Failed to send edit request or request already exists.";
            $_SESSION['status'] = '400';
        }
    }
    
    header("Location: ../index.php");
    exit;
}

// Handle edit request (approve/reject)
if (isset($_POST['handleEditRequest'])) {
    $request_id = $_POST['request_id'];
    $action = $_POST['action']; // 'approve' or 'reject'
    $author_id = $_SESSION['user_id'];
    
    $request = $editRequestObj->getRequest($request_id);
    if ($request && $request['author_id'] == $author_id) {
        $status = ($action === 'approve') ? 'approved' : 'rejected';
        
        if ($editRequestObj->updateRequestStatus($request_id, $status, $author_id)) {
            // If approved, grant edit access
            if ($status === 'approved') {
                $sharedArticleObj->grantEditAccess(
                    $request['article_id'],
                    $request['author_id'],
                    $request['requester_id']
                );
            }
            
            // Create notification for requester
            $message = $status === 'approved' ? 
                "Your edit request for '{$request['article_title']}' has been approved!" :
                "Your edit request for '{$request['article_title']}' has been rejected.";
                
            $notificationObj->createNotification(
                $request['requester_id'],
                'edit_' . $status,
                'Edit Request ' . ucfirst($status),
                $message,
                $request['article_id'],
                $author_id
            );
            
            $_SESSION['message'] = "Edit request " . $status . " successfully!";
            $_SESSION['status'] = '200';
        } else {
            $_SESSION['message'] = "Failed to update edit request.";
            $_SESSION['status'] = '400';
        }
    }
    
    header("Location: ../notifications.php");
    exit;
}

// Revoke edit access
if (isset($_POST['revokeEditAccess'])) {
    $article_id = $_POST['article_id'];
    $editor_id = $_POST['editor_id'];
    
    if ($sharedArticleObj->revokeEditAccess($article_id, $editor_id)) {
        $_SESSION['message'] = "Edit access revoked successfully!";
        $_SESSION['status'] = '200';
    } else {
        $_SESSION['message'] = "Failed to revoke edit access.";
        $_SESSION['status'] = '400';
    }
    
    header("Location: ../articles_submitted.php");
    exit;
}

// Mark notification as read
if (isset($_POST['markNotificationRead'])) {
    $notification_id = $_POST['notification_id'];
    $user_id = $_SESSION['user_id'];
    
    echo $notificationObj->markAsRead($notification_id, $user_id);
    exit;
}

?>