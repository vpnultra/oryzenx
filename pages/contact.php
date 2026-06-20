<?php
/**
 * Contact Page
 */

session_start();
require_once '../includes/init.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    $category = $_POST['category'] ?? '';

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'All fields are required';
    } else {
        $name = $db->escape($name);
        $email = $db->escape($email);
        $phone = $db->escape($phone);
        $subject = $db->escape($subject);
        $message = $db->escape($message);
        $category = $db->escape($category);
        $ip_address = $db->escape($_SERVER['REMOTE_ADDR']);

        $sql = "INSERT INTO contact_messages (name, email, phone, subject, message, category, ip_address, created_at)
                VALUES ('$name', '$email', '$phone', '$subject', '$message', '$category', '$ip_address', NOW())";

        if ($db->query($sql)) {
            $success = 'Thank you for contacting us! We will get back to you soon.';
        } else {
            $error = 'Failed to send message';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main class="container py-5">
        <h1 class="fw-bold mb-4 text-center"><i class="fas fa-envelope"></i> Contact Us</h1>
        <p class="text-center text-muted mb-5">Have questions? We'd love to hear from you.</p>

        <div class="row">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 text-center mb-4">
                    <div class="card-body">
                        <i class="fas fa-map-marker-alt fa-2x text-primary mb-3"></i>
                        <h6 class="fw-bold">Address</h6>
                        <p class="small text-muted">123 Business St, City, Country</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 text-center mb-4">
                    <div class="card-body">
                        <i class="fas fa-phone fa-2x text-primary mb-3"></i>
                        <h6 class="fw-bold">Phone</h6>
                        <p class="small text-muted">+1 (555) 123-4567</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 text-center mb-4">
                    <div class="card-body">
                        <i class="fas fa-envelope fa-2x text-primary mb-3"></i>
                        <h6 class="fw-bold">Email</h6>
                        <p class="small text-muted">{{ADMIN_EMAIL}}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 text-center mb-4">
                    <div class="card-body">
                        <i class="fas fa-clock fa-2x text-primary mb-3"></i>
                        <h6 class="fw-bold">Support</h6>
                        <p class="small text-muted">24/7 Available</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-md-8 offset-md-2">
                <div class="card border-0 shadow-sm rounded-4 p-5">
                    <h3 class="fw-bold mb-4">Send us a Message</h3>

                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle"></i> {{$success}}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle"></i> {{$error}}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-600">Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-600">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-600">Phone (Optional)</label>
                                <input type="tel" name="phone" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-600">Category</label>
                                <select name="category" class="form-select">
                                    <option value="">Select Category</option>
                                    <option value="support">Support</option>
                                    <option value="sales">Sales</option>
                                    <option value="partnership">Partnership</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-600">Subject</label>
                            <input type="text" name="subject" class="form-control" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-600">Message</label>
                            <textarea name="message" class="form-control" rows="5" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg rounded-3 w-100">
                            <i class="fas fa-paper-plane"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>