<?php  

require_once 'Database.php';

/**
 * Class for handling Edit Request-related operations.
 */
class EditRequest extends Database {
    
    /**
     * Creates a new edit request
     * @param int $article_id Article ID
     * @param int $requester_id User requesting edit access
     * @param int $author_id Article author
     * @param string|null $message Optional message
     * @return bool Success status
     */
    public function createEditRequest($article_id, $requester_id, $author_id, $message = null) {
        // Check if request already exists
        if ($this->requestExists($article_id, $requester_id)) {
            return false;
        }
        
        $sql = "INSERT INTO edit_requests (article_id, requester_id, author_id, message) 
                VALUES (?, ?, ?, ?)";
        return $this->executeNonQuery($sql, [$article_id, $requester_id, $author_id, $message]);
    }
    
    /**
     * Checks if an edit request already exists
     * @param int $article_id Article ID
     * @param int $requester_id Requester ID
     * @return bool True if exists
     */
    private function requestExists($article_id, $requester_id) {
        $sql = "SELECT COUNT(*) as count FROM edit_requests 
                WHERE article_id = ? AND requester_id = ? AND status = 'pending'";
        $result = $this->executeQuerySingle($sql, [$article_id, $requester_id]);
        return $result && $result['count'] > 0;
    }
    
    /**
     * Gets edit requests for a user (as author)
     * @param int $author_id Author ID
     * @param string|null $status Filter by status
     * @return array Edit requests
     */
    public function getRequestsForAuthor($author_id, $status = null) {
        $sql = "SELECT er.*, a.title as article_title, u.username as requester_username 
                FROM edit_requests er 
                JOIN articles a ON er.article_id = a.article_id 
                JOIN school_publication_users u ON er.requester_id = u.user_id 
                WHERE er.author_id = ?";
        
        $params = [$author_id];
        
        if ($status) {
            $sql .= " AND er.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY er.created_at DESC";
        
        return $this->executeQuery($sql, $params);
    }
    
    /**
     * Gets edit requests made by a user
     * @param int $requester_id Requester ID
     * @return array Edit requests
     */
    public function getRequestsByUser($requester_id) {
        $sql = "SELECT er.*, a.title as article_title, u.username as author_username 
                FROM edit_requests er 
                JOIN articles a ON er.article_id = a.article_id 
                JOIN school_publication_users u ON er.author_id = u.user_id 
                WHERE er.requester_id = ? 
                ORDER BY er.created_at DESC";
        
        return $this->executeQuery($sql, [$requester_id]);
    }
    
    /**
     * Updates edit request status
     * @param int $request_id Request ID
     * @param string $status New status ('approved' or 'rejected')
     * @param int $author_id Author ID (for security)
     * @return bool Success status
     */
    public function updateRequestStatus($request_id, $status, $author_id) {
        $sql = "UPDATE edit_requests SET status = ?, updated_at = NOW() 
                WHERE request_id = ? AND author_id = ?";
        return $this->executeNonQuery($sql, [$status, $request_id, $author_id]);
    }
    
    /**
     * Gets a specific edit request
     * @param int $request_id Request ID
     * @return array|null Edit request data
     */
    public function getRequest($request_id) {
        $sql = "SELECT er.*, a.title as article_title, 
                       req.username as requester_username,
                       auth.username as author_username 
                FROM edit_requests er 
                JOIN articles a ON er.article_id = a.article_id 
                JOIN school_publication_users req ON er.requester_id = req.user_id 
                JOIN school_publication_users auth ON er.author_id = auth.user_id 
                WHERE er.request_id = ?";
        
        return $this->executeQuerySingle($sql, [$request_id]);
    }
}

?>