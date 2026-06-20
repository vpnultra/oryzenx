<?php
/**
 * Oryzenx Installation Script
 */

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$current_step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$errors = [];
$success = [];

if ($current_step == 1 && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['install_step1'] = true;
    header('Location: install.php?step=2');
    exit;
}

if ($current_step == 2 && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $db_host = trim($_POST['db_host'] ?? '');
    $db_name = trim($_POST['db_name'] ?? '');
    $db_user = trim($_POST['db_user'] ?? '');
    $db_pass = trim($_POST['db_pass'] ?? '');

    if (empty($db_host) || empty($db_name) || empty($db_user)) {
        $errors[] = 'All database fields are required';
    } else {
        $conn = @mysqli_connect($db_host, $db_user, $db_pass);
        
        if (!$conn) {
            $errors[] = 'Database connection failed: ' . mysqli_connect_error();
        } else {
            if (!mysqli_select_db($conn, $db_name)) {
                if (!mysqli_query($conn, "CREATE DATABASE `$db_name`")) {
                    $errors[] = 'Failed to create database';
                }
            }

            if (empty($errors)) {
                $_SESSION['db_host'] = $db_host;
                $_SESSION['db_name'] = $db_name;
                $_SESSION['db_user'] = $db_user;
                $_SESSION['db_pass'] = $db_pass;
                header('Location: install.php?step=3');
                exit;
            }
        }
    }
}

if ($current_step == 3 && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $db_host = $_SESSION['db_host'] ?? '';
    $db_name = $_SESSION['db_name'] ?? '';
    $db_user = $_SESSION['db_user'] ?? '';
    $db_pass = $_SESSION['db_pass'] ?? '';

    $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
    
    if (!$conn) {
        $errors[] = 'Database connection failed';
    } else {
        $sql_file = __DIR__ . '/db/database.sql';
        
        if (!file_exists($sql_file)) {
            $errors[] = 'Database SQL file not found';
        } else {
            $sql = file_get_contents($sql_file);
            $statements = array_filter(array_map('trim', explode(';', $sql)));

            foreach ($statements as $statement) {
                if (!empty($statement)) {
                    if (!mysqli_query($conn, $statement)) {
                        $errors[] = 'Error: ' . mysqli_error($conn);
                        break;
                    }
                }
            }

            if (empty($errors)) {
                $_SESSION['install_step3'] = true;
                header('Location: install.php?step=4');
                exit;
            }
        }
    }
}

