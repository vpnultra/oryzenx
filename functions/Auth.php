<?php
/**
 * Authentication Functions
 */

class Auth {
    private $db;
    private $user_id = null;
    private $user_data = null;

    public function __construct($db) {
        $this->db = $db;
        $this->checkSession();
    }

    public function register($name, $email, $password, $confirm_password) {
        if ($password !== $confirm_password) {
            return ['success' => false, 'message' => 'Passwords do not match'];
        }

        if (strlen($password) < 8) {
            return ['success' => false, 'message' => 'Password must be at least 8 characters'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email address'];
        }

        $email = $this->db->escape($email);
        $checkUser = $this->db->query("SELECT id FROM users WHERE email = '$email'");

        if ($checkUser->num_rows > 0) {
            return ['success' => false, 'message' => 'Email already registered'];
        }

        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $name = $this->db->escape($name);

        $sql = "INSERT INTO users (name, email, password, role, status, created_at) 
                VALUES ('$name', '$email', '$hashed_password', 'user', 'active', NOW())";

        if ($this->db->query($sql)) {
            return ['success' => true, 'message' => 'Registration successful'];
        }

        return ['success' => false, 'message' => 'Registration failed'];
    }

    public function login($email, $password) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email'];
        }

        $email = $this->db->escape($email);
        $result = $this->db->query("SELECT id, password, role, status FROM users WHERE email = '$email' LIMIT 1");

        if ($result->num_rows === 0) {
            return ['success' => false, 'message' => 'Email not found'];
        }

        $user = $result->fetch_assoc();

        if ($user['status'] !== 'active') {
            return ['success' => false, 'message' => 'Account is inactive'];
        }

        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Incorrect password'];
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];

        $this->db->query("UPDATE users SET last_login = NOW() WHERE id = {$user['id']}");

        return ['success' => true, 'message' => 'Login successful'];
    }

    public function logout() {
        session_destroy();
        return ['success' => true, 'message' => 'Logged out successfully'];
    }

    public function checkSession() {
        if (isset($_SESSION['user_id'])) {
            $user_id = (int)$_SESSION['user_id'];
            $result = $this->db->query("SELECT id, name, email, role, status FROM users WHERE id = $user_id LIMIT 1");
            
            if ($result->num_rows > 0) {
                $this->user_id = $user_id;
                $this->user_data = $result->fetch_assoc();
            } else {
                session_destroy();
            }
        }
    }

    public function isLoggedIn() {
        return $this->user_id !== null;
    }

    public function isAdmin() {
        return $this->user_data && $this->user_data['role'] === 'admin';
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function getUser() {
        return $this->user_data;
    }

    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: /login.php');
            exit;
        }
    }

    public function requireAdmin() {
        if (!$this->isAdmin()) {
            http_response_code(403);
            exit('Access Denied');
        }
    }

    public function forgotPassword($email) {
        $email = $this->db->escape($email);
        $result = $this->db->query("SELECT id FROM users WHERE email = '$email' LIMIT 1");

        if ($result->num_rows === 0) {
            return ['success' => false, 'message' => 'Email not found'];
        }

        $reset_token = bin2hex(random_bytes(32));
        $user = $result->fetch_assoc();
        $hash = password_hash($reset_token, PASSWORD_BCRYPT);

        $sql = "UPDATE users SET password = '$hash' WHERE id = {$user['id']}";
        $this->db->query($sql);

        return ['success' => true, 'message' => 'Reset link sent to email', 'token' => $reset_token];
    }
}
?>