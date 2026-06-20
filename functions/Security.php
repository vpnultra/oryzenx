<?php
/**
 * Security Functions
 */

class Security {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Generate CSRF Token
     */
    public function generateCSRFToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verify CSRF Token
     */
    public function verifyCSRFToken($token) {
        if (empty($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Sanitize Input
     */
    public function sanitize($input) {
        if (is_array($input)) {
            return array_map([$this, 'sanitize'], $input);
        }
        return htmlspecialchars(strip_tags($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validate Email
     */
    public function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Validate URL
     */
    public function validateURL($url) {
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    /**
     * Rate Limiting
     */
    public function checkRateLimit($identifier, $max_attempts = 10, $window = 3600) {
        $cache_key = 'rate_limit_' . md5($identifier);
        $attempts = apcu_fetch($cache_key);

        if ($attempts === false) {
            apcu_store($cache_key, 1, $window);
            return true;
        }

        if ($attempts >= $max_attempts) {
            return false;
        }

        apcu_inc($cache_key);
        return true;
    }

    /**
     * Log Activity
     */
    public function logActivity($user_id, $action, $action_type = 'general', $description = '') {
        $user_id = $user_id ? (int)$user_id : 'NULL';
        $action = $this->db->escape($action);
        $action_type = $this->db->escape($action_type);
        $description = $this->db->escape($description);
        $ip_address = $this->db->escape($_SERVER['REMOTE_ADDR']);
        $user_agent = $this->db->escape($_SERVER['HTTP_USER_AGENT'] ?? '');

        $sql = "INSERT INTO activity_logs (user_id, action, action_type, description, ip_address, user_agent, created_at)
                VALUES ($user_id, '$action', '$action_type', '$description', '$ip_address', '$user_agent', NOW())";

        return $this->db->query($sql);
    }

    /**
     * Validate Password Strength
     */
    public function validatePassword($password) {
        $errors = [];

        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters';
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter';
        }

        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter';
        }

        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number';
        }

        if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
            $errors[] = 'Password must contain at least one special character';
        }

        return $errors;
    }

    /**
     * XSS Protection
     */
    public function escapeHTML($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Generate Secure Random String
     */
    public function generateToken($length = 32) {
        return bin2hex(random_bytes($length / 2));
    }
}
?>