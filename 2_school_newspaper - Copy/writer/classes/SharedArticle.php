<?php  

require_once 'Database.php';

/**
 * Class for handling Shared Article-related operations.
 */
class SharedArticle extends Database {
    
    /**
     * Grants edit access to an article
     * @param int $article_id Article ID
     * @param int $author_id Author ID
     * @param int $editor_id Editor ID
     * @return bool Success status
     */
    public function grantEditAccess($article_id, $author_id, $editor_id) {
        $sql = "INSERT INTO shared_articles (article_id, author_id, editor_id) 
                VALUES (?, ?, ?) 
                ON DUPLICATE KEY UPDATE granted_at = NOW()";
        return $this->executeNonQuery($sql, [$article_id, $author_id, $editor_id]);
    }
    
    /**
     * Revokes edit access to an article
     * @param int $article_id Article ID
     * @param int $editor_id Editor ID
     * @return bool Success status
     */
    public function revokeEditAccess($article_id, $editor_id) {
        $sql = "DELETE FROM shared_articles WHERE article_id = ? AND editor_id = ?";
        return $this->executeNonQuery($sql, [$article_id, $editor_id]);
    }
    
    /**
     * Gets articles shared with a specific user
     * @param int $editor_id Editor ID
     * @return array Shared articles
     */
    public function getSharedArticles($editor_id) {
        $sql = "SELECT sa.*, a.title, a.content, a.image_path, a.is_active, a.created_at,
                       u.username as author_username
                FROM shared_articles sa 
                JOIN articles a ON sa.article_id = a.article_id 
                JOIN school_publication_users u ON sa.author_id = u.user_id 
                WHERE sa.editor_id = ? 
                ORDER BY sa.granted_at DESC";
        
        return $this->executeQuery($sql, [$editor_id]);
    }
    
    /**
     * Gets editors for a specific article
     * @param int $article_id Article ID
     * @return array Editors with access
     */
    public function getArticleEditors($article_id) {
        $sql = "SELECT sa.*, u.username as editor_username 
                FROM shared_articles sa 
                JOIN school_publication_users u ON sa.editor_id = u.user_id 
                WHERE sa.article_id = ?";
        
        return $this->executeQuery($sql, [$article_id]);
    }
    
    /**
     * Checks if a user has edit access to an article
     * @param int $article_id Article ID
     * @param int $user_id User ID
     * @return bool True if has access
     */
    public function hasEditAccess($article_id, $user_id) {
        // Check if user is the author
        $sql = "SELECT COUNT(*) as count FROM articles WHERE article_id = ? AND author_id = ?";
        $result = $this->executeQuerySingle($sql, [$article_id, $user_id]);
        if ($result && $result['count'] > 0) {
            return true;
        }
        
        // Check if user has shared access
        $sql = "SELECT COUNT(*) as count FROM shared_articles WHERE article_id = ? AND editor_id = ?";
        $result = $this->executeQuerySingle($sql, [$article_id, $user_id]);
        return $result && $result['count'] > 0;
    }
    
    /**
     * Gets articles that a user has shared with others
     * @param int $author_id Author ID
     * @return array Shared articles with editor info
     */
    public function getArticlesSharedByAuthor($author_id) {
        $sql = "SELECT sa.*, a.title, u.username as editor_username 
                FROM shared_articles sa 
                JOIN articles a ON sa.article_id = a.article_id 
                JOIN school_publication_users u ON sa.editor_id = u.user_id 
                WHERE sa.author_id = ? 
                ORDER BY sa.granted_at DESC";
        
        return $this->executeQuery($sql, [$author_id]);
    }
}

?>