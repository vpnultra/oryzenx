<?php
/**
 * Domain Detail Page
 */

session_start();
require_once '../includes/init.php';

$domainMgr = new DomainManager($db);
$offerMgr = new OfferManager($db, new NotificationManager($db));

$domain_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$domain = $domainMgr->getDomainById($domain_id);

if (!$domain) {
    header('HTTP/1.0 404 Not Found');
    exit('Domain not found');
}

// Increment views
$domainMgr->incrementViews($domain_id);

$error = '';
$success = '';

// Handle offer submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_offer'])) {
    if (!$auth->isLoggedIn()) {
        $error = 'Please login to make an offer';
    } else {
        $offer_price = (float)$_POST['offer_price'];
        $email = $_POST['email'];
        $message = $_POST['message'] ?? '';

        $result = $offerMgr->createOffer($domain_id, $auth->getUserId(), $offer_price, $email, $message);
        
        if ($result['success']) {
            $success = 'Offer submitted successfully!';
        } else {
            $error = $result['message'];
        }
    }
}

// Get offers for this domain
$offers = $db->query("SELECT COUNT(*) as count FROM domain_offers WHERE domain_id = $domain_id AND status = 'accepted'")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($domain['name']); ?>.{{$domain['extension']}} - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main class="container py-5">
        <div class="row">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-5">
                        <h1 class="fw-bold mb-2">{{$domain['name']}}.{{$domain['extension']}}</h1>
                        
                        <div class="mb-4">
                            <?php if($domain['is_featured']): ?>
                                <span class="badge bg-gold me-2"><i class="fas fa-star"></i> Featured</span>
                            <?php endif; ?>
                            <?php if($domain['quality_badge'] !== 'none'): ?>
                                <span class="badge bg-primary">{{$domain['quality_badge']}}</span>
                            <?php endif; ?>
                        </div>

                        <div class="row mb-4 pb-4 border-bottom">
                            <div class="col-md-4">
                                <small class="text-muted">Asking Price</small>
                                <h3 class="text-primary fw-bold">{{formatCurrency($domain['asking_price'])}}</h3>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Rating</small>
                                <div>
                                    <?php for($i = 0; $i < 5; $i++): ?>
                                        <i class="fas fa-star <?php echo $i < $domain['rating'] ? 'text-warning' : 'text-muted'; ?>"></i>
                                    <?php endfor; ?>
                                    <span class="ms-2">{{round($domain['rating'], 1)}}/5</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Domain Status</small>
                                <p class="fw-bold">{{ucfirst($domain['sale_status'])}}</p>
                            </div>
                        </div>

                        <h5 class="fw-bold mb-3">Description</h5>
                        <p>{{$domain['description']}}</p>

                        <?php if($domain['keywords']): ?>
                            <h5 class="fw-bold mb-3 mt-5">Keywords</h5>
                            <div>
                                <?php foreach(json_decode($domain['keywords'], true) ?? [] as $keyword): ?>
                                    <span class="badge bg-light text-dark me-2 mb-2">{{$keyword}}</span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="row mt-5 pt-4 border-top">
                            <div class="col-md-4 text-center">
                                <i class="fas fa-eye fa-2x text-muted mb-2"></i>
                                <p class="text-muted">{{$domain['views']}} Views</p>
                            </div>
                            <div class="col-md-4 text-center">
                                <i class="fas fa-heart fa-2x text-danger mb-2"></i>
                                <p class="text-muted">{{$domain['favorites']}} Favorites</p>
                            </div>
                            <div class="col-md-4 text-center">
                                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                <p class="text-muted">{{$offers['count']}} Sales</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Make Offer Card -->
                <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 100px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3"><i class="fas fa-handshake"></i> Make an Offer</h5>

                        <?php if ($error): ?>
                            <div class="alert alert-danger small mb-3">{{$error}}</div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <div class="alert alert-success small mb-3">{{$success}}</div>
                        <?php endif; ?>

                        <form method="POST">
                            <input type="hidden" name="submit_offer" value="1">
                            
                            <div class="mb-3">
                                <label class="form-label small fw-600">Your Offer (USD)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="offer_price" class="form-control" placeholder="0.00" min="150" step="0.01" required>
                                </div>
                                <small class="text-muted">Minimum: $150</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-600">Your Email</label>
                                <input type="email" name="email" class="form-control" value="<?php echo $auth->isLoggedIn() ? htmlspecialchars($auth->getUser()['email']) : ''; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-600">Message (Optional)</label>
                                <textarea name="message" class="form-control" rows="3" placeholder="Add a message..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 rounded-3 fw-bold">
                                <i class="fas fa-paper-plane"></i> Submit Offer
                            </button>
                        </form>

                        <button class="btn btn-outline-secondary w-100 rounded-3 mt-2" onclick="addToFavorites()">
                            <i class="fas fa-heart"></i> Add to Favorites
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function addToFavorites() {
            alert('Added to favorites!');
        }
    </script>
</body>
</html>