if ($current_step == 4 && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $admin_name = trim($_POST['admin_name'] ?? '');
    $admin_email = trim($_POST['admin_email'] ?? '');
    $admin_password = trim($_POST['admin_password'] ?? '');
    $admin_confirm = trim($_POST['admin_confirm'] ?? '');

    if (empty($admin_name) || empty($admin_email) || empty($admin_password)) {
        $errors[] = 'All fields are required';
    } elseif ($admin_password !== $admin_confirm) {
        $errors[] = 'Passwords do not match';
    } elseif (strlen($admin_password) < 8) {
        $errors[] = 'Password must be at least 8 characters';
    } else {
        $db_host = $_SESSION['db_host'] ?? '';
        $db_name = $_SESSION['db_name'] ?? '';
        $db_user = $_SESSION['db_user'] ?? '';
        $db_pass = $_SESSION['db_pass'] ?? '';

        $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
        
        if ($conn) {
            $hashed_password = password_hash($admin_password, PASSWORD_BCRYPT);
            $admin_email = mysqli_real_escape_string($conn, $admin_email);
            $admin_name = mysqli_real_escape_string($conn, $admin_name);

            $sql = "INSERT INTO users (name, email, password, role, status, created_at) 
                    VALUES ('$admin_name', '$admin_email', '$hashed_password', 'admin', 'active', NOW())";

            if (mysqli_query($conn, $sql)) {
                $_SESSION['install_step4'] = true;
                header('Location: install.php?step=5');
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oryzenx Installation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 20px; box-shadow: 0 25px 50px rgba(0,0,0,0.15); max-width: 700px; width: 90%; overflow: hidden;">
        <div style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; padding: 40px; text-align: center;">
            <h1 style="font-size: 32px; font-weight: 700; margin-bottom: 10px;">Oryzenx</h1>
            <p style="font-size: 14px; opacity: 0.9;">Installation Wizard - Step <?php echo $current_step; ?> of 5</p>
        </div>

        <div style="padding: 40px;">
            <?php if (!empty($errors)): ?>
                <?php foreach ($errors as $error): ?>
                    <div style="background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 15px; font-size: 14px;">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if ($current_step == 1): ?>
                <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 20px; color: #1e293b;">System Requirements</h3>
                <div style="padding: 20px; background: #f8fafc; border-radius: 8px;">
                    <p><strong>PHP Version:</strong> <?php echo PHP_VERSION; ?></p>
                    <p><strong>MySQLi:</strong> <?php echo function_exists('mysqli_connect') ? 'OK' : 'MISSING'; ?></p>
                    <p><strong>JSON Extension:</strong> <?php echo extension_loaded('json') ? 'OK' : 'MISSING'; ?></p>
                </div>
                <form method="POST" style="margin-top: 30px;">
                    <button type="submit" class="btn btn-primary" style="background: #6366f1; border: none; padding: 12px 32px; border-radius: 8px; font-weight: 600;">Continue to Database</button>
                </form>
            <?php endif; ?>

            <?php if ($current_step == 2): ?>
                <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 20px; color: #1e293b;">Database Configuration</h3>
                <form method="POST">
                    <div style="margin-bottom: 20px;">
                        <label style="font-weight: 600; display: block; margin-bottom: 8px; color: #1e293b;">Database Host</label>
                        <input type="text" name="db_host" value="localhost" class="form-control" style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; font-size: 14px;" required>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="font-weight: 600; display: block; margin-bottom: 8px; color: #1e293b;">Database Name</label>
                        <input type="text" name="db_name" class="form-control" style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; font-size: 14px;" required>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="font-weight: 600; display: block; margin-bottom: 8px; color: #1e293b;">Database Username</label>
                        <input type="text" name="db_user" class="form-control" style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; font-size: 14px;" required>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="font-weight: 600; display: block; margin-bottom: 8px; color: #1e293b;">Database Password</label>
                        <input type="password" name="db_pass" class="form-control" style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; font-size: 14px;">
                    </div>
                    <button type="submit" class="btn btn-primary" style="background: #6366f1; border: none; padding: 12px 32px; border-radius: 8px; font-weight: 600; margin-top: 20px;">Test Connection</button>
                </form>
            <?php endif; ?>

            <?php if ($current_step == 3): ?>
                <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 20px; color: #1e293b;">Create Database Tables</h3>
                <p style="color: #64748b; margin-bottom: 20px;">This will create all necessary database tables.</p>
                <form method="POST">
                    <button type="submit" class="btn btn-primary" style="background: #6366f1; border: none; padding: 12px 32px; border-radius: 8px; font-weight: 600;">Create Tables</button>
                </form>
            <?php endif; ?>

            <?php if ($current_step == 4): ?>
                <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 20px; color: #1e293b;">Create Admin Account</h3>
                <form method="POST">
                    <div style="margin-bottom: 20px;">
                        <label style="font-weight: 600; display: block; margin-bottom: 8px; color: #1e293b;">Admin Name</label>
                        <input type="text" name="admin_name" class="form-control" style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; font-size: 14px;" required>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="font-weight: 600; display: block; margin-bottom: 8px; color: #1e293b;">Admin Email</label>
                        <input type="email" name="admin_email" class="form-control" style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; font-size: 14px;" required>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="font-weight: 600; display: block; margin-bottom: 8px; color: #1e293b;">Password</label>
                        <input type="password" name="admin_password" class="form-control" style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; font-size: 14px;" required>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="font-weight: 600; display: block; margin-bottom: 8px; color: #1e293b;">Confirm Password</label>
                        <input type="password" name="admin_confirm" class="form-control" style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; font-size: 14px;" required>
                    </div>
                    <button type="submit" class="btn btn-primary" style="background: #6366f1; border: none; padding: 12px 32px; border-radius: 8px; font-weight: 600; margin-top: 20px;">Create Admin</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>