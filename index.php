<?php
// index.php
require_once 'config.php';
require_once 'models/Blog.php';

$blogModel = new Blog();
$featuredBlogs = $blogModel->getFeatured(3);
$recentBlogs = $blogModel->getAll('published', 6);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog CMS - Home</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main>
        <!-- Featured Blogs Section -->
        <?php if(!empty($featuredBlogs)): ?>
        <section class="featured-section">
            <div class="container">
                <h2 class="section-title">Featured Posts</h2>
                <div class="featured-grid">
                    <?php foreach($featuredBlogs as $blog): ?>
                    <article class="featured-card">
                        <?php if($blog['image']): ?>
                        <img src="<?php echo BASE_URL . 'uploads/' . $blog['image']; ?>" alt="<?php echo htmlspecialchars($blog['title']); ?>">
                        <?php endif; ?>
                        <div class="featured-content">
                            <span class="badge">Featured</span>
                            <h3><?php echo htmlspecialchars($blog['title']); ?></h3>
                            <p><?php echo htmlspecialchars($blog['excerpt']); ?></p>
                            <a href="<?php echo BASE_URL . 'blog-details.php?slug=' . $blog['slug']; ?>" class="read-more">Read More →</a>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php endif; ?>
        
        <!-- Recent Blogs Section -->
        <section class="blogs-section">
            <div class="container">
                <h2 class="section-title">Recent Posts</h2>
                <div class="blogs-grid">
                    <?php foreach($recentBlogs as $blog): ?>
                    <article class="blog-card">
                        <?php if($blog['image']): ?>
                        <img src="<?php echo BASE_URL . 'uploads/' . $blog['image']; ?>" alt="<?php echo htmlspecialchars($blog['title']); ?>">
                        <?php endif; ?>
                        <div class="blog-content">
                            <div class="blog-meta">
                                <span><?php echo date('M d, Y', strtotime($blog['created_at'])); ?></span>
                            </div>
                            <h3><?php echo htmlspecialchars($blog['title']); ?></h3>
                            <p><?php echo htmlspecialchars($blog['excerpt']); ?></p>
                            <a href="<?php echo BASE_URL . 'blog-details.php?slug=' . $blog['slug']; ?>" class="btn-primary">Read More</a>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
                <div class="text-center">
                    <a href="<?php echo BASE_URL; ?>blog-list.php" class="btn-secondary">View All Posts</a>
                </div>
            </div>
        </section>
    </main>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>