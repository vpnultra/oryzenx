<?php
/**
 * Blog Functions
 */

class BlogManager {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllPosts($limit = 15, $offset = 0, $status = 'published', $language = 'en') {
        $status = $this->db->escape($status);
        $language = $this->db->escape($language);
        
        $sql = "SELECT p.*, u.name as author_name, c.name as category_name 
                FROM blog_posts p 
                LEFT JOIN users u ON p.author_id = u.id 
                LEFT JOIN blog_categories c ON p.category_id = c.id 
                WHERE p.status = '$status' AND p.language = '$language' 
                ORDER BY p.published_at DESC 
                LIMIT $limit OFFSET $offset";
        
        return $this->db->query($sql);
    }

    public function getPostBySlug($slug) {
        $slug = $this->db->escape($slug);
        $result = $this->db->query("SELECT p.*, u.name as author_name, c.name as category_name 
                                    FROM blog_posts p 
                                    LEFT JOIN users u ON p.author_id = u.id 
                                    LEFT JOIN blog_categories c ON p.category_id = c.id 
                                    WHERE p.slug = '$slug' AND p.status = 'published' LIMIT 1");
        return $result->fetch_assoc();
    }

    public function getPostById($id) {
        $id = (int)$id;
        $result = $this->db->query("SELECT p.*, u.name as author_name, c.name as category_name 
                                    FROM blog_posts p 
                                    LEFT JOIN users u ON p.author_id = u.id 
                                    LEFT JOIN blog_categories c ON p.category_id = c.id 
                                    WHERE p.id = $id LIMIT 1");
        return $result->fetch_assoc();
    }

    public function createPost($data) {
        $title = $this->db->escape($data['title']);
        $slug = $this->generateSlug($data['title']);
        $content = $this->db->escape($data['content']);
        $excerpt = $this->db->escape($data['excerpt'] ?? '');
        $author_id = (int)$data['author_id'];
        $category_id = isset($data['category_id']) ? (int)$data['category_id'] : 'NULL';
        $status = $this->db->escape($data['status'] ?? 'draft');
        $language = $this->db->escape($data['language'] ?? 'en');

        $sql = "INSERT INTO blog_posts (title, slug, content, excerpt, author_id, category_id, status, language, created_at)
                VALUES ('$title', '$slug', '$content', '$excerpt', $author_id, $category_id, '$status', '$language', NOW())";

        if ($this->db->query($sql)) {
            return $this->db->getLastId();
        }
        return false;
    }

    public function updatePost($id, $data) {
        $id = (int)$id;
        $updates = [];

        if (isset($data['title'])) {
            $title = $this->db->escape($data['title']);
            $updates[] = "title = '$title'";
        }

        if (isset($data['content'])) {
            $content = $this->db->escape($data['content']);
            $updates[] = "content = '$content'";
        }

        if (isset($data['status'])) {
            $status = $this->db->escape($data['status']);
            $updates[] = "status = '$status'";
        }

        if (isset($data['category_id'])) {
            $category_id = (int)$data['category_id'];
            $updates[] = "category_id = $category_id";
        }

        if (empty($updates)) {
            return true;
        }

        $sql = "UPDATE blog_posts SET " . implode(', ', $updates) . ", updated_at = NOW() WHERE id = $id";
        return $this->db->query($sql);
    }

    public function deletePost($id) {
        $id = (int)$id;
        return $this->db->query("DELETE FROM blog_posts WHERE id = $id");
    }

    private function generateSlug($title) {
        $slug = strtolower($title);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');
        return $slug;
    }

    public function getCategories() {
        return $this->db->query("SELECT * FROM blog_categories ORDER BY order ASC");
    }

    public function getTags() {
        return $this->db->query("SELECT * FROM blog_tags ORDER BY name ASC");
    }

    public function incrementViews($post_id) {
        $post_id = (int)$post_id;
        return $this->db->query("UPDATE blog_posts SET views = views + 1 WHERE id = $post_id");
    }

    public function addLike($post_id, $user_id = null) {
        $post_id = (int)$post_id;
        $user_id = $user_id ? (int)$user_id : 'NULL';
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $ip_address = $this->db->escape($ip_address);

        $sql = "INSERT INTO blog_reactions (post_id, user_id, reaction_type, ip_address, created_at)
                VALUES ($post_id, $user_id, 'like', '$ip_address', NOW())
                ON DUPLICATE KEY UPDATE created_at = NOW()";

        $this->db->query($sql);
        return $this->db->query("UPDATE blog_posts SET likes = likes + 1 WHERE id = $post_id");
    }

    public function getComments($post_id, $limit = 10, $offset = 0) {
        $post_id = (int)$post_id;
        $sql = "SELECT c.*, u.name as user_name FROM blog_comments c 
                LEFT JOIN users u ON c.user_id = u.id 
                WHERE c.post_id = $post_id AND c.status = 'approved' 
                ORDER BY c.created_at DESC 
                LIMIT $limit OFFSET $offset";
        return $this->db->query($sql);
    }

    public function addComment($post_id, $data) {
        $post_id = (int)$post_id;
        $content = $this->db->escape($data['content']);
        $user_id = isset($data['user_id']) ? (int)$data['user_id'] : 'NULL';
        $author_name = $this->db->escape($data['author_name'] ?? '');
        $author_email = $this->db->escape($data['author_email'] ?? '');

        $sql = "INSERT INTO blog_comments (post_id, user_id, author_name, author_email, content, status, created_at)
                VALUES ($post_id, $user_id, '$author_name', '$author_email', '$content', 'pending', NOW())";

        return $this->db->query($sql);
    }
}
?>