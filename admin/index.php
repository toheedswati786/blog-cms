<?php
// admin/index.php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/Blog.php';


$blogModel = new Blog();

// Handle delete action
if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $blogModel->delete($_GET['id']);
    redirect('admin/index.php?msg=deleted');
}

// Get all blogs (including drafts)
$allBlogs = $blogModel->getAll('published');
$drafts = $blogModel->getAll('draft');
$blogs = array_merge($allBlogs, $drafts);

$message = '';
if(isset($_GET['msg'])) {
    switch($_GET['msg']) {
        case 'created':
            $message = 'Blog created successfully!';
            break;
        case 'updated':
            $message = 'Blog updated successfully!';
            break;
        case 'deleted':
            $message = 'Blog deleted successfully!';
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Blog CMS</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <aside class="admin-sidebar">
            <div class="admin-logo">
                <h2>Blog CMS</h2>
            </div>
            <nav class="admin-nav">
                <a href="<?php echo BASE_URL; ?>admin/index.php" class="active">Dashboard</a>
                <a href="<?php echo BASE_URL; ?>admin/create.php">Create New Post</a>
                <a href="<?php echo BASE_URL; ?>">View Site</a>
            </nav>
        </aside>
        
        <main class="admin-main">
            <div class="admin-header">
                <h1>Blog Posts</h1>
                <a href="<?php echo BASE_URL; ?>admin/create.php" class="btn-primary">+ Create New</a>
            </div>
            
            <?php if($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <div class="admin-content">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Featured</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($blogs)): ?>
                            <?php foreach($blogs as $blog): ?>
                            <tr>
                                <td><?php echo $blog['id']; ?></td>
                                <td><?php echo htmlspecialchars($blog['title']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $blog['status']; ?>">
                                        <?php echo ucfirst($blog['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo $blog['featured'] ? '⭐ Yes' : 'No'; ?></td>
                                <td><?php echo date('M d, Y', strtotime($blog['created_at'])); ?></td>
                                <td class="actions">
                                    <?php if($blog['status'] == 'published'): ?>
                                    <a href="<?php echo BASE_URL . 'blog-details.php?slug=' . $blog['slug']; ?>" class="btn-sm btn-view" target="_blank">View</a>
                                    <?php endif; ?>
                                    <a href="<?php echo BASE_URL; ?>admin/edit.php?id=<?php echo $blog['id']; ?>" class="btn-sm btn-edit">Edit</a>
                                    <a href="<?php echo BASE_URL; ?>admin/index.php?action=delete&id=<?php echo $blog['id']; ?>" class="btn-sm btn-delete" onclick="return confirm('Are you sure you want to delete this blog?')">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No blog posts found. <a href="<?php echo BASE_URL; ?>admin/create.php">Create one now!</a></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>