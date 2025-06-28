<?php
require_once 'includes/header.php';
require_once '../includes/config.php';

// Handle Delete Operation
if (isset($_POST['delete_article'])) {
    $article_id = $_POST['article_id'];
    $delete_sql = "DELETE FROM tbl_articles WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $article_id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Article deleted successfully";
    } else {
        $_SESSION['error'] = "Error deleting article";
    }
    header("Location: article.php");
    exit();
}
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Articles Management</h1>
        <div>
            <button class="btn btn-info me-2" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <i class="fas fa-folder-plus"></i> Quick Add Category
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addArticleModal">
                <i class="fas fa-plus"></i> Add New Article
            </button>
        </div>
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

    <!-- Articles Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Articles List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="articlesTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Author</th>
                            <th>Published Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT a.*, c.category_name, u.username as author_name 
                                FROM tbl_articles a 
                                LEFT JOIN tbl_article_categories c ON a.category_id = c.id 
                                LEFT JOIN tbl_admin u ON a.author = u.id 
                                ORDER BY a.created_at DESC";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute();
                        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        if (!empty($result)) {
                            foreach ($result as $row) {
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                                    <td><?php echo htmlspecialchars($row['category_name'] ?? 'Uncategorized'); ?></td>
                                    <td><?php echo htmlspecialchars($row['author_name']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $row['status'] == 'published' ? 'success' : 'warning'; ?>">
                                            <?php echo ucfirst($row['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info edit-article" 
                                                data-id="<?php echo $row['id']; ?>"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editArticleModal">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-article" 
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

<!-- Add Article Modal -->
<div class="modal fade" id="addArticleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Article</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addArticleForm" action="article_process.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-control" id="category" name="category_id" required>
                            <?php
                            $cat_sql = "SELECT * FROM tbl_article_categories ORDER BY category_name";
                            $cat_result = $conn->query($cat_sql);
                            if ($cat_result && $cat_result->num_rows > 0) {
                                while ($cat = $cat_result->fetch_assoc()) {
                                    echo "<option value='" . $cat['id'] . "'>" . htmlspecialchars($cat['category_name']) . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea class="form-control" id="content" name="content" rows="10" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="featured_image" class="form-label">Featured Image</label>
                        <input type="file" class="form-control" id="featured_image" name="featured_image" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="add_article" class="btn btn-primary">Save Article</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Article Modal -->
<div class="modal fade" id="editArticleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Article</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editArticleForm" action="article_process.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="article_id" id="edit_article_id">
                <div class="modal-body">
                    <!-- Form fields will be populated by JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="update_article" class="btn btn-primary">Update Article</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteArticleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Article</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this article: <span id="delete_article_title"></span>?
            </div>
            <div class="modal-footer">
                <form action="article_process.php" method="POST">
                    <input type="hidden" name="article_id" id="delete_article_id">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="delete_article" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addCategoryForm" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="category_name" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="category_name" name="category_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="category_description" class="form-label">Description (Optional)</label>
                        <textarea class="form-control" id="category_description" name="category_description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Include TinyMCE Config -->
<?php require_once 'includes/tinymce_config.php'; ?>

<!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#articlesTable').DataTable({
        order: [[3, 'desc']], // Sort by published date by default
        pageLength: 10,
        responsive: true
    });

    // Initialize TinyMCE with CDN version
    tinymce.init({
        selector: '#content',
        height: 400,
        menubar: true,
        branding: false,
        promotion: false,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | ' +
                'bold italic backcolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | help',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px; }',
        setup: function(editor) {
            editor.on('change', function() {
                editor.save();
            });
        }
    });

    // Initialize TinyMCE for edit form when modal opens
    $('#editArticleModal').on('shown.bs.modal', function() {
        if (tinymce.get('edit_content')) {
            tinymce.get('edit_content').remove();
        }
        tinymce.init({
            selector: '#edit_content',
            height: 400,
            menubar: true,
            branding: false,
            promotion: false,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | ' +
                    'bold italic backcolor | alignleft aligncenter ' +
                    'alignright alignjustify | bullist numlist outdent indent | ' +
                    'removeformat | help',
            content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px; }',
            setup: function(editor) {
                editor.on('change', function() {
                    editor.save();
                });
            }
        });
    });

    // Clean up TinyMCE instance when modal is closed
    $('#editArticleModal').on('hidden.bs.modal', function() {
        if (tinymce.get('edit_content')) {
            tinymce.get('edit_content').remove();
        }
    });

    // Handle Edit Article
    $('.edit-article').click(function() {
        const articleId = $(this).data('id');
        
        // Fetch article data
        $.get('get_article.php', {id: articleId}, function(data) {
            const article = JSON.parse(data);
            $('#edit_article_id').val(article.id);
            $('#editArticleForm .modal-body').html(`
                <div class="mb-3">
                    <label for="edit_title" class="form-label">Title</label>
                    <input type="text" class="form-control" id="edit_title" name="title" value="${article.title}" required>
                </div>
                <div class="mb-3">
                    <label for="edit_category" class="form-label">Category</label>
                    <select class="form-control" id="edit_category" name="category_id" required>
                        ${article.categories}
                    </select>
                </div>
                <div class="mb-3">
                    <label for="edit_content" class="form-label">Content</label>
                    <textarea class="form-control" id="edit_content" name="content" rows="10" required>${article.content}</textarea>
                </div>
                <div class="mb-3">
                    <label for="edit_featured_image" class="form-label">Featured Image</label>
                    ${article.current_image ? `<div class="mb-2"><img src="${article.current_image}" class="img-thumbnail" style="max-height: 100px"></div>` : ''}
                    <input type="file" class="form-control" id="edit_featured_image" name="featured_image" accept="image/*">
                </div>
                <div class="mb-3">
                    <label for="edit_status" class="form-label">Status</label>
                    <select class="form-control" id="edit_status" name="status" required>
                        <option value="draft" ${article.status === 'draft' ? 'selected' : ''}>Draft</option>
                        <option value="published" ${article.status === 'published' ? 'selected' : ''}>Published</option>
                    </select>
                </div>
            `);
        });
    });

    // Handle Delete Article
    $('.delete-article').click(function() {
        const articleId = $(this).data('id');
        const articleTitle = $(this).data('title');
        
        $('#delete_article_id').val(articleId);
        $('#delete_article_title').text(articleTitle);
        $('#deleteArticleModal').modal('show');
    });

    // Handle Add Category Form Submission
    $('#addCategoryForm').on('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        var categoryName = $('#category_name').val();
        var categoryDescription = $('#category_description').val();
        
        if (!categoryName) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Category name is required',
                position: 'top-end',
                toast: true,
                timer: 3000
            });
            return;
        }
        
        // Send AJAX request
        $.ajax({
            type: 'POST',
            url: 'article_process.php',
            data: {
                add_category: true,
                category_name: categoryName,
                category_description: categoryDescription
            },
            success: function(response) {
                try {
                    var result = JSON.parse(response);
                    
                    if (result.success) {
                        // Add new category to select dropdowns
                        var newOption = new Option(result.category_name, result.category_id);
                        $('#category').append(newOption);
                        if ($('#edit_category').length) {
                            $('#edit_category').append($(newOption).clone());
                        }
                        
                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: result.message,
                            position: 'top-end',
                            toast: true,
                            showConfirmButton: false,
                            timer: 3000
                        });
                        
                        // Close modal and reset form
                        $('#addCategoryModal').modal('hide');
                        $('#addCategoryForm')[0].reset();
                    } else {
                        // Show error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: result.message || 'Error adding category',
                            position: 'top-end',
                            toast: true,
                            timer: 3000
                        });
                    }
                } catch (e) {
                    console.error('Parse error:', e);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Invalid response from server',
                        position: 'top-end',
                        toast: true,
                        timer: 3000
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Ajax Error:', status, error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Error processing request. Please try again.',
                    position: 'top-end',
                    toast: true,
                    timer: 3000
                });
            }
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>