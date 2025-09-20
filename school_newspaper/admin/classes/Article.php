<?php  

require_once 'Database.php';
require_once 'User.php';
require_once 'Notification.php';

/**
 * Enhanced Article class with image support and notification features
 */
class Article extends Database {
    
    private $notificationObj;
    
    public function __construct() {
        parent::__construct();
        $this->notificationObj = new Notification();
    }
    
    /**
     * Creates a new article with optional image
     * @param string $title The article title
     * @param string $content The article content
     * @param int $author_id The ID of the author
     * @param int $is_admin Whether created by admin (default 0)
     * @param array|null $image Optional image file
     * @return bool Success status
     */
    public function createArticle($title, $content, $author_id, $is_admin = 0, $image = null) {
        $image_path = null;
        
        // Handle image upload
        if ($image && $image['error'] === UPLOAD_ERR_OK) {
            $image_path = $this->handleImageUpload($image);
            if (!$image_path) {
                return false; // Image upload failed
            }
        }
        
        $sql = "INSERT INTO articles (title, content, author_id, is_admin, image_path, is_active) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $is_active = $is_admin ? 1 : 0; // Admin articles are active by default
        
        return $this->executeNonQuery($sql, [$title, $content, $author_id, $is_admin, $image_path, $is_active]);
    }
    
    /**
     * Handles image file upload
     * @param array $image Image file data
     * @return string|false Image path on success, false on failure
     */
    private function handleImageUpload($image) {
        $target_dir = "../uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $imageFileType = strtolower(pathinfo($image["name"], PATHINFO_EXTENSION));
        $target_file = "uploads/" . uniqid() . "." . $imageFileType;
        
        // Validate file type
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($imageFileType, $allowed_types)) {
            return false;
        }
        
        // Validate file size (5MB max)
        if ($image["size"] > 5000000) {
            return false;
        }
        
        // Validate that it's actually an image
        $check = getimagesize($image["tmp_name"]);
        if ($check === false) {
            return false;
        }
        
        if (move_uploaded_file($image["tmp_name"], "../" . $target_file)) {
            return $target_file;
        }
        
        return false;
    }
    
    /**
     * Retrieves articles from the database
     * @param int|null $id The article ID to retrieve, or null for all articles
     * @return array
     */
    public function getArticles($id = null) {
        if ($id) {
            $sql = "SELECT * FROM articles WHERE article_id = ?";
            return $this->executeQuerySingle($sql, [$id]);
        }
        $sql = "SELECT a.*, u.username FROM articles a 
                JOIN school_publication_users u ON a.author_id = u.user_id 
                ORDER BY a.created_at DESC";
        return $this->executeQuery($sql);
    }

    /**
     * Gets active articles with author information
     * @param int|null $id Specific article ID
     * @return array
     */
    public function getActiveArticles($id = null) {
        if ($id) {
            $sql = "SELECT a.*, u.username FROM articles a 
                    JOIN school_publication_users u ON a.author_id = u.user_id 
                    WHERE a.article_id = ? AND a.is_active = 1";
            return $this->executeQuerySingle($sql, [$id]);
        }
        $sql = "SELECT a.*, u.username FROM articles a 
                JOIN school_publication_users u ON a.author_id = u.user_id 
                WHERE a.is_active = 1 ORDER BY a.created_at DESC";
        return $this->executeQuery($sql);
    }

    /**
     * Gets articles by user ID
     * @param int $user_id User ID
     * @return array
     */
    public function getArticlesByUserID($user_id) {
        $sql = "SELECT a.*, u.username FROM articles a 
                JOIN school_publication_users u ON a.author_id = u.user_id
                WHERE a.author_id = ? ORDER BY a.created_at DESC";
        return $this->executeQuery($sql, [$user_id]);
    }

    /**
     * Updates an article with optional image
     * @param int $id The article ID to update
     * @param string $title The new title
     * @param string $content The new content
     * @param array|null $image Optional new image
     * @return bool Success status
     */
    public function updateArticle($id, $title, $content, $image = null) {
        // Get current article for image handling
        $current_article = $this->getArticles($id);
        $image_path = $current_article['image_path'] ?? null;
        
        // Handle new image upload
        if ($image && $image['error'] === UPLOAD_ERR_OK) {
            $new_image_path = $this->handleImageUpload($image);
            if ($new_image_path) {
                // Delete old image if it exists
                if ($image_path && file_exists("../" . $image_path)) {
                    unlink("../" . $image_path);
                }
                $image_path = $new_image_path;
            }
        }
        
        $sql = "UPDATE articles SET title = ?, content = ?, image_path = ? WHERE article_id = ?";
        return $this->executeNonQuery($sql, [$title, $content, $image_path, $id]);
    }
    
    /**
     * Updates article visibility (admin only)
     * @param int $id Article ID
     * @param int $is_active New status
     * @return bool Success status
     */
    public function updateArticleVisibility($id, $is_active) {
        $sql = "UPDATE articles SET is_active = ? WHERE article_id = ?";
        return $this->executeNonQuery($sql, [$is_active, $id]);
    }

    /**
     * Deletes an article with admin notification
     * @param int $id The article ID to delete
     * @param int|null $admin_id ID of admin deleting (for notification)
     * @param string|null $reason Reason for deletion
     * @return bool Success status
     */
    public function deleteArticle($id, $admin_id = null, $reason = null) {
        // Get article info before deletion for notification
        $article = $this->getArticles($id);
        if (!$article) {
            return false;
        }
        
        // Delete image file if it exists
        if ($article['image_path'] && file_exists("../" . $article['image_path'])) {
            unlink("../" . $article['image_path']);
        }
        
        $sql = "DELETE FROM articles WHERE article_id = ?";
        $success = $this->executeNonQuery($sql, [$id]);
        
        // Send notification to author if deleted by admin
        if ($success && $admin_id && $admin_id != $article['author_id']) {
            $message = $reason ? 
                "Your article '{$article['title']}' was deleted. Reason: {$reason}" :
                "Your article '{$article['title']}' was deleted by an administrator.";
                
            $this->notificationObj->createNotification(
                $article['author_id'],
                'article_deleted',
                'Article Deleted',
                $message,
                null,
                $admin_id
            );
        }
        
        return $success;
    }

    /**
     * Submits a new article (legacy method for compatibility)
     * @param string $title The article title
     * @param string $content The article content
     * @param int $user_id The user ID
     * @param int $is_admin Admin status
     * @param array|null $image Optional image file
     * @return bool Success status
     */
    public function submitArticle($title, $content, $user_id, $is_admin = 0, $image = null) {
        return $this->createArticle($title, $content, $user_id, $is_admin, $image);
    }
    
    /**
     * Gets articles that can be requested for editing by a user
     * @param int $user_id Current user ID
     * @return array Available articles
     */
    public function getArticlesForEditRequest($user_id) {
        $sql = "SELECT a.*, u.username as author_username 
                FROM articles a 
                JOIN school_publication_users u ON a.author_id = u.user_id 
                WHERE a.author_id != ? AND a.is_active = 1
                ORDER BY a.created_at DESC";
        return $this->executeQuery($sql, [$user_id]);
    }
}

?>