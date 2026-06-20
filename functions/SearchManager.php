<?php
/**
 * Search Functions
 */

class SearchManager {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function search($query, $type = 'all', $limit = 20) {
        $query = $this->db->escape(trim($query));
        $results = [];

        if (empty($query)) {
            return $results;
        }

        // Log search keyword
        $this->logSearchKeyword($query);

        // Search Domains
        if ($type === 'all' || $type === 'domains') {
            $domain_results = $this->db->query(
                "SELECT id, name, extension, asking_price, quality_badge, image 
                FROM domains 
                WHERE (name LIKE '%$query%' OR description LIKE '%$query%') 
                AND is_available = 1 
                ORDER BY is_featured DESC, views DESC 
                LIMIT $limit"
            );

            while ($row = $domain_results->fetch_assoc()) {
                $row['type'] = 'domain';
                $results[] = $row;
            }
        }

        // Search Blog Posts
        if ($type === 'all' || $type === 'posts') {
            $post_results = $this->db->query(
                "SELECT id, title, slug, excerpt, featured_image, views 
                FROM blog_posts 
                WHERE (title LIKE '%$query%' OR content LIKE '%$query%') 
                AND status = 'published' 
                ORDER BY published_at DESC 
                LIMIT $limit"
            );

            while ($row = $post_results->fetch_assoc()) {
                $row['type'] = 'post';
                $results[] = $row;
            }
        }

        // Search Categories
        if ($type === 'all' || $type === 'categories') {
            $category_results = $this->db->query(
                "SELECT id, name, slug, posts_count 
                FROM blog_categories 
                WHERE name LIKE '%$query%' 
                LIMIT $limit"
            );

            while ($row = $category_results->fetch_assoc()) {
                $row['type'] = 'category';
                $results[] = $row;
            }
        }

        // Search Tags
        if ($type === 'all' || $type === 'tags') {
            $tag_results = $this->db->query(
                "SELECT id, name, slug, posts_count 
                FROM blog_tags 
                WHERE name LIKE '%$query%' 
                LIMIT $limit"
            );

            while ($row = $tag_results->fetch_assoc()) {
                $row['type'] = 'tag';
                $results[] = $row;
            }
        }

        return array_slice($results, 0, $limit);
    }

    private function logSearchKeyword($keyword) {
        $keyword = $this->db->escape($keyword);
        
        $existing = $this->db->query("SELECT id FROM search_keywords WHERE keyword = '$keyword'");
        
        if ($existing->num_rows > 0) {
            $this->db->query("UPDATE search_keywords SET search_count = search_count + 1, updated_at = NOW() WHERE keyword = '$keyword'");
        } else {
            $this->db->query("INSERT INTO search_keywords (keyword, search_count, created_at) VALUES ('$keyword', 1, NOW())");
        }
    }

    public function getPopularSearches($limit = 10) {
        return $this->db->query(
            "SELECT keyword, search_count FROM search_keywords 
            ORDER BY search_count DESC 
            LIMIT $limit"
        );
    }
}
?>