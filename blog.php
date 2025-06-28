<?php
define('INCLUDED', true);
session_start(); // Start the session

// Check if the session 'password' is set
if (!isset($_SESSION['password'])) {
    header("location: login.php?message=login first"); // Redirect to the login page if the session is not set
    exit;
}

// Validate blog ID
if (!isset($_GET['blogid']) || !is_numeric($_GET['blogid'])) {
    header("location: home.php?error=invalid_blog");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<?php
include 'header.php'
?>
<style>
  .img_format {
    display: block;
    margin: 0 auto;
    width: 100%;
    max-width: 100%;
    height: auto;
    border-radius: 10px;
    margin-top: 10px;
  }
  .like-btn, .dislike-btn {
    cursor: pointer;
    transition: transform 0.2s;
  }
  .like-btn:hover, .dislike-btn:hover {
    transform: scale(1.1);
  }
  .liked, .disliked {
    color: #007bff;
  }
  .blog-meta {
    color: #666;
    font-size: 0.9em;
    margin-bottom: 15px;
  }
  .blog-content {
    line-height: 1.6;
    margin: 20px 0;
  }
  .error-message {
    color: #dc3545;
    padding: 15px;
    margin: 10px 0;
    border-radius: 5px;
    background-color: #f8d7da;
  }
  .comments-section {
    margin-top: 20px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
  }
  .comments-section h4 {
    font-size: 16px;
    margin-bottom: 12px;
  }
  .comment-form {
    margin-bottom: 15px;
  }
  .comment-form textarea {
    width: 100%;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 8px;
    font-size: 11px;
  }
  .comment-item {
    background: white;
    margin-bottom: 10px;
  }
  .comment-content {
    padding: 10px !important;
  }
  .comment-content p {
    font-size: 11px;
    margin: 5px 0 !important;
    line-height: 1.4;
  }
  .comment-content strong {
    font-size: 11px;
  }
  .comment-content small {
    font-size: 10px;
    color: #666;
  }
  .reply-form {
    margin-top: 5px !important;
  }
  .reply-form textarea {
    width: 100%;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 6px;
    font-size: 11px;
  }
  .replies {
    margin-left: 20px;
    margin-top: 5px !important;
  }
  .reply-item {
    background: #f8f9fa;
    padding: 8px;
    margin-top: 5px;
    border-radius: 4px;
  }
  .reply-content {
    padding: 8px !important;
  }
  .reply-content p {
    font-size: 11px;
    margin: 3px 0 !important;
    line-height: 1.4;
  }
  .reply-content strong {
    font-size: 11px;
  }
  .reply-content small {
    font-size: 10px;
    color: #666;
  }
  .btn-sm {
    padding: 0.2rem 0.5rem;
    font-size: 11px;
  }
  .mt-2 {
    margin-top: 0.4rem !important;
  }
  .mb-2 {
    margin-bottom: 0.4rem !important;
  }
  .mb-3 {
    margin-bottom: 0.6rem !important;
  }
  .mb-4 {
    margin-bottom: 0.8rem !important;
  }
</style>

<body>
  <div class="site-content">
    <!-- Preloader start -->

    <!-- Preloader end -->
    <!-- Header start -->
    <?php
    include 'top_nav.php';
    include("db_conn.php");

    ?>
    <!-- Header end -->
    <!-- Homescreen content start -->
    <section id="homescreen">
      <div class="home-offer-sec mt-8">
        <div class="container">
          <div class="review-third-sec mt-24">
            <div class="review-third-sec-content">
              <?php
              $blogid = intval($_GET['blogid']); // Sanitize input
              $sql = "SELECT * FROM tbl_blog WHERE id = ?";
              $stmt = mysqli_prepare($conn, $sql);
              mysqli_stmt_bind_param($stmt, "i", $blogid);
              mysqli_stmt_execute($stmt);
              $result = mysqli_stmt_get_result($stmt);

              if ($result && mysqli_num_rows($result) == 1) {
                  $row = mysqli_fetch_assoc($result);
              ?>
                <div class="review-third-sec-wrap">
                  <div class="review-bottom-second">
                    <div class="review-content">
                      <div class="blog-meta">
                        <i class="fas fa-user"></i> <?php echo htmlspecialchars($row['writer'] ?? 'Anonymous'); ?> | 
                        <i class="far fa-calendar"></i> <?php echo date('F j, Y', strtotime($row['date'])); ?>
                      </div>
                    </div>
                  </div>
                </div>

                <?php if (!empty($row['image_url'])) : ?>
                  <img class="img_format" src="<?php echo htmlspecialchars($row['image_url']); ?>" 
                       alt="<?php echo htmlspecialchars($row['title']); ?>"
                       onerror="this.src='assets/images/gallery/default-blog.jpg'" />
                <?php else: ?>
                  <img class="img_format" src="assets/images/gallery/default-blog.jpg" 
                       alt="Default Blog Image" />
                <?php endif; ?>

                <div class="review-para mt-12">
                  <h4 style="color: blue">
                    <?php echo htmlspecialchars($row['title'] ?? 'Untitled'); ?>
                  </h4>
                  <div class="blog-content">
                    <?php 
                    if (isset($row['content']) && !empty($row['content'])) {
                        // Convert line breaks to <br> tags and escape HTML
                        echo nl2br(htmlspecialchars($row['content']));
                    } else {
                        echo '<div class="error-message">No content available.</div>';
                    }
                    ?>
                  </div>
                </div>

                <div class="review-helpful-sec-full mt-8">
                  <div>
                    <p class="helpful-txt">Was this article helpful?</p>
                  </div>
                  <div class="d-flex align-items-center">
                    <div class="like-btn me-4" data-blog-id="<?php echo $blogid; ?>" onclick="handleLike(this, true)">
                      <i class="far fa-thumbs-up"></i>
                      <span class="like-count">0</span>
                    </div>
                    <div class="dislike-btn" data-blog-id="<?php echo $blogid; ?>" onclick="handleLike(this, false)">
                      <i class="far fa-thumbs-down"></i>
                      <span class="dislike-count">0</span>
                    </div>
                  </div>
                </div>

                <!-- Comments Section -->
                <div class="comments-section mt-5">
                  <h4>Comments</h4>
                  <div class="comment-form mb-4">
                    <textarea class="form-control" id="commentText" rows="3" placeholder="Write your comment..."></textarea>
                    <button class="btn btn-primary mt-2" onclick="submitComment(<?php echo $blogid; ?>, null)">Post Comment</button>
                  </div>
                  
                  <div id="comments-container">
                    <?php
                    // Fetch comments for this blog
                    $comments_sql = "SELECT c.* 
                                   FROM tbl_blog_comments c 
                                   WHERE c.blog_id = ? AND c.parent_id IS NULL 
                                   ORDER BY c.created_at DESC";
                    $comments_stmt = mysqli_prepare($conn, $comments_sql);
                    mysqli_stmt_bind_param($comments_stmt, "i", $blogid);
                    mysqli_stmt_execute($comments_stmt);
                    $comments_result = mysqli_stmt_get_result($comments_stmt);

                    while ($comment = mysqli_fetch_assoc($comments_result)) {
                        ?>
                        <div class="comment-item mb-3" id="comment-<?php echo $comment['id']; ?>">
                            <div class="comment-content p-3 border rounded">
                                <div class="d-flex justify-content-between">
                                    <strong>Anonymous User</strong>
                                    <small><?php echo date('M j, Y g:i A', strtotime($comment['created_at'])); ?></small>
                                </div>
                                <p class="mt-2 mb-2"><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></p>
                                <button class="btn btn-sm btn-light" onclick="showReplyForm(<?php echo $comment['id']; ?>)">Reply</button>
                                
                                <div id="reply-form-<?php echo $comment['id']; ?>" class="reply-form mt-2" style="display: none;">
                                    <textarea class="form-control" rows="2" placeholder="Write your reply..."></textarea>
                                    <button class="btn btn-sm btn-primary mt-2" onclick="submitComment(<?php echo $blogid; ?>, <?php echo $comment['id']; ?>)">Post Reply</button>
                                </div>

                                <div class="replies ms-4 mt-3">
                                    <?php
                                    // Fetch replies for this comment
                                    $replies_sql = "SELECT * FROM tbl_blog_comments WHERE parent_id = ? ORDER BY created_at ASC";
                                    $replies_stmt = mysqli_prepare($conn, $replies_sql);
                                    mysqli_stmt_bind_param($replies_stmt, "i", $comment['id']);
                                    mysqli_stmt_execute($replies_stmt);
                                    $replies_result = mysqli_stmt_get_result($replies_stmt);

                                    while ($reply = mysqli_fetch_assoc($replies_result)) {
                                        ?>
                                        <div class="reply-item mb-2">
                                            <div class="reply-content p-2 border rounded">
                                                <div class="d-flex justify-content-between">
                                                    <strong>Admin</strong>
                                                    <small><?php echo date('M j, Y g:i A', strtotime($reply['created_at'])); ?></small>
                                                </div>
                                                <p class="mt-1 mb-1"><?php echo nl2br(htmlspecialchars($reply['comment'])); ?></p>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    mysqli_stmt_close($replies_stmt);
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    mysqli_stmt_close($comments_stmt);
                    ?>
                  </div>
                </div>
                <!-- End Comments Section -->

              <?php 
              } else {
                  echo '<div class="error-message">Blog post not found.</div>';
              }
              mysqli_stmt_close($stmt);
              ?>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- Homescreen content end -->
    <!-- Tabbar start -->
    <?php include 'partial-front/bottom_nav.php'; ?>
    <!-- Tabbar end -->
    <!--SideBar setting menu start-->
    <?php
    include 'option.php'
    ?>
    <div class="dark-overlay"></div>
    <!--SideBar setting menu end-->
    <!-- pwa install app popup Start -->
    <!-- <div class="offcanvas offcanvas-bottom addtohome-popup theme-offcanvas" tabindex="-1" id="offcanvas" aria-modal="true" role="dialog">
			<button type="button" class="btn-close text-reset popup-close-home" data-bs-dismiss="offcanvas" aria-label="Close"></button>
			<div class="offcanvas-body small">
				<img src="assets/images/logo/logo.png" alt="logo" class="logo-popup">
				<p class="title font-w600">Guruji</p>
				<p class="install-txt">Install Guruji - Online Learning & Educational Courses PWA to your home screen for easy access, just like any other app</p>
				<a href="javascript:void(0)" class="theme-btn install-app btn-inline addhome-btn" id="installApp">Add to Home Screen</a>
			</div>
		</div> -->
    <!-- pwa install app popup End -->
  </div>
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/slick.min.js"></script>
  <script src="assets/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/modal.js"></script>
  <script src="assets/js/custom.js"></script>
  <script>
  // Load initial like counts and comments
  $(document).ready(function() {
    const blogId = <?php echo $blogid; ?>;
    updateLikeCounts(blogId);
    loadComments(blogId);
  });

  function handleLike(element, isLike) {
    const blogId = element.dataset.blogId;
    
    $.ajax({
      url: 'api/handle_like.php',
      method: 'POST',
      data: {
        blog_id: blogId,
        is_like: isLike
      },
      success: function(response) {
        if (response.success) {
          updateLikeCounts(blogId);
          // Update button styles
          if (isLike) {
            $('.like-btn').addClass('liked');
            $('.dislike-btn').removeClass('disliked');
          } else {
            $('.like-btn').removeClass('liked');
            $('.dislike-btn').addClass('disliked');
          }
        } else if (response.error) {
          alert(response.error);
        }
      },
      error: function() {
        alert('Error processing your request. Please try again.');
      }
    });
  }

  function updateLikeCounts(blogId) {
    $.ajax({
      url: 'api/handle_like.php',
      method: 'GET',
      data: { blog_id: blogId },
      success: function(response) {
        if (response.success) {
          $('.like-count').text(response.likes || 0);
          $('.dislike-count').text(response.dislikes || 0);
          
          // Update button styles based on user's previous interaction
          if (response.user_status === 'liked') {
            $('.like-btn').addClass('liked');
            $('.dislike-btn').removeClass('disliked');
          } else if (response.user_status === 'disliked') {
            $('.like-btn').removeClass('liked');
            $('.dislike-btn').addClass('disliked');
          } else {
            $('.like-btn').removeClass('liked');
            $('.dislike-btn').removeClass('disliked');
          }
        }
      }
    });
  }

  function showReplyForm(commentId) {
    $(`#reply-form-${commentId}`).toggle();
  }

  function submitComment(blogId, parentId) {
    const textareaSelector = parentId ? `#reply-form-${parentId} textarea` : '#commentText';
    const comment = $(textareaSelector).val();
    
    if (!comment.trim()) {
      alert('Please write a comment first');
      return;
    }

    $.ajax({
      url: 'api/handle_comment.php',
      method: 'POST',
      data: {
        blog_id: blogId,
        parent_id: parentId,
        comment: comment
      },
      success: function(response) {
        if (response.success) {
          // Clear the textarea
          $(textareaSelector).val('');
          // Hide reply form if it's a reply
          if (parentId) {
            $(`#reply-form-${parentId}`).hide();
          }
          // Reload comments
          loadComments(blogId);
        } else if (response.error) {
          alert(response.error);
        }
      },
      error: function() {
        alert('Error submitting your comment. Please try again.');
      }
    });
  }

  function loadComments(blogId) {
    $.ajax({
      url: 'api/handle_comment.php',
      method: 'GET',
      data: { blog_id: blogId },
      success: function(response) {
        if (response.success) {
          const commentsContainer = $('#comments-container');
          commentsContainer.empty();
          
          response.comments.forEach(function(comment) {
            if (!comment.parent_id) { // Only process parent comments first
              const commentHtml = createCommentHtml(comment);
              commentsContainer.append(commentHtml);
              
              // Add replies
              const replies = response.comments.filter(c => c.parent_id === comment.id);
              if (replies.length > 0) {
                const repliesContainer = $(`<div class="replies ms-4 mt-3"></div>`);
                replies.forEach(reply => {
                  repliesContainer.append(createReplyHtml(reply));
                });
                $(`#comment-${comment.id}`).append(repliesContainer);
              }
            }
          });
        }
      }
    });
  }

  function createCommentHtml(comment) {
    return `
      <div class="comment-item mb-3" id="comment-${comment.id}">
        <div class="comment-content p-3 border rounded">
          <div class="d-flex justify-content-between">
            <strong>${escapeHtml(comment.username)}</strong>
            <small>${formatDate(comment.created_at)}</small>
          </div>
          <p class="mt-2 mb-2">${escapeHtml(comment.comment)}</p>
          <button class="btn btn-sm btn-light" onclick="showReplyForm(${comment.id})">Reply</button>
          
          <div id="reply-form-${comment.id}" class="reply-form mt-2" style="display: none;">
            <textarea class="form-control" rows="2" placeholder="Write your reply..."></textarea>
            <button class="btn btn-sm btn-primary mt-2" onclick="submitComment(${<?php echo $blogid; ?>}, ${comment.id})">Post Reply</button>
          </div>
        </div>
      </div>
    `;
  }

  function createReplyHtml(reply) {
    return `
      <div class="reply-item mb-2">
        <div class="reply-content p-2 border rounded">
          <div class="d-flex justify-content-between">
            <strong>${escapeHtml(reply.username)}</strong>
            <small>${formatDate(reply.created_at)}</small>
          </div>
          <p class="mt-1 mb-1">${escapeHtml(reply.comment)}</p>
        </div>
      </div>
    `;
  }

  function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString('en-US', {
      month: 'short',
      day: 'numeric',
      year: 'numeric',
      hour: 'numeric',
      minute: 'numeric',
      hour12: true
    });
  }
  </script>
</body>

</html>