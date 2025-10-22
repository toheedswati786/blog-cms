<?php
// blog-details.php
require_once 'config.php';
require_once 'models/Blog.php';

if(!isset($_GET['slug'])) {
    redirect('blog-list.php');
}

$blogModel = new Blog();
$blog = $blogModel->getBySlug($_GET['slug']);

if(!$blog) {
    redirect('blog-list.php');
}

// Get related blogs (same featured status)
$relatedBlogs = $blogModel->getAll('published', 3);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($blog['title']); ?> - Blog CMS</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main>
        <article class="blog-detail">
            <div class="container">
                <div class="blog-header">
                    <?php if($blog['featured']): ?>
                    <span class="badge">Featured</span>
                    <?php endif; ?>
                    <h1><?php echo htmlspecialchars($blog['title']); ?></h1>
                    <div class="blog-meta">
                        <span>Published on <?php echo date('F d, Y', strtotime($blog['created_at'])); ?></span>
                        <?php if($blog['updated_at'] != $blog['created_at']): ?>
                        <span>Updated on <?php echo date('F d, Y', strtotime($blog['updated_at'])); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if($blog['image']): ?>
                <div class="blog-image">
                    <img src="<?php echo BASE_URL . 'uploads/' . $blog['image']; ?>" alt="<?php echo htmlspecialchars($blog['title']); ?>">
                </div>
                <?php endif; ?>
                
                <div class="blog-body">
                    <?php echo nl2br(htmlspecialchars($blog['content'])); ?>
                </div>
                
                <div class="blog-footer">
                    <a href="<?php echo BASE_URL; ?>blog-list.php" class="btn-secondary">← Back to All Posts</a>
                </div>
            </div>
        </article>
        
        <?php if(!empty($relatedBlogs)): ?>
        <section class="related-blogs">
            <div class="container">
                <h2 class="section-title">Related Posts</h2>
                <div class="blogs-grid">
                    <?php foreach(array_slice($relatedBlogs, 0, 3) as $relatedBlog): ?>
                        <?php if($relatedBlog['id'] != $blog['id']): ?>
                        <article class="blog-card">
                            <?php if($relatedBlog['image']): ?>
                            <img src="<?php echo BASE_URL . 'uploads/' . $relatedBlog['image']; ?>" alt="<?php echo htmlspecialchars($relatedBlog['title']); ?>">
                            <?php endif; ?>
                            <div class="blog-content">
                                <h3><?php echo htmlspecialchars($relatedBlog['title']); ?></h3>
                                <p><?php echo htmlspecialchars($relatedBlog['excerpt']); ?></p>
                                <a href="<?php echo BASE_URL . 'blog-details.php?slug=' . $relatedBlog['slug']; ?>" class="btn-primary">Read More</a>
                            </div>
                        </article>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php endif; ?>
    </main>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>