<?php
/**
 * Domains Listing Page
 */

session_start();
require_once '../includes/init.php';

$domainMgr = new DomainManager($db);

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = ITEMS_PER_PAGE;
$offset = ($page - 1) * $limit;

$filters = [
    'search' => $_GET['search'] ?? '',
    'category' => $_GET['category'] ?? '',
    'price_min' => $_GET['price_min'] ?? '',
    'price_max' => $_GET['price_max'] ?? '',
    'featured_only' => $_GET['featured'] ?? ''
];

$domains = $domainMgr->getAllDomains($limit, $offset, $filters);
$total = $domainMgr->getTotalDomains();
$total_pages = ceil($total / $limit);
$categories = $domainMgr->getCategories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Domains - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main class="container py-5">
        <h1 class="fw-bold mb-4"><i class="fas fa-globe"></i> Browse Domains</h1>

        <div class="row">
            <!-- Filters Sidebar -->
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold mb-3">Filters</h5>
                    <form method="GET">
                        <div class="mb-3">
                            <label class="form-label fw-600">Search</label>
                            <input type="text" name="search" class="form-control" placeholder="Domain name..." value="<?php echo htmlspecialchars($filters['search']); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-600">Category</label>
                            <select name="category" class="form-select">
                                <option value="">All Categories</option>
                                <?php while($cat = $categories->fetch_assoc()): ?>
                                    <option value="<?php echo htmlspecialchars($cat['category']); ?>" <?php echo $filters['category'] == $cat['category'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['category']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-600">Price Range</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text">$</span>
                                <input type="number" name="price_min" class="form-control" placeholder="Min" value="<?php echo htmlspecialchars($filters['price_min']); ?>">
                            </div>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="price_max" class="form-control" placeholder="Max" value="<?php echo htmlspecialchars($filters['price_max']); ?>">
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input type="checkbox" name="featured" class="form-check-input" value="1" <?php echo $filters['featured_only'] ? 'checked' : ''; ?> id="featured">
                                <label class="form-check-label" for="featured">Featured Only</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-3">
                            <i class="fas fa-filter"></i> Apply Filters
                        </button>
                    </form>
                </div>
            </div>

            <!-- Domains Grid -->
            <div class="col-md-9">
                <div class="row">
                    <?php while($domain = $domains->fetch_assoc()): ?>
                        <div class="col-md-6 col-lg-6 mb-4">
                            <div class="card domain-card h-100 shadow-sm border-0 rounded-4">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h5 class="card-title fw-bold">{{$domain['name']}}.{{$domain['extension']}}</h5>
                                            <?php if($domain['category']): ?>
                                                <span class="badge bg-light text-dark">{{$domain['category']}}</span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if($domain['is_featured']): ?>
                                            <span class="badge bg-gold"><i class="fas fa-star"></i> Featured</span>
                                        <?php endif; ?>
                                    </div>

                                    <p class="text-muted small">{{truncate($domain['description'], 80)}}</p>

                                    <div class="my-3">
                                        <p class="h5 text-primary fw-bold">{{formatCurrency($domain['asking_price'])}}</p>
                                    </div>

                                    <div class="row text-center text-muted small mb-3">
                                        <div class="col-4">
                                            <i class="fas fa-eye"></i><br>{{$domain['views']}}
                                        </div>
                                        <div class="col-4">
                                            <i class="fas fa-star text-warning"></i><br>{{round($domain['rating'], 1)}}
                                        </div>
                                        <div class="col-4">
                                            <i class="fas fa-heart"></i><br>{{$domain['favorites']}}
                                        </div>
                                    </div>

                                    <a href="domain-detail.php?id={{$domain['id']}}" class="btn btn-primary w-100 rounded-3">
                                        View Details
                                    </a>
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
                                    <a class="page-link" href="?page={{$page - 1}}<?php echo !empty($filters['search']) ? '&search=' . urlencode($filters['search']) : ''; ?>">Previous</a>
                                </li>
                            <?php endif; ?>

                            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item {{$i == $page ? 'active' : ''}}">
                                    <a class="page-link" href="?page={{$i}}<?php echo !empty($filters['search']) ? '&search=' . urlencode($filters['search']) : ''; ?>">{{$i}}</a>
                                </li>
                            <?php endfor; ?>

                            <?php if($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page={{$page + 1}}<?php echo !empty($filters['search']) ? '&search=' . urlencode($filters['search']) : ''; ?>">Next</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>