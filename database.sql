-- database.sql
-- Create database and tables for Blog CMS

CREATE DATABASE IF NOT EXISTS blog_cms;
USE blog_cms;

CREATE TABLE IF NOT EXISTS blogs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content TEXT NOT NULL,
    excerpt VARCHAR(500),
    featured TINYINT(1) DEFAULT 0,
    image VARCHAR(255),
    status ENUM('draft', 'published') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_featured (featured),
    INDEX idx_status (status),
    INDEX idx_slug (slug)
);