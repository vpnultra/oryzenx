<?php
/**
 * Domain Functions
 */

class DomainManager {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllDomains($limit = 15, $offset = 0, $filters = []) {
        $where = "WHERE is_available = 1 AND sale_status = 'available'";

        if (!empty($filters['search'])) {
            $search = $this->db->escape($filters['search']);
            $where .= " AND (name LIKE '%$search%' OR description LIKE '%$search%')";
        }

        if (!empty($filters['category'])) {
            $category = $this->db->escape($filters['category']);
            $where .= " AND category = '$category'";
        }

        if (!empty($filters['price_min'])) {
            $price_min = (float)$filters['price_min'];
            $where .= " AND asking_price >= $price_min";
        }

        if (!empty($filters['price_max'])) {
            $price_max = (float)$filters['price_max'];
            $where .= " AND asking_price <= $price_max";
        }

        if (!empty($filters['featured_only'])) {
            $where .= " AND is_featured = 1";
        }

        $sql = "SELECT * FROM domains $where ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
        return $this->db->query($sql);
    }

    public function getDomainById($id) {
        $id = (int)$id;
        $result = $this->db->query("SELECT * FROM domains WHERE id = $id LIMIT 1");
        return $result->fetch_assoc();
    }

    public function getDomainByName($name) {
        $name = $this->db->escape($name);
        $result = $this->db->query("SELECT * FROM domains WHERE name = '$name' LIMIT 1");
        return $result->fetch_assoc();
    }

    public function addDomain($data) {
        $name = $this->db->escape($data['name']);
        $extension = $this->db->escape($data['extension']);
        $description = $this->db->escape($data['description'] ?? '');
        $asking_price = (float)($data['asking_price'] ?? 0);
        $min_price = (float)($data['min_price'] ?? 0);
        $category = $this->db->escape($data['category'] ?? '');

        $sql = "INSERT INTO domains (name, extension, description, asking_price, min_price, category, is_available, created_at)
                VALUES ('$name', '$extension', '$description', $asking_price, $min_price, '$category', 1, NOW())";

        return $this->db->query($sql);
    }

    public function updateDomain($id, $data) {
        $id = (int)$id;
        $updates = [];

        if (isset($data['name'])) {
            $name = $this->db->escape($data['name']);
            $updates[] = "name = '$name'";
        }

        if (isset($data['description'])) {
            $description = $this->db->escape($data['description']);
            $updates[] = "description = '$description'";
        }

        if (isset($data['asking_price'])) {
            $price = (float)$data['asking_price'];
            $updates[] = "asking_price = $price";
        }

        if (isset($data['rating'])) {
            $rating = (float)$data['rating'];
            $updates[] = "rating = $rating";
        }

        if (isset($data['quality_badge'])) {
            $badge = $this->db->escape($data['quality_badge']);
            $updates[] = "quality_badge = '$badge'";
        }

        if (isset($data['is_featured'])) {
            $featured = (int)$data['is_featured'];
            $updates[] = "is_featured = $featured";
        }

        if (isset($data['sale_status'])) {
            $status = $this->db->escape($data['sale_status']);
            $updates[] = "sale_status = '$status'";
        }

        if (empty($updates)) {
            return true;
        }

        $sql = "UPDATE domains SET " . implode(', ', $updates) . ", updated_at = NOW() WHERE id = $id";
        return $this->db->query($sql);
    }

    public function deleteDomain($id) {
        $id = (int)$id;
        return $this->db->query("DELETE FROM domains WHERE id = $id");
    }

    public function getFeaturedDomains($limit = 6) {
        $sql = "SELECT * FROM domains WHERE is_featured = 1 AND is_available = 1 ORDER BY featured_until DESC LIMIT $limit";
        return $this->db->query($sql);
    }

    public function incrementViews($domain_id) {
        $domain_id = (int)$domain_id;
        return $this->db->query("UPDATE domains SET views = views + 1 WHERE id = $domain_id");
    }

    public function getTotalDomains() {
        $result = $this->db->query("SELECT COUNT(*) as total FROM domains");
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function getCategories() {
        return $this->db->query("SELECT DISTINCT category FROM domains WHERE category IS NOT NULL ORDER BY category");
    }
}
?>