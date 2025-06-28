<?php
session_start();
require_once('../config/database.php');
include('includes/header.php');

// Only allow admin access
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
$pageTitle = 'Company Management';
?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Company Branding & Information</h6>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editBrandingModal">Edit Branding</button>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-3 text-center">
                    <img src="../assets/img/logo.png" alt="Company Logo" id="companyLogo" class="img-fluid mb-2" style="max-height: 80px;">
                    <div><strong id="companyName">Company Name</strong></div>
                    <div class="mt-2">
                        <span class="badge bg-primary" id="primaryColor">Primary</span>
                        <span class="badge bg-secondary" id="secondaryColor">Secondary</span>
                    </div>
                </div>
                <div class="col-md-9">
                    <h6>Contact Information</h6>
                    <p><strong>Address:</strong> <span id="companyAddress">123 Main St, City</span></p>
                    <p><strong>Phone:</strong> <span id="companyPhone">+1234567890</span></p>
                    <p><strong>Email:</strong> <span id="companyEmail">info@company.com</span></p>
                    <p><strong>Website:</strong> <span id="companyWebsite">www.company.com</span></p>
                    <p><strong>About:</strong> <span id="companyAbout">Short company description goes here.</span></p>
                    <button class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#editInfoModal">Edit Info</button>
                </div>
            </div>
            <hr>
            <h6>Settings</h6>
            <div class="row">
                <div class="col-md-6">
                    <ul class="list-group" id="settingsList">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Example Setting
                            <span>Value</span>
                            <button class="btn btn-sm btn-outline-secondary ms-2" data-bs-toggle="modal" data-bs-target="#editSettingModal">Edit</button>
                        </li>
                    </ul>
                </div>
            </div>
            <button class="btn btn-outline-primary btn-sm mt-3" data-bs-toggle="modal" data-bs-target="#addSettingModal">Add Setting</button>
        </div>
    </div>
</div>

<!-- Branding Modal -->
<div class="modal fade" id="editBrandingModal" tabindex="-1" aria-labelledby="editBrandingModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editBrandingModalLabel">Edit Company Branding</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="brandingForm">
          <div class="mb-3">
            <label for="companyNameInput" class="form-label">Company Name</label>
            <input type="text" class="form-control" id="companyNameInput" name="company_name" required>
          </div>
          <div class="mb-3">
            <label for="logoInput" class="form-label">Logo</label>
            <input type="file" class="form-control" id="logoInput" name="logo">
          </div>
          <div class="mb-3">
            <label for="primaryColorInput" class="form-label">Primary Color</label>
            <input type="color" class="form-control form-control-color" id="primaryColorInput" name="primary_color" value="#4e73df">
          </div>
          <div class="mb-3">
            <label for="secondaryColorInput" class="form-label">Secondary Color</label>
            <input type="color" class="form-control form-control-color" id="secondaryColorInput" name="secondary_color" value="#858796">
          </div>
          <div class="mb-3">
            <label for="taglineInput" class="form-label">Tagline</label>
            <input type="text" class="form-control" id="taglineInput" name="tagline">
          </div>
          <button type="submit" class="btn btn-primary">Save</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Info Modal -->
<div class="modal fade" id="editInfoModal" tabindex="-1" aria-labelledby="editInfoModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editInfoModalLabel">Edit Company Information</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="infoForm">
          <div class="mb-3">
            <label for="addressInput" class="form-label">Address</label>
            <input type="text" class="form-control" id="addressInput" name="address">
          </div>
          <div class="mb-3">
            <label for="phoneInput" class="form-label">Phone</label>
            <input type="text" class="form-control" id="phoneInput" name="phone">
          </div>
          <div class="mb-3">
            <label for="emailInput" class="form-label">Email</label>
            <input type="email" class="form-control" id="emailInput" name="email">
          </div>
          <div class="mb-3">
            <label for="websiteInput" class="form-label">Website</label>
            <input type="text" class="form-control" id="websiteInput" name="website">
          </div>
          <div class="mb-3">
            <label for="aboutInput" class="form-label">About</label>
            <textarea class="form-control" id="aboutInput" name="about" rows="3"></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Save</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Edit Setting Modal -->
<div class="modal fade" id="editSettingModal" tabindex="-1" aria-labelledby="editSettingModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editSettingModalLabel">Edit Setting</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="settingForm">
          <div class="mb-3">
            <label for="settingKeyInput" class="form-label">Setting Key</label>
            <input type="text" class="form-control" id="settingKeyInput" name="setting_key" readonly>
          </div>
          <div class="mb-3">
            <label for="settingValueInput" class="form-label">Setting Value</label>
            <input type="text" class="form-control" id="settingValueInput" name="setting_value">
          </div>
          <button type="submit" class="btn btn-primary">Save</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Add Setting Modal -->
<div class="modal fade" id="addSettingModal" tabindex="-1" aria-labelledby="addSettingModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addSettingModalLabel">Add Setting</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="addSettingForm">
          <div class="mb-3">
            <label for="addSettingKeyInput" class="form-label">Setting Key</label>
            <input type="text" class="form-control" id="addSettingKeyInput" name="setting_key">
          </div>
          <div class="mb-3">
            <label for="addSettingValueInput" class="form-label">Setting Value</label>
            <input type="text" class="form-control" id="addSettingValueInput" name="setting_value">
          </div>
          <button type="submit" class="btn btn-primary">Add</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Toast Notification -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
  <div id="companyToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body" id="companyToastBody">
        Success!
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="company.js"></script>
<script>
function showCompanyToast(message, isSuccess = true) {
    var toast = new bootstrap.Toast(document.getElementById('companyToast'));
    $('#companyToast').removeClass('bg-success bg-danger').addClass(isSuccess ? 'bg-success' : 'bg-danger');
    $('#companyToastBody').text(message);
    toast.show();
}
// Remove any leftover modal-backdrop on modal close

</script>
<?php include('includes/footer.php'); ?> 