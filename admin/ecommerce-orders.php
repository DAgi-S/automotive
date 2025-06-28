<?php
session_start();
require_once '../includes/config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

include 'includes/header.php';

// Fetch all orders with customer information
$query = "SELECT o.*, u.name as customer_name, u.email as customer_email, 
          p.name as product_name, p.price as product_price
          FROM tbl_orders o 
          LEFT JOIN tbl_user u ON o.user_id = u.id
          LEFT JOIN tbl_products p ON o.product_id = p.id
          ORDER BY o.order_date DESC";
$orders = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Ecommerce Orders</h1>

    <!-- Orders List Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">All Orders</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="ordersTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Total Price</th>
                            <th>Status</th>
                            <th>Order Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></td>
                                <td>
                                    <?php echo htmlspecialchars($order['customer_name']); ?>
                                    <small class="d-block text-muted"><?php echo htmlspecialchars($order['customer_email']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($order['quantity']); ?></td>
                                <td>$<?php echo number_format($order['price'], 2); ?></td>
                                <td>
                                    <?php
                                    $statusClass = [
                                        'pending' => 'warning',
                                        'processing' => 'info',
                                        'completed' => 'success',
                                        'cancelled' => 'danger'
                                    ];
                                    $status = $order['status'];
                                    $class = $statusClass[$status] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?php echo $class; ?>">
                                        <?php echo ucfirst($status); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y H:i', strtotime($order['order_date'])); ?></td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm view-order" 
                                            data-order-id="<?php echo $order['id']; ?>">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <?php if ($status === 'pending'): ?>
                                        <button type="button" class="btn btn-success btn-sm update-status" 
                                                data-order-id="<?php echo $order['id']; ?>"
                                                data-status="processing">
                                            <i class="fas fa-check"></i> Process
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="orderDetailsModalLabel">
                    <i class="fas fa-shopping-cart me-2"></i>Order Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="loadingSpinner" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div id="modalContent" style="display: none;">
                    <!-- Order details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#ordersTable').DataTable({
        order: [[6, 'desc']], // Sort by order date by default
        pageLength: 25,
        responsive: true
    });

    // Handle View Order button click
    $('.view-order').click(function() {
        var orderId = $(this).data('order-id');
        viewOrderDetails(orderId);
    });

    // Handle Update Status button click
    $('.update-status').click(function() {
        var orderId = $(this).data('order-id');
        var newStatus = $(this).data('status');
        updateOrderStatus(orderId, newStatus);
    });
});

function viewOrderDetails(orderId) {
    // Show modal and loading state
    const modal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
    modal.show();
    
    $('#loadingSpinner').show();
    $('#modalContent').hide();

    // Fetch order details
    $.ajax({
        url: 'ajax/get_order_details.php',
        method: 'GET',
        data: { order_id: orderId },
        success: function(response) {
            // Update modal content
            $('#modalContent').html(`
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-user me-2"></i>Customer Information</h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-1"><strong>Name:</strong> ${response.customer_name}</p>
                                <p class="mb-1"><strong>Email:</strong> ${response.customer_email}</p>
                                <p class="mb-0"><strong>Phone:</strong> ${response.customer_phone || 'N/A'}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-box me-2"></i>Order Information</h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-1"><strong>Order ID:</strong> #${String(response.id).padStart(6, '0')}</p>
                                <p class="mb-1"><strong>Status:</strong> <span class="badge bg-${getStatusClass(response.status)}">${response.status}</span></p>
                                <p class="mb-1"><strong>Order Date:</strong> ${formatDate(response.order_date)}</p>
                                <p class="mb-0"><strong>Total Amount:</strong> $${parseFloat(response.price).toFixed(2)}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Order Items</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>${response.product_name}</td>
                                        <td>$${parseFloat(response.product_price).toFixed(2)}</td>
                                        <td>${response.quantity}</td>
                                        <td>$${parseFloat(response.price).toFixed(2)}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `);

            // Hide loading spinner and show content
            $('#loadingSpinner').hide();
            $('#modalContent').fadeIn();
        },
        error: function(xhr) {
            let errorMessage = 'An error occurred while fetching order details.';
            if (xhr.responseJSON && xhr.responseJSON.error) {
                errorMessage = xhr.responseJSON.error;
            }
            $('#loadingSpinner').hide();
            $('#modalContent').html('<div class="alert alert-danger">' + errorMessage + '</div>').show();
        }
    });
}

function updateOrderStatus(orderId, newStatus) {
    if (confirm('Are you sure you want to update the order status?')) {
        $.ajax({
            url: 'ajax/update_order_status.php',
            method: 'POST',
            data: {
                order_id: orderId,
                status: newStatus
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error updating order status: ' + response.error);
                }
            },
            error: function() {
                alert('An error occurred while updating the order status.');
            }
        });
    }
}

function getStatusClass(status) {
    const classes = {
        'pending': 'warning',
        'processing': 'info',
        'completed': 'success',
        'cancelled': 'danger'
    };
    return classes[status] || 'secondary';
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}
</script>

<?php include 'includes/footer.php'; ?> 