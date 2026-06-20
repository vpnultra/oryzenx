<?php
/**
 * Payment Functions
 */

class PaymentManager {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function createPayment($user_id, $domain_id, $amount, $payment_method = 'bitcoin') {
        $user_id = (int)$user_id;
        $domain_id = (int)$domain_id;
        $amount = (float)$amount;
        $payment_method = $this->db->escape($payment_method);
        $order_id = 'ORD-' . time() . '-' . random_int(1000, 9999);

        $wallet_address = $payment_method === 'bitcoin' ? BITCOIN_WALLET : ETHEREUM_WALLET;
        $wallet_address = $this->db->escape($wallet_address);

        $qr_code = $this->generateQRCode($wallet_address, $amount);
        $qr_code = $this->db->escape($qr_code);

        $sql = "INSERT INTO payments (user_id, order_id, domain_id, amount, payment_method, wallet_address, qr_code, status, created_at)
                VALUES ($user_id, '$order_id', $domain_id, $amount, '$payment_method', '$wallet_address', '$qr_code', 'pending', NOW())";

        if ($this->db->query($sql)) {
            return [
                'success' => true,
                'payment_id' => $this->db->getLastId(),
                'order_id' => $order_id,
                'wallet' => $wallet_address,
                'qr_code' => $qr_code
            ];
        }

        return ['success' => false, 'message' => 'Payment creation failed'];
    }

    public function getPaymentById($id) {
        $id = (int)$id;
        $result = $this->db->query("SELECT * FROM payments WHERE id = $id LIMIT 1");
        return $result->fetch_assoc();
    }

    public function getUserPayments($user_id, $limit = 10, $offset = 0) {
        $user_id = (int)$user_id;
        $sql = "SELECT p.*, d.name as domain_name FROM payments p 
                LEFT JOIN domains d ON p.domain_id = d.id 
                WHERE p.user_id = $user_id 
                ORDER BY p.created_at DESC 
                LIMIT $limit OFFSET $offset";
        return $this->db->query($sql);
    }

    public function updatePaymentStatus($payment_id, $status, $admin_notes = '') {
        $payment_id = (int)$payment_id;
        $status = $this->db->escape($status);
        $admin_notes = $this->db->escape($admin_notes);

        $sql = "UPDATE payments SET status = '$status', admin_notes = '$admin_notes', updated_at = NOW(), verified_at = NOW() WHERE id = $payment_id";
        return $this->db->query($sql);
    }

    public function uploadProof($payment_id, $file_path) {
        $payment_id = (int)$payment_id;
        $file_path = $this->db->escape($file_path);

        $sql = "UPDATE payments SET proof_image = '$file_path', updated_at = NOW() WHERE id = $payment_id";
        return $this->db->query($sql);
    }

    private function generateQRCode($address, $amount) {
        $url = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($address);
        return $url;
    }

    public function getPendingPayments() {
        return $this->db->query("SELECT p.*, u.name as user_name, u.email as user_email, d.name as domain_name FROM payments p 
                                LEFT JOIN users u ON p.user_id = u.id 
                                LEFT JOIN domains d ON p.domain_id = d.id 
                                WHERE p.status = 'pending' 
                                ORDER BY p.created_at ASC");
    }

    public function getTotalRevenue() {
        $result = $this->db->query("SELECT SUM(amount) as total FROM payments WHERE status = 'approved'");
        $row = $result->fetch_assoc();
        return $row['total'] ?? 0;
    }
}
?>