<?php
/**
 * Offer Functions
 */

class OfferManager {
    private $db;
    private $notifications;

    public function __construct($db, $notifications) {
        $this->db = $db;
        $this->notifications = $notifications;
    }

    public function createOffer($domain_id, $user_id, $offer_price, $email, $message = '') {
        if ($offer_price < MIN_OFFER_PRICE) {
            return ['success' => false, 'message' => 'Minimum offer price is $' . MIN_OFFER_PRICE];
        }

        $domain_id = (int)$domain_id;
        $user_id = (int)$user_id;
        $offer_price = (float)$offer_price;
        $email = $this->db->escape($email);
        $message = $this->db->escape($message);
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $ip_address = $this->db->escape($ip_address);

        $sql = "INSERT INTO domain_offers (domain_id, user_id, offer_price, email, message, ip_address, status, created_at)
                VALUES ($domain_id, $user_id, $offer_price, '$email', '$message', '$ip_address', 'pending', NOW())";

        if ($this->db->query($sql)) {
            $offer_id = $this->db->getLastId();

            // Get domain owner
            $domain = $this->db->query("SELECT owner_id FROM domains WHERE id = $domain_id")->fetch_assoc();
            if ($domain && $domain['owner_id']) {
                // Notify domain owner
                $this->notifications->sendNotification(
                    $domain['owner_id'],
                    'offer',
                    'New Offer Received',
                    'You received a new offer for your domain',
                    $user_id,
                    $domain_id,
                    'domain'
                );
            }

            return ['success' => true, 'message' => 'Offer submitted successfully', 'offer_id' => $offer_id];
        }

        return ['success' => false, 'message' => 'Failed to submit offer'];
    }

    public function getOfferById($id) {
        $id = (int)$id;
        $result = $this->db->query("SELECT * FROM domain_offers WHERE id = $id LIMIT 1");
        return $result->fetch_assoc();
    }

    public function getDomainOffers($domain_id, $limit = 10, $offset = 0) {
        $domain_id = (int)$domain_id;
        $sql = "SELECT o.*, u.name, u.email as user_email FROM domain_offers o 
                LEFT JOIN users u ON o.user_id = u.id 
                WHERE o.domain_id = $domain_id 
                ORDER BY o.created_at DESC 
                LIMIT $limit OFFSET $offset";
        return $this->db->query($sql);
    }

    public function getUserOffers($user_id, $limit = 10, $offset = 0) {
        $user_id = (int)$user_id;
        $sql = "SELECT o.*, d.name as domain_name FROM domain_offers o 
                LEFT JOIN domains d ON o.domain_id = d.id 
                WHERE o.user_id = $user_id 
                ORDER BY o.created_at DESC 
                LIMIT $limit OFFSET $offset";
        return $this->db->query($sql);
    }

    public function updateOfferStatus($offer_id, $status) {
        $offer_id = (int)$offer_id;
        $status = $this->db->escape($status);

        $sql = "UPDATE domain_offers SET status = '$status', updated_at = NOW() WHERE id = $offer_id";
        return $this->db->query($sql);
    }

    public function deleteOffer($offer_id) {
        $offer_id = (int)$offer_id;
        return $this->db->query("DELETE FROM domain_offers WHERE id = $offer_id");
    }

    public function getOfferStats() {
        $pending = $this->db->query("SELECT COUNT(*) as count FROM domain_offers WHERE status = 'pending'")->fetch_assoc()['count'];
        $accepted = $this->db->query("SELECT COUNT(*) as count FROM domain_offers WHERE status = 'accepted'")->fetch_assoc()['count'];
        $rejected = $this->db->query("SELECT COUNT(*) as count FROM domain_offers WHERE status = 'rejected'")->fetch_assoc()['count'];
        $total_value = $this->db->query("SELECT SUM(offer_price) as total FROM domain_offers WHERE status = 'accepted'")->fetch_assoc()['total'];

        return [
            'pending' => $pending,
            'accepted' => $accepted,
            'rejected' => $rejected,
            'total_value' => $total_value ?? 0
        ];
    }
}
?>