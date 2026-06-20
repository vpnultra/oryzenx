<?php
/**
 * User Profile Page
 */

session_start();
require_once '../includes/init.php';

$auth->requireLogin();

$user_id = $auth->getUserId();
$user = $auth->getUser();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? $user['name'];
    $phone = $_POST['phone'] ?? '';
    $country = $_POST['country'] ?? '';
    $bio = $_POST['bio'] ?? '';

    $name = $db->escape($name);
    $phone = $db->escape($phone);
    $country = $db->escape($country);
    $bio = $db->escape($bio);

    $sql = "UPDATE users SET name = '$name', phone = '$phone', country = '$country', bio = '$bio' WHERE id = $user_id";
    
    if ($db->query($sql)) {
        $success = 'Profile updated successfully';
        $user['name'] = $name;
        $user['phone'] = $phone;
        $user['country'] = $country;
        $user['bio'] = $bio;
    } else {
        $error = 'Failed to update profile';
    }
}

// Get user statistics
$domains_owned = $db->query("SELECT COUNT(*) as count FROM domains WHERE owner_id = $user_id")->fetch_assoc()['count'];
$offers_made = $db->query("SELECT COUNT(*) as count FROM domain_offers WHERE user_id = $user_id")->fetch_assoc()['count'];
$payments = $db->query("SELECT COUNT(*) as count FROM payments WHERE user_id = $user_id")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main class="container py-5">
        <div class="row">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body text-center py-5">
                        <div class="mb-3">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['name']); ?>&size=150&background=6366f1&color=fff" alt="<?php echo htmlspecialchars($user['name']); ?>" class="rounded-circle" style="width: 150px; height: 150px;">
                        </div>
                        <h4 class="fw-bold"><?php echo htmlspecialchars($user['name']); ?></h4>
                        <p class="text-muted small"><?php echo htmlspecialchars($user['email']); ?></p>
                        <span class="badge bg-primary"><?php echo ucfirst($user['role']); ?></span>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mt-3">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Statistics</h6>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Domains Owned</span>
                                <span class="badge bg-primary"><?php echo $domains_owned; ?></span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Offers Made</span>
                                <span class="badge bg-success"><?php echo $offers_made; ?></span>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Payments</span>
                                <span class="badge bg-warning"><?php echo $payments; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h3 class="fw-bold mb-4">Edit Profile</h3>

                        <?php if ($success): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-600">Full Name</label>
                                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-600">Email</label>
                                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-600">Phone</label>
                                    <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="+1234567890">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-600">Country</label>
                                    <input type="text" name="country" class="form-control" value="<?php echo htmlspecialchars($user['country'] ?? ''); ?>" placeholder="United States">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-600">Bio</label>
                                <textarea name="bio" class="form-control" rows="4" placeholder="Tell us about yourself..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg rounded-3">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>