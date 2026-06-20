<?php
/**
 * Search Page
 */

session_start();
require_once '../includes/init.php';

$searchMgr = new SearchManager($db);

$query = $_GET['q'] ?? '';
$type = $_GET['type'] ?? 'all';
$results = [];

if (!empty($query)) {
    $results = $searchMgr->search($query, $type, 50);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main class="container py-5">
        <h1 class="fw-bold mb-4">
            <i class="fas fa-search"></i> Search Results
        </h1>

        <div class="mb-4">
            <form method="GET" class="d-flex gap-2">
                <input type="text" name="q" class="form-control form-control-lg" placeholder="Search domains, posts, categories..." value="<?php echo htmlspecialchars($query); ?>" required>
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-search"></i> Search
                </button>
            </form>
        </div>

        <?php if (!empty($query)): ?>
            <p class="text-muted mb-4">Found {{count($results)}} results for "{{htmlspecialchars($query)}}"</p>

            <?php if (empty($results)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No results found. Try different keywords.
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach($results as $result): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <?php if($result['type'] === 'domain'): ?>
                                <div class="card domain-card h-100 shadow-sm border-0 rounded-4">
                                    <div class="card-body">
                                        <h5 class="card-title fw-bold">{{$result['name']}}.{{$result['extension']}}</h5>
                                        <p class="text-primary h5 fw-bold">{{formatCurrency($result['asking_price'])}}</p>
                                        <p class="card-text text-muted small">{{truncate($result['image'] ?? '', 100)}}</p>
                                        <a href="domain-detail.php?id={{$result['id']}}" class="btn btn-primary btn-sm rounded-3">View Domain</a>
                                    </div>
                                </div>
                            <?php elseif($result['type'] === 'post'): ?>
                                <div class="card blog-card h-100 shadow-sm border-0 rounded-4">
                                    <div class="card-body">
                                        <h5 class="card-title fw-bold">{{$result['title']}}</h5>
                                        <p class="text-muted small"><i class="fas fa-eye"></i> {{$result['views']}} views</p>
                                        <p class="card-text text-muted small">{{truncate($result['excerpt'], 100)}}</p>
                                        <a href="blog-detail.php?slug={{$result['slug']}}" class="btn btn-primary btn-sm rounded-3">Read Article</a>
                                    </div>
                                </div>
                            <?php elseif($result['type'] === 'category'): ?>
                                <div class="card border-0 shadow-sm rounded-4">
                                    <div class="card-body text-center">
                                        <i class="fas fa-folder fa-2x text-primary mb-2"></i>
                                        <h5 class="card-title fw-bold">{{$result['name']}}</h5>
                                        <p class="text-muted">{{$result['posts_count']}} posts</p>
                                        <a href="blog.php?category={{$result['id']}}" class="btn btn-outline-primary btn-sm rounded-3">Browse Category</a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Enter a search term to find domains, posts, and more.
            </div>

            <!-- Popular Searches -->
            <h4 class="fw-bold mt-5 mb-3">Popular Searches</h4>
            <div class="row">
                <?php 
                    $popular = $db->query("SELECT keyword FROM search_keywords ORDER BY search_count DESC LIMIT 10");
                    while($keyword = $popular->fetch_assoc()): 
                ?>
                    <div class="col-auto mb-2">
                        <a href="?q={{urlencode($keyword['keyword'])}}" class="btn btn-outline-secondary btn-sm rounded-3">{{$keyword['keyword']}}</a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </main>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>