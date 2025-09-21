<?php  

require_once 'Database.php';
require_once 'User.php';
// Remove the Notification require - we'll handle it differently

/**
 * Enhanced Article class with category support
 */
class Article extends Database {
    
    public function __construct() {
        parent::__construct();
        // Don't initialize notification here to avoid dependency issues
    }
    
    /**
     * Creates a new article with category and optional image
     */
    public function createArticle($title, $content, $author_id, $category_id = null, $is_admin = 0, $image = null) {
        $image_path = null;
        
        // Handle image upload
        if ($image && $image['error'] === UPLOAD_ERR_OK) {
            $image_path = $this->handleImageUpload($image);
            if (!$image_path) {
                return false;
            }
        }
        
        $sql = "INSERT INTO articles (title, content, author_id, category_id, is_admin, image_path, is_active) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $is_active = $is_admin ? 1 : 0;
        
        return $this->executeNonQuery($sql, [$title, $content, $author_id, $category_id, $is_admin, $image_path, $is_active]);
    }
    
    /**
     * Handles image file upload
     */
    private function handleImageUpload($image) {
        $target_dir = "../uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $imageFileType = strtolower(pathinfo($image["name"], PATHINFO_EXTENSION));
        $target_file = "uploads/" . uniqid() . "." . $imageFileType;
        
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($imageFileType, $allowed_types)) {
            return false;
        }
        
        if ($image["size"] > 5000000) {
            return false;
        }
        
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
     * Retrieves articles with category information
     */
    public function getArticles($id = null) {
        if ($id) {
            $sql = "SELECT a.*, u.username, c.category_name, c.category_color 
                    FROM articles a 
                    JOIN school_publication_users u ON a.author_id = u.user_id 
                    LEFT JOIN categories c ON a.category_id = c.category_id 
                    WHERE a.article_id = ?";
            return $this->executeQuerySingle($sql, [$id]);
        }
        $sql = "SELECT a.*, u.username, c.category_name, c.category_color 
                FROM articles a 
                JOIN school_publication_users u ON a.author_id = u.user_id 
                LEFT JOIN categories c ON a.category_id = c.category_id 
                ORDER BY a.created_at DESC";
        return $this->executeQuery($sql);
    }

    /**
     * Gets active articles with category information
     */
    public function getActiveArticles($id = null, $category_id = null) {
        if ($id) {
            $sql = "SELECT a.*, u.username, c.category_name, c.category_color 
                    FROM articles a 
                    JOIN school_publication_users u ON a.author_id = u.user_id 
                    LEFT JOIN categories c ON a.category_id = c.category_id 
                    WHERE a.article_id = ? AND a.is_active = 1";
            return $this->executeQuerySingle($sql, [$id]);
        }
        
        $sql = "SELECT a.*, u.username, c.category_name, c.category_color 
                FROM articles a 
                JOIN school_publication_users u ON a.author_id = u.user_id 
                LEFT JOIN categories c ON a.category_id = c.category_id 
                WHERE a.is_active = 1";
        
        $params = [];
        if ($category_id) {
            $sql .= " AND a.category_id = ?";
            $params[] = $category_id;
        }
        
        $sql .= " ORDER BY a.created_at DESC";
        
        return $this->executeQuery($sql, $params);
    }

    /**
     * Gets articles by user ID
     */
    public function getArticlesByUserID($user_id) {
        $sql = "SELECT a.*, u.username, c.category_name, c.category_color 
                FROM articles a 
                JOIN school_publication_users u ON a.author_id = u.user_id
                LEFT JOIN categories c ON a.category_id = c.category_id 
                WHERE a.author_id = ? ORDER BY a.created_at DESC";
        return $this->executeQuery($sql, [$user_id]);
    }

    /**
     * Updates an article with category and image
     */
    public function updateArticle($id, $title, $content, $category_id = null, $image = null) {
        $current_article = $this->getArticles($id);
        $image_path = $current_article['image_path'] ?? null;
        
        if ($image && $image['error'] === UPLOAD_ERR_OK) {
            $new_image_path = $this->handleImageUpload($image);
            if ($new_image_path) {
                if ($image_path && file_exists("../" . $image_path)) {
                    unlink("../" . $image_path);
                }
                $image_path = $new_image_path;
            }
        }
        
        $sql = "UPDATE articles SET title = ?, content = ?, category_id = ?, image_path = ? WHERE article_id = ?";
        return $this->executeNonQuery($sql, [$title, $content, $category_id, $image_path, $id]);
    }
    
    /**
     * Updates article visibility
     */
    public function updateArticleVisibility($id, $is_active) {
        $sql = "UPDATE articles SET is_active = ? WHERE article_id = ?";
        return $this->executeNonQuery($sql, [$is_active, $id]);
    }

    /**
     * Deletes an article
     */
    public function deleteArticle($id, $admin_id = null, $reason = null) {
        $article = $this->getArticles($id);
        if (!$article) {
            return false;
        }
        
        if ($article['image_path'] && file_exists("../" . $article['image_path'])) {
            unlink("../" . $article['image_path']);
        }
        
        $sql = "DELETE FROM articles WHERE article_id = ?";
        $success = $this->executeNonQuery($sql, [$id]);
        
        // Handle notification separately in handleForms.php if needed
        
        return $success;
    }

    /**
     * Gets articles for edit requests
     */
    public function getArticlesForEditRequest($user_id) {
        $sql = "SELECT a.*, u.username as author_username, c.category_name, c.category_color 
                FROM articles a 
                JOIN school_publication_users u ON a.author_id = u.user_id 
                LEFT JOIN categories c ON a.category_id = c.category_id 
                WHERE a.author_id != ? AND a.is_active = 1
                ORDER BY a.created_at DESC";
        return $this->executeQuery($sql, [$user_id]);
    }
    
    /**
     * Legacy method for compatibility
     */
    public function submitArticle($title, $content, $user_id, $category_id = null, $is_admin = 0, $image = null) {
        return $this->createArticle($title, $content, $user_id, $category_id, $is_admin, $image);
    }
}

?>