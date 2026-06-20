<?php
/**
 * Blog Post Detail Page
 */

session_start();
require_once '../includes/init.php';

$blogMgr = new BlogManager($db);

$slug = $_GET['slug'] ?? '';
if (empty($slug)) {
    header('Location: blog.php');
    exit;
}

$post = $blogMgr->getPostBySlug($slug);
if (!$post) {
    header('HTTP/1.0 404 Not Found');
    exit('Post not found');
}

// Increment views
$blogMgr->incrementViews($post['id']);

// Get comments
$comments = $blogMgr->getComments($post['id'], 10);

$comment_error = '';
$comment_success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_comment'])) {
    $content = $_POST['content'] ?? '';
    
    if (empty($content)) {
        $comment_error = 'Comment cannot be empty';
    } else {
        $comment_data = [
            'content' => $content,
            'user_id' => $auth->isLoggedIn() ? $auth->getUserId() : null,
            'author_name' => $_POST['author_name'] ?? '',
            'author_email' => $_POST['author_email'] ?? ''
        ];
        
        if ($blogMgr->addComment($post['id'], $comment_data)) {
            $comment_success = 'Comment submitted! Awaiting approval.';
        } else {
            $comment_error = 'Failed to submit comment';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?> - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($post['meta_description'] ?? $post['excerpt']); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main class="container py-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <!-- Post Header -->
                <article class="mb-5">
                    <?php if($post['featured_image']): ?>
                        <img src="../uploads/{{$post['featured_image']}}" class="img-fluid rounded-4 mb-4" alt="{{$post['title']}}" style="max-height: 400px; object-fit: cover; width: 100%;">
                    <?php endif; ?>

                    <h1 class="fw-bold mb-3">{{$post['title']}}</h1>

                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom flex-wrap gap-3">
                        <div>
                            <small class="text-muted">
                                <i class="fas fa-user"></i> {{$post['author_name']}}
                            </small>
                        </div>
                        <div>
                            <small class="text-muted">
                                <i class="fas fa-calendar"></i> {{date('F d, Y', strtotime($post['published_at']))}}
                            </small>
                        </div>
                        <div>
                            <small class="text-muted">
                                <i class="fas fa-eye"></i> {{$post['views']}} views
                            </small>
                        </div>
                        <?php if($post['category_name']): ?>
                            <div>
                                <span class="badge bg-primary">{{$post['category_name']}}</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="post-content mb-5" style="line-height: 1.8; font-size: 16px;">
                        {{$post['content']}}
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-4 border-top">
                        <div>
                            <button class="btn btn-outline-primary btn-sm rounded-3" onclick="likePost()">
                                <i class="fas fa-heart"></i> {{$post['likes']}}
                            </button>
                        </div>
                        <div>
                            <a href="blog.php" class="btn btn-outline-secondary btn-sm rounded-3">
                                <i class="fas fa-arrow-left"></i> Back to Blog
                            </a>
                        </div>
                    </div>
                </article>

                <!-- Comments Section -->
                <section class="mt-5 pt-5 border-top">
                    <h3 class="fw-bold mb-4"><i class="fas fa-comments"></i> Comments ({{count($comments)}})</h3>

                    <?php if($comment_error): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{$comment_error}}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if($comment_success): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            {{$comment_success}}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Comment Form -->
                    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                        <h5 class="fw-bold mb-3">Leave a Comment</h5>
                        <form method="POST">
                            <input type="hidden" name="submit_comment" value="1">
                            
                            <?php if(!$auth->isLoggedIn()): ?>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Name</label>
                                        <input type="text" name="author_name" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="author_email" class="form-control" required>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="mb-3">
                                <label class="form-label">Comment</label>
                                <textarea name="content" class="form-control" rows="4" placeholder="Share your thoughts..." required></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary rounded-3">
                                <i class="fas fa-paper-plane"></i> Post Comment
                            </button>
                        </form>
                    </div>

                    <!-- Display Comments -->
                    <div class="comments-list">
                        <?php while($comment = $comments->fetch_assoc()): ?>
                            <div class="card border-0 bg-light rounded-3 p-4 mb-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="fw-bold mb-0">{{$comment['user_name'] ?? $comment['author_name']}}</h6>
                                        <small class="text-muted">{{timeAgo($comment['created_at'])}}</small>
                                    </div>
                                </div>
                                <p class="mb-0">{{$comment['content']}}</p>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function likePost() {
            alert('Thanks for the like!');
        }
    </script>
</body>
</html>