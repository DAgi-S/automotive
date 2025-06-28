$(document).ready(function() {
    let lastCompanyInfo = {};
    let lastCompanyBranding = {};
    function loadCompanyData() {
        $.get('ajax/company_branding.php', {action: 'get'}, function(res) {
            if (res.success) {
                // Branding
                $('#companyLogo').attr('src', res.branding.logo_url || '../assets/img/logo.png');
                $('#companyName').text(res.branding.company_name || 'Company Name');
                $('#primaryColor').css('background', res.branding.primary_color || '#4e73df');
                $('#secondaryColor').css('background', res.branding.secondary_color || '#858796');
                lastCompanyBranding = res.branding || {};
                // Info
                $('#companyAddress').text(res.info.address || '');
                $('#companyPhone').text(res.info.phone || '');
                $('#companyEmail').text(res.info.email || '');
                $('#companyWebsite').text(res.info.website || '');
                $('#companyAbout').text(res.info.about || '');
                lastCompanyInfo = res.info || {};
                // Settings
                let settingsHtml = '';
                (res.settings || []).forEach(function(s) {
                    settingsHtml += `<li class="list-group-item d-flex justify-content-between align-items-center">
                        ${s.setting_key}
                        <span>${s.setting_value}</span>
                        <button class="btn btn-sm btn-outline-secondary ms-2 edit-setting-btn" data-id="${s.id}" data-key="${s.setting_key}" data-value="${s.setting_value}" data-bs-toggle="modal" data-bs-target="#editSettingModal">Edit</button>
                        <button class="btn btn-sm btn-outline-danger ms-2 delete-setting-btn" data-id="${s.id}">Delete</button>
                    </li>`;
                });
                $('#settingsList').html(settingsHtml);
            }
        }, 'json');
    }
    loadCompanyData();

    // Branding form
    $('#brandingForm').submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('action', 'update_branding');
        $.ajax({
            url: 'ajax/company_branding.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    $('#editBrandingModal').modal('hide');
                    // Reset form and modal state after closing
                    $('#brandingForm')[0].reset();
                    setTimeout(function() {
                        $('body').removeClass('modal-open');
                        $('.modal-backdrop').remove();
                    }, 300);
                    loadCompanyData();
                    showCompanyToast('Branding updated successfully!', true);
                } else {
                    showCompanyToast(res.error || 'Error updating branding', false);
                }
            },
            error: function() {
                showCompanyToast('Server error updating branding', false);
            }
        });
    });
    // Info form
    $('#infoForm').submit(function(e) {
        e.preventDefault();
        var data = $(this).serialize() + '&action=update_info';
        $.post('ajax/company_branding.php', data, function(res) {
            if (res.success) {
                $('#editInfoModal').modal('hide');
                loadCompanyData();
                showCompanyToast('Company info updated!', true);
            } else {
                showCompanyToast(res.error || 'Error updating info', false);
            }
        }, 'json').fail(function() {
            showCompanyToast('Server error updating info', false);
        });
    });
    // Edit setting
    $(document).on('click', '.edit-setting-btn', function() {
        $('#settingKeyInput').val($(this).data('key'));
        $('#settingValueInput').val($(this).data('value'));
        $('#settingForm').data('id', $(this).data('id'));
    });
    $('#settingForm').submit(function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var data = $(this).serialize() + '&action=update_setting&id=' + id;
        $.post('ajax/company_branding.php', data, function(res) {
            if (res.success) {
                $('#editSettingModal').modal('hide');
                loadCompanyData();
                showCompanyToast('Setting updated!', true);
            } else {
                showCompanyToast(res.error || 'Error updating setting', false);
            }
        }, 'json').fail(function() {
            showCompanyToast('Server error updating setting', false);
        });
    });
    // Add setting
    $('#addSettingForm').submit(function(e) {
        e.preventDefault();
        var data = $(this).serialize() + '&action=add_setting';
        $.post('ajax/company_branding.php', data, function(res) {
            if (res.success) {
                $('#addSettingModal').modal('hide');
                loadCompanyData();
                showCompanyToast('Setting added!', true);
            } else {
                showCompanyToast(res.error || 'Error adding setting', false);
            }
        }, 'json').fail(function() {
            showCompanyToast('Server error adding setting', false);
        });
    });
    // Delete setting
    $(document).on('click', '.delete-setting-btn', function() {
        if (confirm('Delete this setting?')) {
            var id = $(this).data('id');
            $.post('ajax/company_branding.php', {action: 'delete_setting', id: id}, function(res) {
                if (res.success) {
                    loadCompanyData();
                    showCompanyToast('Setting deleted!', true);
                } else {
                    showCompanyToast(res.error || 'Error deleting setting', false);
                }
            }, 'json').fail(function() {
                showCompanyToast('Server error deleting setting', false);
            });
        }
    });
    // Remove any leftover modal-backdrop on modal close
    $(document).on('hidden.bs.modal', function () {
        $('.modal-backdrop').remove();
    });

    // Pre-fill info modal on open
    $('#editInfoModal').on('show.bs.modal', function() {
        $('#addressInput').val(lastCompanyInfo.address || '');
        $('#phoneInput').val(lastCompanyInfo.phone || '');
        $('#emailInput').val(lastCompanyInfo.email || '');
        $('#websiteInput').val(lastCompanyInfo.website || '');
        $('#aboutInput').val(lastCompanyInfo.about || '');
    });

    // Pre-fill branding modal on open
    $('#editBrandingModal').on('show.bs.modal', function() {
        $('#companyNameInput').val(lastCompanyBranding.company_name || '');
        $('#taglineInput').val(lastCompanyBranding.tagline || '');
        $('#primaryColorInput').val(lastCompanyBranding.primary_color || '#4e73df');
        $('#secondaryColorInput').val(lastCompanyBranding.secondary_color || '#858796');
        // Logo input is file type, so we don't pre-fill it, but could show preview if needed
    });
}); 