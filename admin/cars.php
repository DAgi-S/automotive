<?php
session_start();
require_once '../includes/config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

include 'includes/header.php';

// Fetch all cars with owner information
$query = "SELECT ti.*, u.name as owner_name, u.email as owner_email, u.phonenum as owner_phone,
          cb.brand_name, cm.model_name
          FROM tbl_info ti 
          LEFT JOIN tbl_user u ON ti.userid = u.id
          LEFT JOIN car_brands cb ON ti.car_brand = cb.id
          LEFT JOIN car_models cm ON ti.car_model = cm.id
          ORDER BY ti.created_at DESC";
$cars = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Registered Cars</h1>

    <!-- Cars List Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">All Registered Cars</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="carsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>OWNER</th>
                            <th>BRAND</th>
                            <th>MODEL</th>
                            <th>MILEAGE</th>
                            <th>REGISTRATION DATE</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cars as $car): ?>
                            <tr>
                                <td>
                                    <?php echo htmlspecialchars($car['owner_name']); ?>
                                    <small class="d-block text-muted"><?php echo htmlspecialchars($car['owner_email']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($car['brand_name']); ?></td>
                                <td><?php echo htmlspecialchars($car['model_name']); ?></td>
                                <td><?php echo htmlspecialchars($car['mile_age']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($car['created_at'])); ?></td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm view-details" 
                                            data-car-id="<?php echo $car['id']; ?>">
                                        <i class="fas fa-eye"></i> View Details
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Car Details Modal -->
<div class="modal fade" id="carDetailsModal" tabindex="-1" aria-labelledby="carDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="carDetailsModalLabel">
                    <i class="fas fa-car me-2"></i>Car Details
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
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="fas fa-user me-2"></i>Owner Information</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-1"><strong>Name:</strong> <span id="ownerName"></span></p>
                                    <p class="mb-1"><strong>Email:</strong> <span id="ownerEmail"></span></p>
                                    <p class="mb-0"><strong>Phone:</strong> <span id="ownerPhone"></span></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Vehicle Information</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-1"><strong>Brand:</strong> <span id="carBrand"></span></p>
                                    <p class="mb-1"><strong>Model:</strong> <span id="carModel"></span></p>
                                    <p class="mb-1"><strong>Mileage:</strong> <span id="carMileage"></span></p>
                                    <p class="mb-1"><strong>Plate Number:</strong> <span id="plateNumber"></span></p>
                                    <p class="mb-0"><strong>Trailer Number:</strong> <span id="trailerNumber"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-history me-2"></i>Service History</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Service</th>
                                            <th>Status</th>
                                            <th>Last Service Date</th>
                                            <th>Next Due</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr id="oilChangeRow">
                                            <td>Oil Change</td>
                                            <td><span id="oilChangeStatus" class="badge"></span></td>
                                            <td id="oilChangeDate"></td>
                                            <td id="oilChangeDue"></td>
                                        </tr>
                                        <tr id="insuranceRow">
                                            <td>Insurance</td>
                                            <td><span id="insuranceStatus" class="badge"></span></td>
                                            <td id="insuranceDate"></td>
                                            <td id="insuranceDue"></td>
                                        </tr>
                                        <tr id="boloRow">
                                            <td>Bolo</td>
                                            <td><span id="boloStatus" class="badge"></span></td>
                                            <td id="boloDate"></td>
                                            <td id="boloDue"></td>
                                        </tr>
                                        <tr id="rdWegenRow">
                                            <td>RD Wegen</td>
                                            <td><span id="rdWegenStatus" class="badge"></span></td>
                                            <td id="rdWegenDate"></td>
                                            <td id="rdWegenDue"></td>
                                        </tr>
                                        <tr id="yemengedFendRow">
                                            <td>Yemenged Fend</td>
                                            <td><span id="yemengedFendStatus" class="badge"></span></td>
                                            <td id="yemengedFendDate"></td>
                                            <td id="yemengedFendDue"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
.service-date {
    font-size: 0.9rem;
}

.service-date.expired {
    color: #e74a3b;
}

.service-date.valid {
    color: #1cc88a;
}

.service-date.warning {
    color: #f6c23e;
}

.badge {
    font-size: 0.8rem;
    padding: 0.4em 0.8em;
}

.badge-success {
    background-color: #1cc88a;
}

.badge-danger {
    background-color: #e74a3b;
}

.badge-warning {
    background-color: #f6c23e;
    color: #fff;
}

.card-header h6 {
    font-size: 1rem;
    margin: 0;
}

.table td {
    vertical-align: middle;
}

.modal-lg {
    max-width: 900px;
}

.btn-close-white {
    filter: invert(1) grayscale(100%) brightness(200%);
}
</style>

<?php include 'includes/footer.php'; ?>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#carsTable').DataTable({
        order: [[4, 'desc']], // Sort by registration date by default
        pageLength: 25,
        responsive: true
    });

    // Handle View Details button click
    $('.view-details').click(function() {
        var carId = $(this).data('car-id');
        viewCarDetails(carId);
    });
});

