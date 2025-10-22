<?php
// admin/create.php
require_once '../config.php';
require_once '../models/Blog.php';

$blogModel = new Blog();
$errors = [];

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitize($_POST['title']);
    $content = $_POST['content'];
    $excerpt = sanitize($_POST['excerpt']);
    $featured = isset($_POST['featured']) ? 1 : 0;
    $status = $_POST['status'];
    $slug = createSlug($title);
    
    // Validation
    if(empty($title)) {
        $errors[] = 'Title is required';
    }
    if(empty($content)) {
        $errors[] = 'Content is required';
    }
    if($blogModel->slugExists($slug)) {
        $errors[] = 'A blog with similar title already exists';
    }
    
    // Handle image upload
    $imageName = '';
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $fileExt = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if(in_array($fileExt, $allowed)) {
            $imageName = uniqid() . '.' . $fileExt;
            $uploadPath = '../uploads/' . $imageName;
            
            if(!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                $errors[] = 'Failed to upload image';
            }
        } else {
            $errors[] = 'Invalid image format. Only JPG, JPEG, PNG, and GIF allowed';
        }
    }
    
    if(empty($errors)) {
        $data = [
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'excerpt' => $excerpt,
            'featured' => $featured,
            'image' => $imageName,
            'status' => $status
        ];
        
        if($blogModel->create($data)) {
            redirect('admin/index.php?msg=created');
        } else {
            $errors[] = 'Failed to create blog post';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Blog Post - Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <aside class="admin-sidebar">
            <div class="admin-logo">
                <h2>Blog CMS</h2>
            </div>
            <nav class="admin-nav">
                <a href="<?php echo BASE_URL; ?>admin/index.php">Dashboard</a>
                <a href="<?php echo BASE_URL; ?>admin/create.php" class="active">Create New Post</a>
                <a href="<?php echo BASE_URL; ?>">View Site</a>
            </nav>
        </aside>
        
        <main class="admin-main">
            <div class="admin-header">
                <h1>Create New Blog Post</h1>
                <a href="<?php echo BASE_URL; ?>admin/index.php" class="btn-secondary">← Back to Dashboard</a>
            </div>
            
            <?php if(!empty($errors)): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <div class="admin-content">
                <form method="POST" enctype="multipart/form-data" class="admin-form">
                    <div class="form-group">
                        <label for="title">Title *</label>
                        <input type="text" id="title" name="title" required value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="excerpt">Excerpt</label>
                        <textarea id="excerpt" name="excerpt" rows="3" maxlength="500"><?php echo isset($_POST['excerpt']) ? htmlspecialchars($_POST['excerpt']) : ''; ?></textarea>
                        <small>Short description (max 500 characters)</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="content">Content *</label>
                        <textarea id="content" name="content" rows="15" required><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="image">Featured Image</label>
                        <input type="file" id="image" name="image" accept="image/*">
                        <small>Supported formats: JPG, JPEG, PNG, GIF</small>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="status">Status *</label>
                            <select id="status" name="status" required>
                                <option value="draft" <?php echo (isset($_POST['status']) && $_POST['status'] == 'draft') ? 'selected' : ''; ?>>Draft</option>
                                <option value="published" <?php echo (isset($_POST['status']) && $_POST['status'] == 'published') ? 'selected' : ''; ?>>Published</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="featured" value="1" <?php echo (isset($_POST['featured'])) ? 'checked' : ''; ?>>
                                <span>Mark as Featured</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Create Blog Post</button>
                        <a href="<?php echo BASE_URL; ?>admin/index.php" class="btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>