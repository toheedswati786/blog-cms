<?php
// admin/edit.php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/Blog.php';

if(!isset($_GET['id'])) {
    redirect('admin/index.php');
}

$blogModel = new Blog();
$blog = $blogModel->getById($_GET['id']);

if(!$blog) {
    redirect('admin/index.php');
}

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
    if($blogModel->slugExists($slug, $_GET['id'])) {
        $errors[] = 'A blog with similar title already exists';
    }
    
    // Handle image upload
    $imageName = $blog['image'];
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $fileExt = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if(in_array($fileExt, $allowed)) {
            // Delete old image if exists
            if($blog['image'] && file_exists('../uploads/' . $blog['image'])) {
                unlink('../uploads/' . $blog['image']);
            }
            
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
        
        if($blogModel->update($_GET['id'], $data)) {
            redirect('admin/index.php?msg=updated');
        } else {
            $errors[] = 'Failed to update blog post';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Blog Post - Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <script src="https://cdn.tiny.cloud/1/gxn8o7v7lw5gbb4z7nnhwg6naqjrndcexr5cfaroxbnqklg8/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#content',
            height: 500,
            menubar: false,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | bold italic underline strikethrough | ' +
                'alignleft aligncenter alignright alignjustify | ' +
                'bullist numlist outdent indent | forecolor backcolor | ' +
                'removeformat | link image | code | help',
            content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, sans-serif; font-size: 16px; }'
        });
    </script>
</head>
<body>
    <div class="admin-wrapper">
        <aside class="admin-sidebar">
            <div class="admin-logo">
                <h2>Blog CMS</h2>
            </div>
            <nav class="admin-nav">
                <a href="<?php echo BASE_URL; ?>admin/index.php">Dashboard</a>
                <a href="<?php echo BASE_URL; ?>admin/create.php">Create New Post</a>
                <a href="<?php echo BASE_URL; ?>">View Site</a>
            </nav>
        </aside>
        
        <main class="admin-main">
            <div class="admin-header">
                <h1>Edit Blog Post</h1>
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
                        <input type="text" id="title" name="title" required value="<?php echo htmlspecialchars($blog['title']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="excerpt">Excerpt</label>
                        <textarea id="excerpt" name="excerpt" rows="3" maxlength="500"><?php echo htmlspecialchars($blog['excerpt']); ?></textarea>
                        <small>Short description (max 500 characters)</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="content">Content *</label>
                        <textarea id="content" name="content" rows="15" required><?php echo htmlspecialchars($blog['content']); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="image">Featured Image</label>
                        <?php if($blog['image']): ?>
                        <div class="current-image">
                            <img src="<?php echo BASE_URL . 'uploads/' . $blog['image']; ?>" alt="Current image">
                            <p>Current Image</p>
                        </div>
                        <?php endif; ?>
                        <input type="file" id="image" name="image" accept="image/*">
                        <small>Upload a new image to replace the current one</small>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="status">Status *</label>
                            <select id="status" name="status" required>
                                <option value="draft" <?php echo $blog['status'] == 'draft' ? 'selected' : ''; ?>>Draft</option>
                                <option value="published" <?php echo $blog['status'] == 'published' ? 'selected' : ''; ?>>Published</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="featured" value="1" <?php echo $blog['featured'] ? 'checked' : ''; ?>>
                                <span>Mark as Featured</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Update Blog Post</button>
                        <a href="<?php echo BASE_URL; ?>admin/index.php" class="btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>