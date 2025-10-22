<?php
// models/Blog.php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';

class Blog {
    private $conn;
    private $table = 'blogs';
    
    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }
    
    // Get all blogs
    public function getAll($status = 'published', $limit = null) {
        $sql = "SELECT * FROM {$this->table} WHERE status = :status ORDER BY created_at DESC";
        if($limit) {
            $sql .= " LIMIT :limit";
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':status', $status);
        if($limit) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get featured blogs
    public function getFeatured($limit = 3) {
        $sql = "SELECT * FROM {$this->table} WHERE featured = 1 AND status = 'published' ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get blog by ID
    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Get blog by slug
    public function getBySlug($slug) {
        $sql = "SELECT * FROM {$this->table} WHERE slug = :slug AND status = 'published'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':slug', $slug);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Create blog
    public function create($data) {
        $sql = "INSERT INTO {$this->table} (title, slug, content, excerpt, featured, image, status) 
                VALUES (:title, :slug, :content, :excerpt, :featured, :image, :status)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':slug', $data['slug']);
        $stmt->bindParam(':content', $data['content']);
        $stmt->bindParam(':excerpt', $data['excerpt']);
        $stmt->bindParam(':featured', $data['featured']);
        $stmt->bindParam(':image', $data['image']);
        $stmt->bindParam(':status', $data['status']);
        
        return $stmt->execute();
    }
    
    // Update blog
    public function update($id, $data) {
        $sql = "UPDATE {$this->table} SET 
                title = :title, 
                slug = :slug, 
                content = :content, 
                excerpt = :excerpt, 
                featured = :featured, 
                image = :image, 
                status = :status 
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':slug', $data['slug']);
        $stmt->bindParam(':content', $data['content']);
        $stmt->bindParam(':excerpt', $data['excerpt']);
        $stmt->bindParam(':featured', $data['featured']);
        $stmt->bindParam(':image', $data['image']);
        $stmt->bindParam(':status', $data['status']);
        
        return $stmt->execute();
    }
    
    // Delete blog
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
    // Check if slug exists
    public function slugExists($slug, $excludeId = null) {
        $sql = "SELECT id FROM {$this->table} WHERE slug = :slug";
        if($excludeId) {
            $sql .= " AND id != :id";
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':slug', $slug);
        if($excludeId) {
            $stmt->bindParam(':id', $excludeId);
        }
        $stmt->execute();
        return $stmt->fetch() ? true : false;
    }
}
?>