<?php
/**
 * Services Page
 */

session_start();
require_once '../includes/init.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main class="container py-5">
        <h1 class="fw-bold mb-4 text-center"><i class="fas fa-cogs"></i> Our Services</h1>
        <p class="text-center text-muted mb-5">Comprehensive solutions for your domain marketplace needs</p>

        <div class="row">
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card service-card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); height: 150px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-globe text-white" style="font-size: 64px;"></i>
                    </div>
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Domain Marketplace</h5>
                        <p class="text-muted mb-3">Browse and purchase premium domains from trusted sellers. Secure transactions with escrow protection.</p>
                        <ul class="list-unstyled small">
                            <li><i class="fas fa-check text-success me-2"></i>Thousands of domains</li>
                            <li><i class="fas fa-check text-success me-2"></i>Secure payments</li>
                            <li><i class="fas fa-check text-success me-2"></i>24/7 support</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card service-card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                    <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); height: 150px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-blog text-white" style="font-size: 64px;"></i>
                    </div>
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Blog Platform</h5>
                        <p class="text-muted mb-3">Publish and manage blog posts with rich text editor. Multi-language support with SEO optimization.</p>
                        <ul class="list-unstyled small">
                            <li><i class="fas fa-check text-success me-2"></i>Rich text editor</li>
                            <li><i class="fas fa-check text-success me-2"></i>Multi-language</li>
                            <li><i class="fas fa-check text-success me-2"></i>SEO tools</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card service-card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                    <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); height: 150px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-lock text-white" style="font-size: 64px;"></i>
                    </div>
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Secure Payments</h5>
                        <p class="text-muted mb-3">Accept cryptocurrency and traditional payments. Advanced security and fraud protection included.</p>
                        <ul class="list-unstyled small">
                            <li><i class="fas fa-check text-success me-2"></i>Bitcoin & Ethereum</li>
                            <li><i class="fas fa-check text-success me-2"></i>SSL encryption</li>
                            <li><i class="fas fa-check text-success me-2"></i>Fraud protection</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card service-card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                    <div style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); height: 150px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-chart-line text-white" style="font-size: 64px;"></i>
                    </div>
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Analytics Dashboard</h5>
                        <p class="text-muted mb-3">Track your domain performance with real-time analytics and detailed reports.</p>
                        <ul class="list-unstyled small">
                            <li><i class="fas fa-check text-success me-2"></i>Real-time data</li>
                            <li><i class="fas fa-check text-success me-2"></i>Custom reports</li>
                            <li><i class="fas fa-check text-success me-2"></i>Export options</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card service-card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                    <div style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); height: 150px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-users text-white" style="font-size: 64px;"></i>
                    </div>
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Community Support</h5>
                        <p class="text-muted mb-3">Connect with other domain enthusiasts and get expert advice from our team.</p>
                        <ul class="list-unstyled small">
                            <li><i class="fas fa-check text-success me-2"></i>Expert guidance</li>
                            <li><i class="fas fa-check text-success me-2"></i>Community forums</li>
                            <li><i class="fas fa-check text-success me-2"></i>Email support</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card service-card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                    <div style="background: linear-gradient(135deg, #ff9a56 0%, #ff6a88 100%); height: 150px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-rocket text-white" style="font-size: 64px;"></i>
                    </div>
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Premium Features</h5>
                        <p class="text-muted mb-3">Unlock advanced features including domain valuation and market analysis tools.</p>
                        <ul class="list-unstyled small">
                            <li><i class="fas fa-check text-success me-2"></i>Domain valuation</li>
                            <li><i class="fas fa-check text-success me-2"></i>Market insights</li>
                            <li><i class="fas fa-check text-success me-2"></i>Priority support</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>