<?php
/**
 * User Registration Page
 */

session_start();
require_once '../includes/init.php';

if ($auth->isLoggedIn()) {
    header('Location: /oryzenx/');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($name) || empty($email) || empty($password)) {
        $error = 'All fields are required';
    } else {
        $result = $auth->register($name, $email, $password, $confirm_password);
        
        if ($result['success']) {
            $success = 'Registration successful! Please login.';
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/auth.css" rel="stylesheet">
</head>
<body class="bg-gradient">
    <div class="container">
        <div class="row min-vh-100 align-items-center justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold text-primary"><i class="fas fa-globe"></i> <?php echo SITE_NAME; ?></h2>
                            <p class="text-muted">Create Your Account</p>
                        </div>

                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" class="needs-validation">
                            <div class="mb-3">
                                <label class="form-label fw-600">Full Name</label>
                                <input type="text" name="name" class="form-control form-control-lg" placeholder="Your Name" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-600">Email Address</label>
                                <input type="email" name="email" class="form-control form-control-lg" placeholder="your@email.com" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-600">Password</label>
                                <input type="password" name="password" class="form-control form-control-lg" placeholder="••••••••" required>
                                <small class="text-muted">At least 8 characters with uppercase, lowercase, number and special character</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-600">Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-control form-control-lg" placeholder="••••••••" required>
                            </div>

                            <div class="form-check mb-4">
                                <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                                <label class="form-check-label" for="terms">
                                    I agree to the <a href="#" class="text-primary text-decoration-none">Terms of Service</a> and <a href="#" class="text-primary text-decoration-none">Privacy Policy</a>
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-3 fw-bold mb-3">
                                <i class="fas fa-user-plus"></i> Create Account
                            </button>
                        </form>

                        <hr>

                        <p class="text-center text-muted mb-0">
                            Already have an account? <a href="login.php" class="text-primary fw-bold text-decoration-none">Login here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>