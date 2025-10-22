<?php
// blog-list.php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/models/Blog.php';

$blogModel = new Blog();
$blogs = $blogModel->getAll('published');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Blogs - Blog CMS</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main>
        <section class="page-header">
            <div class="container">
                <h1>All Blog Posts</h1>
                <p>Explore our latest articles and updates</p>
            </div>
        </section>
        
        <section class="blogs-section">
            <div class="container">
                <div class="blogs-grid">
                    <?php if(!empty($blogs)): ?>
                        <?php foreach($blogs as $blog): ?>
                        <article class="blog-card">
                            <?php if($blog['image']): ?>
                            <img src="<?php echo BASE_URL . 'uploads/' . $blog['image']; ?>" alt="<?php echo htmlspecialchars($blog['title']); ?>">
                            <?php endif; ?>
                            <div class="blog-content">
                                <div class="blog-meta">
                                    <span><?php echo date('M d, Y', strtotime($blog['created_at'])); ?></span>
                                    <?php if($blog['featured']): ?>
                                    <span class="badge-small">Featured</span>
                                    <?php endif; ?>
                                </div>
                                <h3><?php echo htmlspecialchars($blog['title']); ?></h3>
                                <p><?php echo htmlspecialchars($blog['excerpt']); ?></p>
                                <a href="<?php echo BASE_URL . 'blog-details.php?slug=' . $blog['slug']; ?>" class="btn-primary">Read More</a>
                            </div>
                        </article>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-posts">No blog posts available at the moment.</p>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>