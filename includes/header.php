<?php
/**
 * Header Component
 */

if (!isset($auth)) {
    require_once __DIR__ . '/../includes/init.php';
}
?>
<header class="sticky-top sticky-header">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm border-bottom border-light">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold text-primary" href="/oryzenx/">
                <i class="fas fa-globe"></i> <?php echo SITE_NAME; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/oryzenx/pages/domains.php"><i class="fas fa-globe"></i> Domains</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/oryzenx/pages/blog.php"><i class="fas fa-newspaper"></i> Blog</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/oryzenx/pages/search.php"><i class="fas fa-search"></i> Search</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/oryzenx/pages/services.php"><i class="fas fa-cogs"></i> Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/oryzenx/pages/contact.php"><i class="fas fa-envelope"></i> Contact</a>
                    </li>
                </ul>
                <div class="d-flex gap-2 ms-3">
                    <a href="/oryzenx/pages/notifications.php" class="btn btn-sm btn-outline-primary position-relative">
                        <i class="fas fa-bell"></i>
                        <?php if($auth->isLoggedIn()): ?>
                            <?php 
                                $notif_mgr = new NotificationManager($db);
                                $unread = $notif_mgr->getUnreadCount($auth->getUserId());
                                if($unread > 0):
                            ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge bg-danger rounded-pill" style="font-size: 10px;"><?php echo $unread; ?></span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </a>
                    <?php if($auth->isLoggedIn()): ?>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> <?php echo $auth->getUser()['name']; ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="/oryzenx/pages/profile.php">Profile</a></li>
                                <li><a class="dropdown-item" href="/oryzenx/pages/my-domains.php">My Domains</a></li>
                                <li><a class="dropdown-item" href="/oryzenx/pages/my-offers.php">My Offers</a></li>
                                <?php if($auth->isAdmin()): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="/oryzenx/admin/">Admin Panel</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="/oryzenx/api/logout.php">Logout</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="/oryzenx/pages/login.php" class="btn btn-sm btn-outline-primary">Login</a>
                        <a href="/oryzenx/pages/register.php" class="btn btn-sm btn-primary">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
</header>
