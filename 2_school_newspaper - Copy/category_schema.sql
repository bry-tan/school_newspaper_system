-- Category System Database Schema Enhancement

-- Create categories table
CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE,
    category_description TEXT NULL,
    category_color VARCHAR(7) DEFAULT '#007bff',
    is_active TINYINT(1) DEFAULT 1,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES school_publication_users(user_id) ON DELETE CASCADE
);

-- Add category_id column to articles table
ALTER TABLE articles 
ADD COLUMN category_id INT NULL,
ADD FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE SET NULL;

-- Insert default categories
INSERT INTO categories (category_name, category_description, category_color, created_by) VALUES
('News Reports', 'Breaking news and current events from around the school', '#dc3545', 1),
('Editorials', 'Opinion pieces and editorial commentary from staff and students', '#28a745', 1),
('Sports', 'Athletic events, game results, and sports-related news', '#ffc107', 1),
('Arts & Culture', 'Creative arts, performances, exhibitions, and cultural events', '#e83e8c', 1),
('Academic', 'Educational content, study tips, and academic achievements', '#17a2b8', 1),
('Student Life', 'Campus life, clubs, organizations, and student activities', '#6f42c1', 1),
('Opinion', 'Student opinions, letters to the editor, and personal perspectives', '#fd7e14', 1),
('Announcements', 'Official school announcements and important notices', '#6c757d', 1);

-- Create index for better performance
CREATE INDEX idx_articles_category ON articles(category_id);
CREATE INDEX idx_categories_active ON categories(is_active);