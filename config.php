<?php
// config.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'blog_cms');
define('DB_USER', 'root');
define('DB_PASS', '');
define('BASE_URL', 'http://localhost/blog-cms/');

// db.php
class Database {
    private static $instance = null;
    private $conn;
    
    private function __construct() {
        try {
            $this->conn = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
                DB_USER,
                DB_PASS,
                array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
            );
        } catch(PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if(!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->conn;
    }
}

// Helper function
function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit();
}

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function createSlug($string) {
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string), '-'));
    return $slug;
}
?>