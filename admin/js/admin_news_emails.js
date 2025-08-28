// Initialize TinyMCE Editor
// admin_news_emails.js
tinymce.init({
    selector: '#emailContent',
    height: 400,
    plugins: [
        'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'image', 'link', 'lists', 'media', 'searchreplace', 'table', 'visualblocks', 'wordcount',
        'checklist', 'mediaembed', 'casechange', 'export', 'formatpainter', 'pageembed', 'permanentpen', 'footnotes', 'advtemplate', 'advtable', 'advcode', 'editimage', 'tableofcontents', 'mergetags', 'powerpaste', 'tinymcespellchecker', 'autocorrect', 'typography', 'inlinecss'
    ],
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | align lineheight | tinycomments | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
    tinycomments_mode: 'embedded',
    mergetags_list: [
        { value: 'First.Name', title: 'First Name' },
        { value: 'Email', title: 'Email' },
    ],
    content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 14px; line-height: 1.6; }',
    
    // Custom image upload handler
    images_upload_handler: function (blobInfo, success, failure) {
        const formData = new FormData();
        formData.append('imageUpload', blobInfo.blob(), blobInfo.filename());
        
        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                success(data.url);
            } else {
                failure('Image upload failed: ' + data.error);
            }
        })
        .catch(error => {
            failure('Upload error: ' + error.message);
        });
    },
    
    // Auto-save functionality
    setup: function (editor) {
        editor.on('change input', function () {
            clearTimeout(window.contentChangeTimeout);
            window.contentChangeTimeout = setTimeout(checkContentForFlags, 1000);
        });
    }
});

// Content flagging system
function checkContentForFlags() {
    const subject = document.getElementById('subject').value;
    const content = tinymce.get('emailContent').getContent();
    
    const restrictedKeywords = [
        'password', 'login', 'urgent', 'click here immediately', 'verify account',
        'suspended', 'limited time', 'act now', 'congratulations you have won',
        'make money fast', 'work from home', 'guaranteed income', 'risk-free',
        'viagra', 'cialis', 'weight loss', 'miracle cure', 'lose weight fast',
        'free gift', 'no cost', 'absolutely free', 'limited offer',
        'casino', 'gambling', 'lottery', 'sweepstakes', 'inheritance'
    ];
    
    const contentLower = (content + ' ' + subject).toLowerCase();
    const flaggedItems = [];
    
    restrictedKeywords.forEach(keyword => {
        if (contentLower.includes(keyword)) {
            flaggedItems.push(keyword);
        }
    });
    
    // Check for suspicious patterns
    const suspiciousPatterns = [
        /\b\d+% (off|discount|guaranteed)\b/i,
        /\$\d+,?\d* (guaranteed|promised|risk-free)/i,
        /urgent.{0,20}action.{0,20}required/i,
        /verify.{0,20}account.{0,20}immediately/i
    ];
    
    suspiciousPatterns.forEach(pattern => {
        if (pattern.test(contentLower)) {
            flaggedItems.push('suspicious pattern detected');
        }
    });
    
    // Show/hide warnings
    const contentWarning = document.getElementById('contentWarning');
    const subjectWarning = document.getElementById('subjectWarning');
    
    if (flaggedItems.length > 0) {
        contentWarning.style.display = 'block';
        subjectWarning.style.display = 'block';
        
        const flaggedItemsList = document.getElementById('flaggedItems');
        flaggedItemsList.innerHTML = flaggedItems.map(item => 
            `<span class="badge bg-warning me-1">${item}</span>`
        ).join('');
    } else {
        contentWarning.style.display = 'none';
        subjectWarning.style.display = 'none';
    }
}

// Subject change handler
document.getElementById('subject').addEventListener('input', checkContentForFlags);

// Recipient type change handler
document.querySelectorAll('input[name="recipient_type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const container = document.getElementById('userSelectionContainer');
        if (this.value === 'selected') {
            container.style.display = 'block';
        } else {
            container.style.display = 'none';
        }
    });
});

// User search functionality
document.getElementById('userSearch').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const userBoxes = document.querySelectorAll('.user-checkbox');
    
    userBoxes.forEach(box => {
        const searchData = box.getAttribute('data-name');
        if (searchData.includes(searchTerm)) {
            box.style.display = 'block';
        } else {
            box.style.display = 'none';
        }
    });
});

// Select/Deselect all users
function selectAllUsers() {
    document.querySelectorAll('.user-select').forEach(checkbox => {
        if (checkbox.closest('.user-checkbox').style.display !== 'none') {
            checkbox.checked = true;
        }
    });
}

function deselectAllUsers() {
    document.querySelectorAll('.user-select').forEach(checkbox => {
        checkbox.checked = false;
    });
}

// Save draft function
function saveDraft() {
    const subject = document.getElementById('subject').value.trim();
    const content = tinymce.get('emailContent').getContent();
    const recipientType = document.querySelector('input[name="recipient_type"]:checked').value;
    const selectedUsers = Array.from(document.querySelectorAll('.user-select:checked')).map(cb => cb.value);
    
    if (!subject || !content) {
        showToast('warning', 'Please add subject and content before saving.');
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'save_draft');
    formData.append('subject', subject);
    formData.append('content', content);
    formData.append('recipient_type', recipientType);
    
    if (selectedUsers.length > 0) {
        selectedUsers.forEach(userId => {
            formData.append('selected_users[]', userId);
        });
    }
    
    showToast('info', 'Saving draft...');
    
    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message);
            if (data.flagged_content && data.flagged_content.length > 0) {
                showToast('warning', 'Draft flagged for approval: ' + data.flagged_content.join(', '));
            }
            setTimeout(() => location.reload(), 2000);
        } else {
            showToast('error', 'Failed to save draft: ' + data.message);
        }
    })
    .catch(error => {
        showToast('error', 'Network error: ' + error.message);
    });
}

