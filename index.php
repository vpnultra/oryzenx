<?php
/**
 * Oryzenx Main Index Page
 */

session_start();
require_once 'config/config.php';
require_once 'functions/Database.php';
require_once 'functions/Auth.php';
require_once 'functions/DomainManager.php';
require_once 'functions/BlogManager.php';
require_once 'functions/helpers.php';

$db = new Database();
$db->connect();
$auth = new Auth($db);
$domainMgr = new DomainManager($db);
$blogMgr = new BlogManager($db);

// Get featured domains
$featured_domains = $domainMgr->getFeaturedDomains(6);

// Get latest blog posts
$latest_posts = $blogMgr->getAllPosts(6, 0, 'published', 'en');

// Get partner logos
$partners_result = $db->query("SELECT * FROM partners WHERE is_active = 1 ORDER BY order ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Premium Domain Marketplace</title>
    <meta name="description" content="<?php echo SITE_DESCRIPTION; ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Header Navigation -->
    <header class="sticky-header">
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand fw-bold" href="/">
                    <i class="fas fa-globe text-primary"></i> <?php echo SITE_NAME; ?>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="pages/domains.php">Domains</a></li>
                        <li class="nav-item"><a class="nav-link" href="pages/blog.php">Blog</a></li>
                        <li class="nav-item"><a class="nav-link" href="pages/search.php">Search</a></li>
                        <li class="nav-item"><a class="nav-link" href="pages/services.php">Services</a></li>
                        <li class="nav-item"><a class="nav-link" href="pages/contact.php">Contact</a></li>
                    </ul>
                    <div class="ms-3 d-flex gap-2">
                        <a href="pages/notifications.php" class="btn btn-sm btn-outline-primary position-relative">
                            <i class="fas fa-bell"></i>
                            <?php if($auth->isLoggedIn()): ?>
                                <?php 
                                    $notif_count = $db->query("SELECT COUNT(*) as count FROM notifications WHERE to_user_id = {$auth->getUserId()} AND is_read = 0")->fetch_assoc()['count'];
                                    if($notif_count > 0):
                                ?>
                                    <span class="position-absolute top-0 start-100 translate-middle badge bg-danger rounded-pill">{{$notif_count}}</span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </a>
                        <?php if($auth->isLoggedIn()): ?>
                            <a href="pages/profile.php" class="btn btn-sm btn-primary">Profile</a>
                            <a href="api/logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
                        <?php else: ?>
                            <a href="pages/login.php" class="btn btn-sm btn-outline-primary">Login</a>
                            <a href="pages/register.php" class="btn btn-sm btn-primary">Register</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero-section" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 100px 0;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Find Your Premium Domain</h1>
                    <p class="lead mb-4">Access thousands of premium domains from trusted sellers worldwide</p>
                    <div class="search-form">
                        <form action="pages/search.php" method="GET" class="d-flex gap-2">
                            <input type="text" name="q" class="form-control form-control-lg" placeholder="Search domains..." required>
                            <button type="submit" class="btn btn-lg btn-light fw-bold">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </form>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <div style="font-size: 120px; opacity: 0.2;">
                        <i class="fas fa-globe"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Domains Section -->
    <section class="py-80">
        <div class="container">
            <h2 class="section-title">Featured Domains</h2>
            <div class="row">
                <?php while($domain = $featured_domains->fetch_assoc()): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card domain-card h-100 shadow-sm border-0 rounded-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title fw-bold text-truncate">{{$domain['name']}}.{{$domain['extension']}}</h5>
                                    <?php if($domain['quality_badge'] !== 'none'): ?>
                                        <span class="badge bg-gold">{{$domain['quality_badge']}}</span>
                                    <?php endif; ?>
                                </div>
                                <p class="text-muted small">{{truncate($domain['description'], 100)}}</p>
                                <div class="my-3">
                                    <p class="h4 text-primary fw-bold">{{formatCurrency($domain['asking_price'])}}</p>
                                </div>
                                <div class="row text-center text-muted small">
                                    <div class="col-4">
                                        <i class="fas fa-eye"></i> {{$domain['views']}}
                                    </div>
                                    <div class="col-4">
                                        <i class="fas fa-star text-warning"></i> {{round($domain['rating'], 1)}}
                                    </div>
                                    <div class="col-4">
                                        <i class="fas fa-heart"></i> {{$domain['favorites']}}
                                    </div>
                                </div>
                                <a href="pages/domain-detail.php?id={{$domain['id']}}" class="btn btn-primary w-100 mt-3 rounded-3">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            <div class="text-center mt-5">
                <a href="pages/domains.php" class="btn btn-lg btn-primary rounded-pill px-5">View All Domains</a>
            </div>
        </div>
    </section>

    <!-- Latest Blog Posts -->
    <section class="py-80" style="background: #f8f9fa;">
        <div class="container">
            <h2 class="section-title">Latest Articles</h2>
            <div class="row">
                <?php while($post = $latest_posts->fetch_assoc()): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card blog-card h-100 shadow-sm border-0 rounded-4">
                            <?php if($post['featured_image']): ?>
                                <img src="uploads/{{$post['featured_image']}}" class="card-img-top" alt="{{$post['title']}}" style="height: 200px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-light" style="height: 200px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-image text-muted" style="font-size: 48px;"></i>
                                </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title fw-bold">{{$post['title']}}</h5>
                                <p class="text-muted small">By {{$post['author_name']}} • {{timeAgo($post['published_at'])}}</p>
                                <p class="card-text">{{truncate($post['excerpt'] ?? $post['content'], 100)}}</p>
                                <a href="pages/blog-detail.php?slug={{$post['slug']}}" class="btn btn-outline-primary btn-sm">Read More</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            <div class="text-center mt-5">
                <a href="pages/blog.php" class="btn btn-lg btn-primary rounded-pill px-5">View All Articles</a>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="py-80">
        <div class="container">
            <h2 class="section-title text-center mb-5">Our Services</h2>
            <div class="row">
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="service-card p-4 rounded-4 text-center h-100 border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <i class="fas fa-globe fa-2x mb-3"></i>
                        <h5 class="fw-bold">Domain Marketplace</h5>
                        <p class="small">Buy and sell premium domains with secure transactions</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="service-card p-4 rounded-4 text-center h-100 border-0 shadow-sm">
                        <i class="fas fa-code fa-2x mb-3 text-primary"></i>
                        <h5 class="fw-bold">Web Development</h5>
                        <p class="small text-muted">Custom web solutions for your business needs</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="service-card p-4 rounded-4 text-center h-100 border-0 shadow-sm">
                        <i class="fas fa-pencil-ruler fa-2x mb-3 text-success"></i>
                        <h5 class="fw-bold">UI/UX Design</h5>
                        <p class="small text-muted">Beautiful and intuitive interface design</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Partners Section -->
    <section class="py-80" style="background: #f8f9fa;">
        <div class="container">
            <h2 class="section-title text-center mb-5">Our Partners</h2>
            <div class="partners-slider">
                <div class="row align-items-center">
                    <?php while($partner = $partners_result->fetch_assoc()): ?>
                        <div class="col-md-6 col-lg-3 mb-4 text-center">
                            <a href="{{$partner['website']}}" target="_blank" class="partner-link">
                                <img src="uploads/{{$partner['logo']}}" alt="{{$partner['name']}}" class="img-fluid" style="max-height: 80px; opacity: 0.7; transition: opacity 0.3s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.7'">
                            </a>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-80" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <div class="container text-center">
            <h2 class="display-5 fw-bold mb-4">Ready to Find Your Perfect Domain?</h2>
            <p class="lead mb-5">Join thousands of satisfied customers in our marketplace</p>
            <a href="pages/domains.php" class="btn btn-light btn-lg rounded-pill px-5 fw-bold">Start Browsing Now</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-3 mb-4">
                    <h5 class="fw-bold mb-3"><i class="fas fa-globe"></i> {{SITE_NAME}}</h5>
                    <p class="text-muted">Premium domain marketplace connecting buyers and sellers worldwide</p>
                </div>
                <div class="col-md-3 mb-4">
                    <h6 class="fw-bold">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="pages/domains.php" class="text-muted text-decoration-none">Domains</a></li>
                        <li><a href="pages/blog.php" class="text-muted text-decoration-none">Blog</a></li>
                        <li><a href="pages/services.php" class="text-muted text-decoration-none">Services</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h6 class="fw-bold">Legal</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-muted text-decoration-none">Terms of Service</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h6 class="fw-bold">Contact</h6>
                    <ul class="list-unstyled">
                        <li><a href="mailto:{{ADMIN_EMAIL}}" class="text-muted text-decoration-none">{{ADMIN_EMAIL}}</a></li>
                        <li class="text-muted">Available 24/7</li>
                    </ul>
                </div>
            </div>
            <hr class="bg-secondary">
            <div class="text-center text-muted">
                <p>&copy; {{date('Y')}} {{SITE_NAME}}. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>