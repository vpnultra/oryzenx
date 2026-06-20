<?php
/**
 * Blog Listing Page
 */

session_start();
require_once '../includes/init.php';

$blogMgr = new BlogManager($db);

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$category = $_GET['category'] ?? '';
$language = $_GET['lang'] ?? 'en';
$limit = ITEMS_PER_PAGE;
$offset = ($page - 1) * $limit;

$posts = $blogMgr->getAllPosts($limit, $offset, 'published', $language);
$categories = $blogMgr->getCategories();
$total = $db->query("SELECT COUNT(*) as count FROM blog_posts WHERE status = 'published' AND language = '$language'")->fetch_assoc()['count'];
$total_pages = ceil($total / $limit);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main class="container py-5">
        <h1 class="fw-bold mb-4"><i class="fas fa-newspaper"></i> Blog & Articles</h1>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="btn-group" role="group">
                    <a href="?lang=en" class="btn btn-<?php echo $language == 'en' ? 'primary' : 'outline-primary'; ?>">English</a>
                    <a href="?lang=bn" class="btn btn-<?php echo $language == 'bn' ? 'primary' : 'outline-primary'; ?>">Bengali</a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="dropdown float-end">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-filter"></i> Categories
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="?lang=<?php echo $language; ?>">All Categories</a></li>
                        <?php while($cat = $categories->fetch_assoc()): ?>
                            <li><a class="dropdown-item" href="?category=<?php echo $cat['id']; ?>&lang=<?php echo $language; ?>"><?php echo htmlspecialchars($cat['name']); ?></a></li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <?php while($post = $posts->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card blog-card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                        <?php if($post['featured_image']): ?>
                            <img src="../uploads/{{$post['featured_image']}}" class="card-img-top" alt="{{$post['title']}}" style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <div class="bg-light" style="height: 200px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-image text-muted" style="font-size: 48px;"></i>
                            </div>
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <?php if($post['category_name']): ?>
                                <span class="badge bg-primary mb-2" style="width: fit-content;">{{$post['category_name']}}</span>
                            <?php endif; ?>
                            <h5 class="card-title fw-bold">{{$post['title']}}</h5>
                            <p class="text-muted small mb-3">
                                <i class="fas fa-user"></i> {{$post['author_name']}} 
                                <span class="ms-2"><i class="fas fa-calendar"></i> {{date('M d, Y', strtotime($post['published_at']))}}</span>
                            </p>
                            <p class="card-text flex-grow-1">{{truncate($post['excerpt'] ?? $post['content'], 100)}}</p>
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <small class="text-muted"><i class="fas fa-eye"></i> {{$post['views']}} views</small>
                                <a href="blog-detail.php?slug={{$post['slug']}}" class="btn btn-sm btn-primary rounded-3">Read More</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Pagination -->
        <?php if($total_pages > 1): ?>
            <nav aria-label="Page navigation" class="mt-5">
                <ul class="pagination justify-content-center">
                    <?php if($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page={{$page - 1}}&lang={{$language}}">Previous</a>
                        </li>
                    <?php endif; ?>

                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item {{$i == $page ? 'active' : ''}}">
                            <a class="page-link" href="?page={{$i}}&lang={{$language}}">{{$i}}</a>
                        </li>
                    <?php endfor; ?>

                    <?php if($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page={{$page + 1}}&lang={{$language}}">Next</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </main>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>