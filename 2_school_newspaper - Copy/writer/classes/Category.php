<?php  

require_once 'Database.php';

/**
 * Class for handling Category-related operations.
 */
class Category extends Database {
    
    /**
     * Creates a new category (Admin only)
     * @param string $name Category name
     * @param string $description Category description
     * @param string $color Category color (hex)
     * @param int $created_by User ID of creator
     * @return bool Success status
     */
    public function createCategory($name, $description, $color, $created_by) {
        $sql = "INSERT INTO categories (category_name, category_description, category_color, created_by) 
                VALUES (?, ?, ?, ?)";
        return $this->executeNonQuery($sql, [$name, $description, $color, $created_by]);
    }
    
    /**
     * Gets all categories
     * @param bool $active_only Get only active categories
     * @return array Categories
     */
    public function getCategories($active_only = true) {
        $sql = "SELECT c.*, u.username as created_by_name,
                       COUNT(a.article_id) as article_count
                FROM categories c 
                LEFT JOIN school_publication_users u ON c.created_by = u.user_id 
                LEFT JOIN articles a ON c.category_id = a.category_id AND a.is_active = 1";
        
        if ($active_only) {
            $sql .= " WHERE c.is_active = 1";
        }
        
        $sql .= " GROUP BY c.category_id ORDER BY c.category_name ASC";
        
        return $this->executeQuery($sql);
    }
    
    /**
     * Gets a specific category
     * @param int $category_id Category ID
     * @return array|null Category data
     */
    public function getCategory($category_id) {
        $sql = "SELECT c.*, u.username as created_by_name,
                       COUNT(a.article_id) as article_count
                FROM categories c 
                LEFT JOIN school_publication_users u ON c.created_by = u.user_id 
                LEFT JOIN articles a ON c.category_id = a.category_id
                WHERE c.category_id = ?
                GROUP BY c.category_id";
        
        return $this->executeQuerySingle($sql, [$category_id]);
    }
    
    /**
     * Updates a category
     * @param int $category_id Category ID
     * @param string $name New name
     * @param string $description New description
     * @param string $color New color
     * @return bool Success status
     */
    public function updateCategory($category_id, $name, $description, $color) {
        $sql = "UPDATE categories 
                SET category_name = ?, category_description = ?, category_color = ? 
                WHERE category_id = ?";
        return $this->executeNonQuery($sql, [$name, $description, $color, $category_id]);
    }
    
    /**
     * Toggles category active status
     * @param int $category_id Category ID
     * @param int $is_active New status
     * @return bool Success status
     */
    public function toggleCategoryStatus($category_id, $is_active) {
        $sql = "UPDATE categories SET is_active = ? WHERE category_id = ?";
        return $this->executeNonQuery($sql, [$is_active, $category_id]);
    }
    
    /**
     * Deletes a category (moves articles to null category)
     * @param int $category_id Category ID
     * @return bool Success status
     */
    public function deleteCategory($category_id) {
        // First, remove category from articles (set to NULL)
        $sql1 = "UPDATE articles SET category_id = NULL WHERE category_id = ?";
        $this->executeNonQuery($sql1, [$category_id]);
        
        // Then delete the category
        $sql2 = "DELETE FROM categories WHERE category_id = ?";
        return $this->executeNonQuery($sql2, [$category_id]);
    }
    
    /**
     * Gets articles by category
     * @param int $category_id Category ID
     * @param bool $active_only Get only active articles
     * @return array Articles in category
     */
    public function getArticlesByCategory($category_id, $active_only = true) {
        $sql = "SELECT a.*, u.username, c.category_name, c.category_color 
                FROM articles a 
                LEFT JOIN school_publication_users u ON a.author_id = u.user_id 
                LEFT JOIN categories c ON a.category_id = c.category_id 
                WHERE a.category_id = ?";
        
        if ($active_only) {
            $sql .= " AND a.is_active = 1";
        }
        
        $sql .= " ORDER BY a.created_at DESC";
        
        return $this->executeQuery($sql, [$category_id]);
    }
    
    /**
     * Gets category statistics
     * @return array Category stats
     */
    public function getCategoryStats() {
        $sql = "SELECT 
                    COUNT(DISTINCT c.category_id) as total_categories,
                    COUNT(DISTINCT CASE WHEN c.is_active = 1 THEN c.category_id END) as active_categories,
                    COUNT(DISTINCT a.article_id) as categorized_articles,
                    COUNT(DISTINCT CASE WHEN a.category_id IS NULL THEN a.article_id END) as uncategorized_articles
                FROM categories c 
                LEFT JOIN articles a ON c.category_id = a.category_id";
        
        return $this->executeQuerySingle($sql);
    }
    
    /**
     * Checks if category name exists
     * @param string $name Category name
     * @param int|null $exclude_id Exclude this category ID (for updates)
     * @return bool True if exists
     */
    public function categoryNameExists($name, $exclude_id = null) {
        $sql = "SELECT COUNT(*) as count FROM categories WHERE category_name = ?";
        $params = [$name];
        
        if ($exclude_id) {
            $sql .= " AND category_id != ?";
            $params[] = $exclude_id;
        }
        
        $result = $this->executeQuerySingle($sql, $params);
        return $result && $result['count'] > 0;
    }
    
    /**
     * Gets popular categories (by article count)
     * @param int $limit Number of categories to return
     * @return array Popular categories
     */
    public function getPopularCategories($limit = 5) {
        $sql = "SELECT c.*, COUNT(a.article_id) as article_count 
                FROM categories c 
                LEFT JOIN articles a ON c.category_id = a.category_id AND a.is_active = 1
                WHERE c.is_active = 1
                GROUP BY c.category_id 
                ORDER BY article_count DESC, c.category_name ASC 
                LIMIT ?";
        
        return $this->executeQuery($sql, [$limit]);
    }
}

?>