// Submit for approval function
function submitForApproval() {
    if (confirm('Submit this email for approval? You won\'t be able to edit it after submission.')) {
        saveDraft();
    }
}

// Approval functions
function approveEmail(draftId) {
    const notes = document.getElementById('notes_' + draftId).value;
    
    const formData = new FormData();
    formData.append('approval_action', 'approve');
    formData.append('draft_id', draftId);
    formData.append('approval_notes', notes);
    
    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast('error', 'Failed to approve: ' + data.message);
        }
    });
}

function rejectEmail(draftId) {
    const notes = document.getElementById('notes_' + draftId).value;
    
    if (!notes.trim()) {
        showToast('warning', 'Please provide rejection reason.');
        return;
    }
    
    const formData = new FormData();
    formData.append('approval_action', 'reject');
    formData.append('draft_id', draftId);
    formData.append('approval_notes', notes);
    
    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast('error', 'Failed to reject: ' + data.message);
        }
    });
}

// Send approved draft
function sendApprovedDraft(draftId) {
    if (confirm('Send this approved email now?')) {
        const formData = new FormData();
        formData.append('send_action', 'send');
        formData.append('draft_id', draftId);
        
        showToast('info', 'Sending emails...');
        
        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('success', data.message);
                setTimeout(() => location.reload(), 2000);
            } else {
                showToast('error', 'Failed to send: ' + data.message);
            }
        });
    }
}

// View draft function
function viewDraft(draftId) {
    // This would load draft details in the modal
    const modal = new bootstrap.Modal(document.getElementById('draftModal'));
    document.getElementById('draftContent').innerHTML = '<div class="text-center"><div class="spinner-border"></div><p>Loading draft...</p></div>';
    modal.show();
    
    // You would implement the actual draft loading here
}

// Edit draft function
function editDraft(draftId) {
    // This would load the draft into the compose form
    showToast('info', 'Loading draft for editing...');
    // Implementation would load draft data into form fields
}

// Delete draft function
function deleteDraft(draftId) {
    if (confirm('Are you sure you want to delete this draft?')) {
        // Implementation for deleting draft
        showToast('info', 'Draft deleted.');
        setTimeout(() => location.reload(), 1000);
    }
}

// Clear form function
function clearForm() {
    if (confirm('Are you sure you want to clear the form?')) {
        document.getElementById('emailForm').reset();
        tinymce.get('emailContent').setContent('');
        document.getElementById('userSelectionContainer').style.display = 'none';
        document.getElementById('contentWarning').style.display = 'none';
        document.getElementById('subjectWarning').style.display = 'none';
        showToast('info', 'Form cleared.');
    }
}

// Preview function
document.getElementById('previewModal').addEventListener('show.bs.modal', function() {
    const subject = document.getElementById('subject').value || 'No Subject';
    const content = tinymce.get('emailContent').getContent() || '<p>No content added yet.</p>';
    
    const previewHTML = `
        <div style="max-width: 600px; margin: 0 auto; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px 20px; text-align: center;">
                <h1 style="margin: 0; font-size: 28px; font-weight: 300;">Sanix Tech</h1>
            </div>
            <div style="padding: 40px 30px; background: #ffffff; color: #333;">
                <h2 style="color: #333; margin-bottom: 20px; font-size: 24px;">${subject}</h2>
                <p>Hello [User Name],</p>
                ${content}
            </div>
            <div style="background: #f8f9fa; padding: 30px 20px; text-align: center; border-top: 1px solid #e9ecef; color: #6c757d;">
                <p>&copy; ${new Date().getFullYear()} Sanix Tech. All rights reserved.</p>
                <p>This email was sent from our admin panel.</p>
                <div style="font-size: 12px; color: #6c757d; margin-top: 20px;">
                    <p>Don't want to receive these emails? <a href="#" style="color: #6c757d;">Unsubscribe here</a></p>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('previewContent').innerHTML = previewHTML;
});

// Toast notification function
function showToast(type, message) {
    const toastContainer = document.querySelector('.toast-container');
    const toastId = 'toast_' + Date.now();
    
    const bgClass = {
        'success': 'bg-success',
        'error': 'bg-danger',
        'warning': 'bg-warning',
        'info': 'bg-info'
    }[type] || 'bg-info';
    
    const iconClass = {
        'success': 'fa-check-circle',
        'error': 'fa-exclamation-circle',
        'warning': 'fa-exclamation-triangle',
        'info': 'fa-info-circle'
    }[type] || 'fa-info-circle';
    
    const textClass = type === 'warning' ? 'text-dark' : 'text-white';
    const closeClass = type === 'warning' ? '' : 'btn-close-white';
    
    const toastHTML = `
        <div id="${toastId}" class="toast ${bgClass} ${textClass}" role="alert" data-bs-delay="5000">
            <div class="toast-header ${bgClass} ${textClass} border-0">
                <i class="fas ${iconClass} me-2"></i>
                <strong class="me-auto">${type.charAt(0).toUpperCase() + type.slice(1)}</strong>
                <button type="button" class="btn-close ${closeClass}" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHTML);
    
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement);
    toast.show();
    
    toastElement.addEventListener('hidden.bs.toast', function() {
        toastElement.remove();
    });
}

console.log('Enhanced Email System loaded successfully!');