function viewCarDetails(carId) {
    // Show modal and loading state
    const modal = new bootstrap.Modal(document.getElementById('carDetailsModal'));
    modal.show();
    
    $('#loadingSpinner').show();
    $('#modalContent').hide();

    // Fetch car details
    $.ajax({
        url: 'ajax/get_car_details.php',
        method: 'GET',
        data: { car_id: carId },
        success: function(response) {
            // Update basic information
            $('#ownerName').text(response.owner_name || 'N/A');
            $('#ownerEmail').text(response.owner_email || 'N/A');
            $('#ownerPhone').text(response.owner_phone || 'N/A');
            $('#carBrand').text(response.brand_name || 'N/A');
            $('#carModel').text(response.model_name || 'N/A');
            $('#carMileage').text(response.mile_age || 'N/A');
            $('#plateNumber').text(response.plate_number || 'N/A');
            $('#trailerNumber').text(response.trailer_number || 'N/A');

            // Update service information
            updateServiceInfo('oilChange', response.services.oil_change);
            updateServiceInfo('insurance', response.services.insurance);
            updateServiceInfo('bolo', response.services.bolo);
            updateServiceInfo('rdWegen', response.services.rd_wegen);
            updateServiceInfo('yemengedFend', response.services.yemenged_fend);

            // Hide loading spinner and show content
            $('#loadingSpinner').hide();
            $('#modalContent').fadeIn();
        },
        error: function(xhr) {
            let errorMessage = 'An error occurred while fetching car details.';
            if (xhr.responseJSON && xhr.responseJSON.error) {
                errorMessage = xhr.responseJSON.error;
            }
            $('#loadingSpinner').hide();
            $('#modalContent').html('<div class="alert alert-danger">' + errorMessage + '</div>').show();
        }
    });
}

function updateServiceInfo(serviceId, service) {
    const statusElement = $(`#${serviceId}Status`);
    const dateElement = $(`#${serviceId}Date`);
    const dueElement = $(`#${serviceId}Due`);
    
    // Update status badge
    if (service.value === 'yes') {
        statusElement.removeClass().addClass('badge bg-success').text('Active');
    } else {
        statusElement.removeClass().addClass('badge bg-danger').text('Inactive');
    }

    // Update service date and calculate due date
    if (service.date) {
        const serviceDate = new Date(service.date);
        const today = new Date();
        const monthsDiff = (today - serviceDate) / (1000 * 60 * 60 * 24 * 30);
        
        // Format the service date
        dateElement.text(serviceDate.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        }));

        // Calculate and display next due date (assuming 1 year validity)
        const dueDate = new Date(serviceDate);
        dueDate.setFullYear(dueDate.getFullYear() + 1);
        
        let dueClass = 'valid';
        let dueText = dueDate.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        if (monthsDiff > 12) {
            dueClass = 'expired';
            dueText += ' (Expired)';
        } else if (monthsDiff > 10) {
            dueClass = 'warning';
            dueText += ' (Due Soon)';
        }

        dueElement.html(`<span class="service-date ${dueClass}">${dueText}</span>`);
    } else {
        dateElement.text('Not Available');
        dueElement.text('Not Available');
    }
}
</script> 