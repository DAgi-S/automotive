<?php
require_once 'includes/header.php';
require_once '../includes/config.php';
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Blogs Management</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBlogModal">
            <i class="fas fa-plus"></i> Add New Blog
        </button>
    </div>

    <!-- Display Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php 
            echo $_SESSION['success'];
            unset($_SESSION['success']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php 
            echo $_SESSION['error'];
            unset($_SESSION['error']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Blogs Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Blogs List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="blogsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Writer</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Comments</th>
                            <th>Likes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT b.*, 
                                (SELECT COUNT(*) FROM tbl_blog_comments WHERE blog_id = b.id) as comment_count,
                                (SELECT COUNT(*) FROM tbl_blog_likes WHERE blog_id = b.id) as like_count
                                FROM tbl_blog b 
                                ORDER BY b.date DESC";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute();
                        $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        if (!empty($blogs)) {
                            foreach ($blogs as $row) {
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                                    <td><?php echo htmlspecialchars($row['writer']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($row['date'])); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $row['status'] == 'featured' ? 'success' : ($row['status'] == 'published' ? 'warning' : 'secondary'); ?>">
                                            <?php echo ucfirst($row['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-info btn-sm view-comments" data-id="<?php echo $row['id']; ?>" data-bs-toggle="modal" data-bs-target="#viewCommentsModal">
                                            <?php echo $row['comment_count']; ?> Comments
                                        </button>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">
                                            <?php echo $row['like_count'] ?? 0; ?> Likes
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info edit-blog" 
                                                data-id="<?php echo $row['id']; ?>"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editBlogModal">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning view-comments" 
                                                data-id="<?php echo $row['id']; ?>"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#viewCommentsModal">
                                            <i class="fas fa-comments"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-blog" 
                                                data-id="<?php echo $row['id']; ?>"
                                                data-title="<?php echo htmlspecialchars($row['title']); ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Blog Modal -->
<div class="modal fade" id="addBlogModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Blog</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addBlogForm" action="blog_process.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="writer" class="form-label">Writer</label>
                        <input type="text" class="form-control" id="writer" name="writer" required>
                    </div>
                    <div class="mb-3">
                        <label for="s_article" class="form-label">Short Article</label>
                        <textarea class="form-control" id="s_article" name="s_article" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea class="form-control" id="content" name="content" rows="10" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="image_url" class="form-label">Featured Image</label>
                        <input type="file" class="form-control" id="image_url" name="image_url" accept="image/*" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="none">None</option>
                            <option value="featured">Featured</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="add_blog" class="btn btn-primary">Save Blog</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Blog Modal -->
<div class="modal fade" id="editBlogModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Blog</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editBlogForm" action="blog_process.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="blog_id" id="edit_blog_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="edit_title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_writer" class="form-label">Writer</label>
                        <input type="text" class="form-control" id="edit_writer" name="writer" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_s_article" class="form-label">Short Article</label>
                        <textarea class="form-control" id="edit_s_article" name="s_article" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_content" class="form-label">Content</label>
                        <textarea class="form-control" id="edit_content" name="content" rows="10" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_image_url" class="form-label">Featured Image</label>
                        <input type="file" class="form-control" id="edit_image_url" name="image_url" accept="image/*">
                        <div id="current_image" class="mt-2"></div>
                        <small class="text-muted">Leave empty to keep current image</small>
                    </div>
                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Status</label>
                        <select class="form-control" id="edit_status" name="status" required>
                            <option value="none">None</option>
                            <option value="featured">Featured</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="update_blog" class="btn btn-primary">Update Blog</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Comments Modal -->
<div class="modal fade" id="viewCommentsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Blog Comments</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="commentsContainer" class="comments-list">
                    <!-- Loading spinner -->
                    <div id="commentsLoading" class="text-center d-none">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <!-- Comments will be loaded here -->
                    <div id="commentsList"></div>
                    <!-- No comments message -->
                    <div id="noComments" class="text-center d-none">
                        <p class="text-muted">No comments found for this blog.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteBlogModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Blog</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this blog: <span id="delete_blog_title"></span>?
            </div>
            <div class="modal-footer">
                <form action="blog_process.php" method="POST">
                    <input type="hidden" name="blog_id" id="delete_blog_id">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="delete_blog" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Include TinyMCE -->
<script src="https://cdn.tiny.cloud/1/blpthl5six6mva09ze7qxgnsxavet7r6wq84o56qnsel4s52/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize TinyMCE for add blog content
    tinymce.init({
        selector: '#content',
        height: 400,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | ' +
                'bold italic backcolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | help',
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
        branding: false,
        promotion: false,
        // Add image upload handling
        images_upload_url: 'upload.php',
        automatic_uploads: true,
        images_upload_handler: function (blobInfo, success, failure) {
            var xhr, formData;
            xhr = new XMLHttpRequest();
            xhr.withCredentials = false;
            xhr.open('POST', 'upload.php');
            xhr.onload = function() {
                var json;
                if (xhr.status != 200) {
                    failure('HTTP Error: ' + xhr.status);
                    return;
                }
                json = JSON.parse(xhr.responseText);
                if (!json || typeof json.location != 'string') {
                    failure('Invalid JSON: ' + xhr.responseText);
                    return;
                }
                success(json.location);
            };
            formData = new FormData();
            formData.append('file', blobInfo.blob(), blobInfo.filename());
            xhr.send(formData);
        }
    });

    // Function to fetch and populate blog data
    function fetchBlogData(blogId) {
        fetch('fetchEditBlog.php?id=' + blogId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const blog = data.data;
                    document.getElementById('edit_blog_id').value = blog.id;
                    document.getElementById('edit_title').value = blog.title;
                    document.getElementById('edit_writer').value = blog.writer;
                    document.getElementById('edit_s_article').value = blog.s_article;
                    
                    // Initialize TinyMCE for edit content
                    if (tinymce.get('edit_content')) {
                        tinymce.get('edit_content').remove();
                    }
                    
                    tinymce.init({
                        selector: '#edit_content',
                        height: 400,
                        plugins: [
                            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                            'insertdatetime', 'media', 'table', 'help', 'wordcount'
                        ],
                        toolbar: 'undo redo | blocks | ' +
                                'bold italic backcolor | alignleft aligncenter ' +
                                'alignright alignjustify | bullist numlist outdent indent | ' +
                                'removeformat | help',
                        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
                        branding: false,
                        promotion: false,
                        // Add image upload handling
                        images_upload_url: 'upload.php',
                        automatic_uploads: true,
                        images_upload_handler: function (blobInfo, success, failure) {
                            var xhr, formData;
                            xhr = new XMLHttpRequest();
                            xhr.withCredentials = false;
                            xhr.open('POST', 'upload.php');
                            xhr.onload = function() {
                                var json;
                                if (xhr.status != 200) {
                                    failure('HTTP Error: ' + xhr.status);
                                    return;
                                }
                                json = JSON.parse(xhr.responseText);
                                if (!json || typeof json.location != 'string') {
                                    failure('Invalid JSON: ' + xhr.responseText);
                                    return;
                                }
                                success(json.location);
                            };
                            formData = new FormData();
                            formData.append('file', blobInfo.blob(), blobInfo.filename());
                            xhr.send(formData);
                        },
                        setup: function(editor) {
                            editor.on('init', function() {
                                editor.setContent(blog.content || '');
                            });
                        }
                    });

                    document.getElementById('edit_status').value = blog.status;

                    const currentImageDiv = document.getElementById('current_image');
                    if (blog.image_url) {
                        currentImageDiv.innerHTML = `<img src="../${blog.image_url}" class="img-thumbnail" style="max-height: 100px">`;
                    } else {
                        currentImageDiv.innerHTML = '';
                    }
                } else {
                    alert('Error: ' + data.message);
                    var editModal = bootstrap.Modal.getInstance(document.getElementById('editBlogModal'));
                    if (editModal) {
                        editModal.hide();
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error fetching blog details. Please try again.');
                var editModal = bootstrap.Modal.getInstance(document.getElementById('editBlogModal'));
                if (editModal) {
                    editModal.hide();
                }
            });
    }

    // Add click event listeners to edit buttons
    document.querySelectorAll('.edit-blog').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const blogId = this.getAttribute('data-id');
            const editModal = new bootstrap.Modal(document.getElementById('editBlogModal'));
            editModal.show();
            fetchBlogData(blogId);
        });
    });

    // Handle modal close
    document.getElementById('editBlogModal').addEventListener('hidden.bs.modal', function () {
        // Remove TinyMCE instance
        if (tinymce.get('edit_content')) {
            tinymce.get('edit_content').remove();
        }
        document.getElementById('editBlogForm').reset();
        document.getElementById('current_image').innerHTML = '';
    });

    // Handle form submission
    document.getElementById('editBlogForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get content from TinyMCE
        if (tinymce.get('edit_content')) {
            const content = tinymce.get('edit_content').getContent();
            document.getElementById('edit_content').value = content;
        }
        
        this.submit();
    });

    // Handle delete
    document.querySelectorAll('.delete-blog').forEach(button => {
        button.addEventListener('click', function() {
            const blogId = this.getAttribute('data-id');
            const blogTitle = this.getAttribute('data-title');
            
            if (confirm('Are you sure you want to delete blog: ' + blogTitle + '?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'blog_process.php';
                
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'blog_id';
                idInput.value = blogId;
                
                const deleteInput = document.createElement('input');
                deleteInput.type = 'hidden';
                deleteInput.name = 'delete_blog';
                deleteInput.value = '1';
                
                form.appendChild(idInput);
                form.appendChild(deleteInput);
                document.body.appendChild(form);
                form.submit();
            }
        });
    });

    // Add function to load comments
    function loadBlogComments(blogId) {
        const loadingSpinner = document.getElementById('commentsLoading');
        const commentsList = document.getElementById('commentsList');
        const noComments = document.getElementById('noComments');

        // Show loading spinner
        loadingSpinner.classList.remove('d-none');
        commentsList.innerHTML = '';
        noComments.classList.add('d-none');

        fetch('get_blog_comments.php?blog_id=' + blogId)
            .then(response => response.json())
            .then(data => {
                loadingSpinner.classList.add('d-none');
                
                if (data.success && data.comments.length > 0) {
                    // Group comments by parent_id
                    const mainComments = data.comments.filter(c => !c.parent_id);
                    const replies = data.comments.filter(c => c.parent_id);
                    
                    const commentsHtml = mainComments.map(comment => {
                        // Find replies for this comment
                        const commentReplies = replies.filter(r => r.parent_id === comment.id);
                        const repliesHtml = commentReplies.map(reply => `
                            <div class="reply-item ms-4 mt-2 border-start border-primary ps-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-primary">Reply</span>
                                    <small class="text-muted">${reply.created_at}</small>
                                </div>
                                <p class="mb-1">${reply.comment}</p>
                            </div>
                        `).join('');

                        return `
                            <div class="comment-item border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-secondary">Comment #${comment.id}</span>
                                    <small class="text-muted">${comment.created_at}</small>
                                </div>
                                <p class="mb-2">${comment.comment}</p>
                                ${repliesHtml}
                            </div>
                        `;
                    }).join('');
                    
                    commentsList.innerHTML = commentsHtml;
                } else {
                    noComments.classList.remove('d-none');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                loadingSpinner.classList.add('d-none');
                commentsList.innerHTML = '<div class="alert alert-danger">Error loading comments. Please try again.</div>';
            });
    }

    // Add click event listeners to view comments buttons
    document.querySelectorAll('.view-comments').forEach(button => {
        button.addEventListener('click', function() {
            const blogId = this.getAttribute('data-id');
            loadBlogComments(blogId);
        });
    });
});
</script>

<style>
.comments-list {
    max-height: 500px;
    overflow-y: auto;
}
.comment-item:last-child {
    border-bottom: none !important;
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}
.reply-item {
    background-color: #f8f9fa;
    border-radius: 0.25rem;
}
</style>

<?php require_once 'includes/footer.php'; ?> 