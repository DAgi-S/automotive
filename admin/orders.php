<?php
session_start();
require_once('../config/database.php');

// Check if user is logged in
if (!isset($_SESSION['admin_id']) && !isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include('includes/header.php');

?>

<!-- Core CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="css/sb-admin-2.min.css" rel="stylesheet">
<link href="css/custom-admin.css" rel="stylesheet">

<!-- jQuery and Bootstrap Bundle (includes Popper) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Orders Management</h6>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addOrderModal">
                    Add New Order
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="ordersTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Client</th>
                            <th>Car Info</th>
                            <th>Services</th>
                            <th>Technician</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            // Fetch orders with related information
                            $query = "SELECT o.*, 
                                     u.name as client_name,
                                     w.full_name as technician_name,
                                     GROUP_CONCAT(s.service_name SEPARATOR ', ') as services
                                     FROM tbl_service_orders o
                                     LEFT JOIN tbl_user u ON o.client_id = u.id
                                     LEFT JOIN tbl_worker w ON o.technician_id = w.id
                                     LEFT JOIN tbl_order_services os ON o.id = os.order_id
                                     LEFT JOIN tbl_services s ON os.service_id = s.service_id
                                     GROUP BY o.id
                                     ORDER BY o.created_at DESC";
                            
                            $stmt = $conn->query($query);
                            
                            while ($row = $stmt->fetch()) {
                                echo '<tr>';
                                echo '<td>#' . $row['id'] . '</td>';
                                echo '<td>' . date('M d, Y h:i A', strtotime($row['created_at'])) . '</td>';
                                echo '<td>' . htmlspecialchars($row['client_name']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['car_model']) . ' (' . htmlspecialchars($row['license_plate']) . ')</td>';
                                echo '<td>' . htmlspecialchars($row['services']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['technician_name']) . '</td>';
                                echo '<td><span class="badge badge-' . getStatusBadgeClass($row['status']) . '">' . ucfirst($row['status']) . '</span></td>';
                                echo '<td>
                                        <button class="btn btn-sm btn-info edit-order" data-id="' . $row['id'] . '">Edit</button>
                                        <button class="btn btn-sm btn-danger delete-order" data-id="' . $row['id'] . '">Delete</button>
                                        <button class="btn btn-sm btn-secondary view-order" data-id="' . $row['id'] . '">View/Print</button>
                                      </td>';
                                echo '</tr>';
                            }
                        } catch(PDOException $e) {
                            echo '<tr><td colspan="8" class="text-center text-danger">Error loading orders: ' . $e->getMessage() . '</td></tr>';
                        }

                        function getStatusBadgeClass($status) {
                            switch ($status) {
                                case 'pending':
                                    return 'warning';
                                case 'in_progress':
                                    return 'info';
                                case 'completed':
                                    return 'success';
                                case 'cancelled':
                                    return 'danger';
                                default:
                                    return 'secondary';
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Order Modal -->
<div class="modal fade" id="addOrderModal" tabindex="-1" aria-labelledby="addOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addOrderModalLabel">Add New Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="orderForm">
                    <input type="hidden" id="order_id" name="order_id">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="client_id" class="form-label">Client</label>
                            <select class="form-select" id="client_id" name="client_id" required>
                                <option value="">Select Client</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="technician_id" class="form-label">Technician</label>
                            <select class="form-select" id="technician_id" name="technician_id" required>
                                <option value="">Select Technician</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="car_brand" class="form-label">Car Brand</label>
                            <input type="text" class="form-control" id="car_brand" name="car_brand" readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="car_model" class="form-label">Car Model</label>
                            <input type="text" class="form-control" id="car_model" name="car_model" readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="license_plate" class="form-label">License Plate</label>
                            <input type="text" class="form-control" id="license_plate" name="license_plate" readonly>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Services</label>
                        <div id="services_container">
                            <!-- Services will be loaded here -->
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveOrder">Save Order</button>
            </div>
        </div>
    </div>
</div>

<!-- Print Order Modal -->
<div class="modal fade" id="printOrderModal" tabindex="-1" aria-labelledby="printOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="printOrderModalLabel">Order Print Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="printOrderContent">
                <!-- Order details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="printOrderBtn">Print</button>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>

<!-- Page specific scripts -->
<script>
$(document).ready(function() {
    // Load initial data
    loadClients();
    loadTechnicians();
    loadServices();

    // Handle client selection
    $('#client_id').change(function() {
        const selectedOption = $(this).find('option:selected');
        if (selectedOption.val()) {
            // Get car info from data attributes
            const carBrand = selectedOption.data('car-brand') || '';
            const carModel = selectedOption.data('car-model') || '';
            const plateNumber = selectedOption.data('plate-number') || '';
            
            // Update form fields
            $('#car_brand').val(carBrand);
            $('#car_model').val(carModel);
            $('#license_plate').val(plateNumber);
            
            // Show car info in read-only fields
            $('#car_brand, #car_model, #license_plate').prop('readonly', true);
        } else {
            // Clear and enable fields if no client selected
            $('#car_brand, #car_model, #license_plate').val('').prop('readonly', false);
        }
    });

    // Handle Add New Order button click
    $('#addOrderModal').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget);
        // If opened for new order, reset form
        if (!$('#order_id').val()) {
        $('#orderForm')[0].reset();
        $('#order_id').val('');
            loadClients();
            loadTechnicians();
        loadServices();
            $('#addOrderModalLabel').text('Add New Order');
        }
    });

    // Handle form submission
    $('#saveOrder').click(function() {
        // Validate required fields
        if (!$('#client_id').val()) {
            alert('Please select a client');
            return;
        }
        if (!$('#technician_id').val()) {
            alert('Please select a technician');
            return;
        }
        if (!$('#car_brand').val() || !$('#car_model').val() || !$('#license_plate').val()) {
            alert('Car information is incomplete. Please make sure client has car information registered.');
            return;
        }

        const selectedServices = [];
        $('input[name="services[]"]:checked').each(function() {
            selectedServices.push($(this).val());
        });
        
        if (selectedServices.length === 0) {
            alert('Please select at least one service');
            return;
        }

        // Create form data
        const formData = new FormData($('#orderForm')[0]);
        formData.append('services', selectedServices);

        // Submit form
        $.ajax({
            url: 'process_order.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status === 'success') {
                    alert('Order saved successfully!');
                    $('#addOrderModal').modal('hide');
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while saving the order.');
            }
        });
    });

    // Load clients
    function loadClients() {
        $.get('get_clients.php', function(data) {
            $('#client_id').html('<option value="">Select Client</option>' + data);
        });
    }

    // Load technicians
    function loadTechnicians() {
        $.get('get_technicians.php', function(data) {
            $('#technician_id').html('<option value="">Select Technician</option>' + data);
        });
    }

    // Load services
    function loadServices() {
        const orderId = $('#order_id').val();
        $.get('get_services.php' + (orderId ? '?order_id=' + orderId : ''), function(data) {
            $('#services_container').html(data);
        });
    }

    // Handle edit button click
    $(document).on('click', '.edit-order', function() {
        const orderId = $(this).data('id');
        $('#order_id').val(orderId);
        $('#addOrderModalLabel').text('Edit Order');
        
        // Load order details
        $.ajax({
            url: 'get_order_details.php',
            method: 'GET',
            data: { order_id: orderId },
            success: function(response) {
                let data = response;
                if (typeof response === 'string') {
                    try { data = JSON.parse(response); } catch (e) { data = {}; }
                }
                if (data.status === 'success') {
                    // Load clients and technicians, then set values
                    $.when(
                        $.get('get_clients.php', function(clients) {
                            $('#client_id').html('<option value="">Select Client</option>' + clients);
                        $('#client_id').val(data.client_id).trigger('change');
                            $('#car_brand').val(data.car_brand || '');
                            $('#car_model').val(data.car_model || '');
                            $('#license_plate').val(data.plate_number || '');
                        }),
                        $.get('get_technicians.php', function(techs) {
                            $('#technician_id').html('<option value="">Select Technician</option>' + techs);
                        $('#technician_id').val(data.technician_id);
                        })
                    ).then(function() {
                        $('#status').val(data.order_status || data.status);
                        // Load services and check selected ones
                        $.get('get_services.php', { selected_services: data.services }, function(servicesHtml) {
                            $('#services_container').html(servicesHtml);
                            });
                        $('#addOrderModal').modal('show');
                    });
                    } else {
                    alert('Error loading order details: ' + (data.message || 'Unknown error'));
                }
            },
            error: function() {
                alert('Error loading order details: Server error');
            }
        });
    });

    // Reset form when modal is closed
    $('#addOrderModal').on('hidden.bs.modal', function() {
        $('#orderForm')[0].reset();
        $('#order_id').val('');
        $('#addOrderModalLabel').text('Add New Order');
        // Clear all checkboxes
        $('input[name="services[]"]').prop('checked', false);
        // Enable car info fields
        $('#car_brand, #car_model, #license_plate').prop('readonly', false).val('');
    });

    // Handle view/print button click
    $(document).on('click', '.view-order', function() {
        const orderId = $(this).data('id');
        // Always clear modal content before loading
        $('#printOrderContent').html('<div class="text-center text-muted">Loading...</div>');
        // Fetch company branding/info first
        $.get('ajax/company_branding.php', {action: 'get'}, function(companyRes) {
            let brandingHtml = '';
            if (companyRes.success) {
                const b = companyRes.branding || {};
                const i = companyRes.info || {};
                brandingHtml = `
                <style>
                .print-header {
                  background: #f8f9fc;
                  border-bottom: 1px solid #ddd;
                  padding: 16px 24px 8px 24px;
                  margin-bottom: 12px;
                }
                .print-header h4 {
                  margin-bottom: 2px;
                  font-size: 1.4em;
                }
                .print-header .company-meta {
                  font-size: 1em;
                  color: #444;
                }
                .print-section {
                  background: #fff;
                  border: 1px solid #eee;
                  border-radius: 8px;
                  padding: 24px 32px;
                  margin-bottom: 16px;
                }
                .print-label { font-weight: bold; color: #222; }
                .print-value { color: #222; }
                @media print {
                  body { background: #fff !important; }
                  .print-section { box-shadow: none !important; }
                }
                </style>
                <div class='print-header'>
                  <div class='row align-items-center'>
                    <div class='col-2 text-center'>
                      <img src='${b.logo_url || '../assets/img/logo.png'}' alt='Logo' style='max-height:60px;'>
                    </div>
                    <div class='col-10'>
                      <h4 class='mb-0'>${b.company_name || ''}</h4>
                      <div class='company-meta'>
                        <span>${i.address || ''}</span> |
                        <span>${i.phone || ''}</span> |
                        <span>${i.email || ''}</span> |
                        <span>${i.website || ''}</span>
                      </div>
                    </div>
                  </div>
                </div>
                `;
            }
            // Fetch order details for print
            $.ajax({
                url: 'get_order_details.php',
                method: 'GET',
                data: { order_id: orderId },
                success: function(response) {
                    let data = response;
                    if (typeof response === 'string') {
                        try { data = JSON.parse(response); } catch (e) { data = {}; }
                    }
                    if (data.status === 'success') {
                        // Fetch service names and prices
                        $.get('get_services.php', function(servicesHtml) {
                            // Map service IDs to names/prices
                            let serviceMap = {};
                            $(servicesHtml).find('input[type=checkbox]').each(function() {
                                const id = $(this).val();
                                const label = $(this).next('label').text();
                                serviceMap[id] = label;
                            });
                            let selectedServices = (data.services || []).map(function(id) {
                                return serviceMap[id] || id;
                            }).join('<br>');
                            // Build print preview HTML
                            let html = `${brandingHtml}
                            <div class='print-section'>
                              <div class='row mb-3'>
                                <div class='col-6'>
                                  <div class='mb-2 print-label'>Client Information</div>
                                  <div><span class='print-label'>Name:</span> <span class='print-value'>${data.client_name}</span></div>
                                  <div><span class='print-label'>Phone:</span> <span class='print-value'>${data.client_phone || ''}</span></div>
                                </div>
                                <div class='col-6'>
                                  <div class='mb-2 print-label'>Vehicle Information</div>
                                  <div><span class='print-label'>Car Brand:</span> <span class='print-value'>${data.car_brand || ''}</span></div>
                                  <div><span class='print-label'>Car Model:</span> <span class='print-value'>${data.car_model || ''}</span></div>
                                  <div><span class='print-label'>License Plate:</span> <span class='print-value'>${data.plate_number || ''}</span></div>
                                </div>
                              </div>
                              <div class='row mb-3'>
                                <div class='col-6'>
                                  <div class='mb-2 print-label'>Technician</div>
                                  <div class='print-value'>${data.technician_name || ''}</div>
                                </div>
                                <div class='col-6'>
                                  <div class='mb-2 print-label'>Status</div>
                                  <div class='print-value'>${(data.order_status || '').charAt(0).toUpperCase() + (data.order_status || '').slice(1)}</div>
                                </div>
                              </div>
                              <div class='row mb-3'>
                                <div class='col-12'>
                                  <div class='mb-2 print-label'>Services</div>
                                  <div class='print-value'>${selectedServices}</div>
                                </div>
                              </div>
                            </div>`;
                            $('#printOrderContent').html(html);
                            $('#printOrderModal').modal('show');
                        });
                    } else {
                        $('#printOrderContent').html('<div class="alert alert-danger">Error loading order details for print.</div>');
                        $('#printOrderModal').modal('show');
                    }
                },
                error: function() {
                    $('#printOrderContent').html('<div class="alert alert-danger">Server error loading order details for print.</div>');
                    $('#printOrderModal').modal('show');
                }
            });
        }, 'json');
    });

    // Print button
    $('#printOrderBtn').click(function() {
        var printContents = document.getElementById('printOrderContent').innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        location.reload();
    });
});
</script> 