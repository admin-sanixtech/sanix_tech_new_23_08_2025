// approve_user_quiz_questions_javascript.js

// Global variables
let currentReviewQuestionId = null;
let isLoading = false;

// Initialize when document is ready
document.addEventListener('DOMContentLoaded', function() {
    initializeBulkSelection();
    initializeFormHandlers();
    initializeAnimations();
    initializeKeyboardShortcuts();
    initializeAutoHideAlerts();
    initializeQuestionCardInteractions();
});

// Bulk selection functionality
function initializeBulkSelection() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const questionCheckboxes = document.querySelectorAll('.question-select');
    const selectedCountBadge = document.getElementById('selectedCount');
    const bulkApproveBtn = document.getElementById('bulkApprove');
    const bulkRejectBtn = document.getElementById('bulkReject');

    function updateSelectionCount() {
        const selectedCount = document.querySelectorAll('.question-select:checked').length;
        
        if (selectedCountBadge) {
            selectedCountBadge.textContent = `${selectedCount} selected`;
        }
        
        // Enable/disable bulk action buttons
        if (bulkApproveBtn) bulkApproveBtn.disabled = selectedCount === 0;
        if (bulkRejectBtn) bulkRejectBtn.disabled = selectedCount === 0;
        
        // Update select all checkbox state
        if (selectAllCheckbox) {
            if (selectedCount === 0) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = false;
            } else if (selectedCount === questionCheckboxes.length) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = true;
            } else {
                selectAllCheckbox.indeterminate = true;
            }
        }
        
        // Update card styling
        questionCheckboxes.forEach(checkbox => {
            const card = checkbox.closest('.question-card');
            if (card) {
                if (checkbox.checked) {
                    card.classList.add('selected');
                } else {
                    card.classList.remove('selected');
                }
            }
        });
    }

    // Select all functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            questionCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectionCount();
        });
    }

    // Individual checkbox functionality
    questionCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectionCount);
    });

    // Initial update
    updateSelectionCount();
}

// Form handlers
function initializeFormHandlers() {
    const bulkForm = document.getElementById('bulkForm');
    const approvalForm = document.getElementById('approvalForm');

    // Bulk form submission confirmation
    if (bulkForm) {
        bulkForm.addEventListener('submit', function(e) {
            const selectedCount = document.querySelectorAll('.question-select:checked').length;
            const action = e.submitter.value;
            
            if (selectedCount === 0) {
                e.preventDefault();
                showAlert('Please select at least one question.', 'warning');
                return;
            }
            
            const actionWord = action === 'approve' ? 'approve' : 'reject';
            if (!confirm(`Are you sure you want to ${actionWord} ${selectedCount} question(s)?`)) {
                e.preventDefault();
                return;
            }
            
            // Add loading state
            addLoadingState(e.submitter);
        });
    }

    // Individual approval form
    if (approvalForm) {
        approvalForm.addEventListener('submit', function(e) {
            const submitBtn = e.submitter;
            addLoadingState(submitBtn);
        });
    }
}

// Add loading state to buttons
function addLoadingState(button) {
    if (!button) return;
    
    button.disabled = true;
    const originalText = button.innerHTML;
    button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Processing...';
    button.classList.add('btn-loading');
    
    // Store original state for restoration
    button.dataset.originalText = originalText;
    
    // Fallback to re-enable after timeout
    setTimeout(() => {
        restoreButtonState(button);
    }, 10000);
}

// Restore button state
function restoreButtonState(button) {
    if (!button) return;
    
    button.disabled = false;
    if (button.dataset.originalText) {
        button.innerHTML = button.dataset.originalText;
        delete button.dataset.originalText;
    }
    button.classList.remove('btn-loading');
}

// Show custom alerts
function showAlert(message, type = 'info') {
    const alertContainer = document.querySelector('.container-fluid');
    if (!alertContainer) return;
    
    const alertClass = `alert-${type}`;
    const iconClass = {
        'success': 'fas fa-check-circle',
        'warning': 'fas fa-exclamation-triangle',
        'danger': 'fas fa-exclamation-circle',
        'info': 'fas fa-info-circle'
    }[type] || 'fas fa-info-circle';
    
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="${iconClass} me-2"></i>
            ${escapeHtml(message)}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    alertContainer.insertAdjacentHTML('afterbegin', alertHtml);
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        const alert = alertContainer.querySelector('.alert');
        if (alert && alert.classList.contains(`alert-${type}`)) {
            const bsAlert = bootstrap.Alert.getInstance(alert) || new bootstrap.Alert(alert);
            bsAlert.close();
        }
    }, 5000);
}

// Initialize animations
function initializeAnimations() {
    const questionCards = document.querySelectorAll('.question-card');
    questionCards.forEach((card, index) => {
        card.classList.add('card-animation');
        
        setTimeout(() => {
            card.classList.add('show');
        }, index * 50);
    });
}

// Keyboard shortcuts
function initializeKeyboardShortcuts() {
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + A to select all (when not in input field)
        if ((e.ctrlKey || e.metaKey) && e.key === 'a' && !e.target.matches('input, textarea')) {
            e.preventDefault();
            const selectAllCheckbox = document.getElementById('selectAll');
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = !selectAllCheckbox.checked;
                selectAllCheckbox.dispatchEvent(new Event('change'));
            }
        }
        
        // Escape to close modals
        if (e.key === 'Escape') {
            const openModals = document.querySelectorAll('.modal.show');
            openModals.forEach(modalElement => {
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) modal.hide();
            });
        }
    });
}

// Auto-hide alerts
function initializeAutoHideAlerts() {
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        if (alert.classList.contains('alert-success') || alert.classList.contains('alert-info')) {
            setTimeout(() => {
                const bsAlert = bootstrap.Alert.getInstance(alert);
                if (bsAlert) bsAlert.close();
            }, 5000);
        }
    });
}

// Question card interactions
function initializeQuestionCardInteractions() {
    const questionCards = document.querySelectorAll('.question-card');
    
    questionCards.forEach(card => {
        // Add hover effect for question preview
        const preview = card.querySelector('.question-preview');
        const expandBtn = card.querySelector('.expand-btn');
        
        if (preview && expandBtn) {
            expandBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                preview.classList.toggle('expanded');
                this.textContent = preview.classList.contains('expanded') ? 'Show Less' : 'Show More';
            });
        }
        
        // Add click to select functionality (excluding buttons and inputs)
        card.addEventListener('click', function(e) {
            if (e.target.matches('button, input, .btn, .btn *, .form-check-input')) return;
            
            const checkbox = this.querySelector('.question-select');
            if (checkbox) {
                checkbox.checked = !checkbox.checked;
                checkbox.dispatchEvent(new Event('change'));
            }
        });
    });
}

// Review modal functionality
function showReviewModal(questionId) {
    if (isLoading) return;
    
    currentReviewQuestionId = questionId;
    const modal = document.getElementById('reviewModal');
    const reviewContent = document.getElementById('reviewContent');
    
    if (!modal || !reviewContent) {
        showAlert('Review modal not found', 'error');
        return;
    }
    
    // Show loading state
    reviewContent.innerHTML = `
        <div class="text-center p-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3 text-muted">Loading question details...</p>
        </div>
    `;
    
    // Show modal
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
    
    // Fetch question details
    fetchQuestionDetails(questionId);
}

// Fetch question details via AJAX
function fetchQuestionDetails(questionId) {
    if (isLoading) return;
    
    isLoading = true;
    const formData = new FormData();
    formData.append('question_id', questionId);
    
    fetch('get_quiz_question_details.php', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            displayQuestionDetails(data.question);
            setupReviewModalActions(questionId);
        } else {
            showErrorInModal(data.error || 'Failed to load question details');
        }
    })
    .catch(error => {
        console.error('Error fetching question details:', error);
        showErrorInModal('Network error. Please try again.');
    })
    .finally(() => {
        isLoading = false;
    });
}

// Display question details in modal
function displayQuestionDetails(question) {
    const reviewContent = document.getElementById('reviewContent');
    if (!reviewContent) return;
    
    let optionsHtml = '';
    if (question.question_type === 'multiple_choice') {
        const options = {
            'A': { label: question.option_a || '', content: question.option_a_content || '' },
            'B': { label: question.option_b || '', content: question.option_b_content || '' },
            'C': { label: question.option_c || '', content: question.option_c_content || '' },
            'D': { label: question.option_d || '', content: question.option_d_content || '' }
        };
        
        optionsHtml = `
            <div class="mb-4">
                <h6 class="text-light mb-3">
                    <i class="fas fa-list me-2"></i>Answer Options:
                </h6>
                <div class="options-list">
                    ${Object.entries(options).map(([letter, option]) => {
                        const displayText = option.content || option.label;
                        if (!displayText) return '';
                        
                        const isCorrect = question.correct_answer === letter;
                        return `
                            <div class="option-item p-3 mb-2 rounded ${isCorrect ? 'bg-success bg-opacity-10 border border-success' : 'bg-dark bg-opacity-25'}">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-${isCorrect ? 'success' : 'secondary'} me-3">${letter}</span>
                                    <span class="flex-grow-1">${escapeHtml(displayText)}</span>
                                    ${isCorrect ? '<i class="fas fa-check-circle text-success ms-2" title="Correct Answer"></i>' : ''}
                                </div>
                            </div>
                        `;
                    }).join('')}
                </div>
            </div>
        `;
    } else {
        optionsHtml = `
            <div class="mb-4">
                <h6 class="text-light mb-2">
                    <i class="fas fa-check-circle me-2"></i>Correct Answer:
                </h6>
                <div class="bg-success bg-opacity-10 border border-success rounded p-3">
                    <span class="text-success fw-bold">${escapeHtml(question.correct_answer)}</span>
                </div>
            </div>
        `;
    }
    
    reviewContent.innerHTML = `
        <div class="question-details">
            <!-- Question Header -->
            <div class="d-flex justify-content-between align-items-start mb-4 p-3 bg-dark bg-opacity-25 rounded">
                <div>
                    <h5 class="text-light mb-1">Question #${question.pending_id}</h5>
                    <div class="d-flex gap-2">
                        <span class="badge bg-${getTypeColor(question.question_type)}">
                            ${question.question_type.replace('_', ' ').toUpperCase()}
                        </span>
                        <span class="badge bg-${getDifficultyColor(question.difficulty_level)}">
                            ${question.difficulty_level}
                        </span>
                    </div>
                </div>
                <div class="text-end">
                    <small class="text-muted d-block">
                        <i class="fas fa-user me-1"></i>${escapeHtml(question.creator_name)}
                    </small>
                    <small class="text-muted">
                        <i class="fas fa-calendar me-1"></i>${formatDate(question.created_at)}
                    </small>
                </div>
            </div>
            
            <!-- Category -->
            ${question.category_name ? `
                <div class="mb-4">
                    <h6 class="text-light mb-2">
                        <i class="fas fa-folder me-2"></i>Category:
                    </h6>
                    <div class="text-info">
                        <i class="fas fa-tag me-1"></i>
                        ${escapeHtml(question.category_name)}
                        ${question.subcategory_name ? ` <i class="fas fa-angle-right mx-1"></i> ${escapeHtml(question.subcategory_name)}` : ''}
                    </div>
                </div>
            ` : ''}
            
            <!-- Question Text -->
            <div class="mb-4">
                <h6 class="text-light mb-3">
                    <i class="fas fa-question-circle me-2"></i>Question:
                </h6>
                <div class="bg-dark bg-opacity-50 rounded p-3 border-start border-primary border-4">
                    <div class="text-light lh-base">${escapeHtml(question.question_text).replace(/\n/g, '<br>')}</div>
                </div>
            </div>
            
            <!-- Question Content (Additional context) -->
            ${question.question_content ? `
                <div class="mb-4">
                    <h6 class="text-light mb-3">
                        <i class="fas fa-file-text me-2"></i>Additional Content:
                    </h6>
                    <div class="bg-dark bg-opacity-25 rounded p-3">
                        <div class="text-light lh-base">${escapeHtml(question.question_content).replace(/\n/g, '<br>')}</div>
                    </div>
                </div>
            ` : ''}
            
            <!-- Code Snippet -->
            ${question.code_snippet ? `
                <div class="mb-4">
                    <h6 class="text-light mb-3">
                        <i class="fas fa-code me-2"></i>Code Snippet:
                    </h6>
                    <div class="code-snippet">
                        <pre class="text-light mb-0"><code>${escapeHtml(question.code_snippet)}</code></pre>
                    </div>
                </div>
            ` : ''}
            
            <!-- Options/Answer -->
            ${optionsHtml}
            
            <!-- Answer Content (Explanation) -->
            ${question.answer_content ? `
                <div class="mb-4">
                    <h6 class="text-light mb-3">
                        <i class="fas fa-lightbulb me-2"></i>Answer Explanation:
                    </h6>
                    <div class="bg-info bg-opacity-10 border border-info rounded p-3">
                        <div class="text-light lh-base">${escapeHtml(question.answer_content).replace(/\n/g, '<br>')}</div>
                    </div>
                </div>
            ` : ''}
            
            <!-- Description -->
            ${question.description ? `
                <div class="mb-4">
                    <h6 class="text-light mb-3">
                        <i class="fas fa-info-circle me-2"></i>Additional Description:
                    </h6>
                    <div class="bg-dark bg-opacity-25 rounded p-3">
                        <div class="text-light lh-base">${escapeHtml(question.description).replace(/\n/g, '<br>')}</div>
                    </div>
                </div>
            ` : ''}
        </div>
    `;
}

// Setup review modal action buttons
function setupReviewModalActions(questionId) {
    const reviewApprove = document.getElementById('reviewApprove');
    const reviewReject = document.getElementById('reviewReject');
    
    if (reviewApprove) {
        // Remove any existing event listeners
        const newReviewApprove = reviewApprove.cloneNode(true);
        reviewApprove.parentNode.replaceChild(newReviewApprove, reviewApprove);
        
        newReviewApprove.addEventListener('click', () => {
            const reviewModal = bootstrap.Modal.getInstance(document.getElementById('reviewModal'));
            if (reviewModal) {
                reviewModal.hide();
                setTimeout(() => showApprovalModal(questionId, 'approve'), 300);
            }
        });
    }
    
    if (reviewReject) {
        // Remove any existing event listeners
        const newReviewReject = reviewReject.cloneNode(true);
        reviewReject.parentNode.replaceChild(newReviewReject, reviewReject);
        
        newReviewReject.addEventListener('click', () => {
            const reviewModal = bootstrap.Modal.getInstance(document.getElementById('reviewModal'));
            if (reviewModal) {
                reviewModal.hide();
                setTimeout(() => showApprovalModal(questionId, 'reject'), 300);
            }
        });
    }
}

// Show error in modal
function showErrorInModal(errorMessage) {
    const reviewContent = document.getElementById('reviewContent');
    if (!reviewContent) return;
    
    reviewContent.innerHTML = `
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Error:</strong> ${escapeHtml(errorMessage)}
        </div>
        <div class="text-center">
            <button type="button" class="btn btn-outline-primary" onclick="retryFetchQuestion()">
                <i class="fas fa-redo me-1"></i>Retry
            </button>
        </div>
    `;
}

// Retry fetching question details
function retryFetchQuestion() {
    if (currentReviewQuestionId) {
        fetchQuestionDetails(currentReviewQuestionId);
    }
}

// Individual approval/rejection modal
function showApprovalModal(questionId, action) {
    const modal = document.getElementById('approvalModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalMessage = document.getElementById('modalMessage');
    const modalQuestionId = document.getElementById('modalQuestionId');
    const modalAction = document.getElementById('modalAction');
    const modalConfirmBtn = document.getElementById('modalConfirmBtn');
    const modalNotes = document.getElementById('modalNotes');

    if (!modal) {
        showAlert('Approval modal not found', 'error');
        return;
    }

    // Set form values
    if (modalQuestionId) modalQuestionId.value = questionId;
    if (modalAction) modalAction.value = action;

    // Update modal content based on action
    if (action === 'approve') {
        if (modalTitle) modalTitle.textContent = 'Approve Question';
        if (modalMessage) modalMessage.textContent = `Are you sure you want to approve question #${questionId}?`;
        if (modalConfirmBtn) {
            modalConfirmBtn.textContent = 'Approve Question';
            modalConfirmBtn.className = 'btn btn-approve';
        }
    } else {
        if (modalTitle) modalTitle.textContent = 'Reject Question';
        if (modalMessage) modalMessage.textContent = `Are you sure you want to reject question #${questionId}?`;
        if (modalConfirmBtn) {
            modalConfirmBtn.textContent = 'Reject Question';
            modalConfirmBtn.className = 'btn btn-reject';
        }
    }

    // Clear previous notes
    if (modalNotes) modalNotes.value = '';

    // Show modal
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
}

// Utility functions
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDate(dateString) {
    if (!dateString) return 'Unknown';
    
    try {
        return new Date(dateString).toLocaleString('en-US', {
            year: 'numeric',
            month: 'short',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        });
    } catch (e) {
        return dateString;
    }
}

function getTypeColor(type) {
    const colors = {
        'multiple_choice': 'primary',
        'true_false': 'info',
        'short_answer': 'success',
        'code': 'warning',
        'essay': 'secondary'
    };
    return colors[type] || 'secondary';
}

function getDifficultyColor(difficulty) {
    const colors = {
        'Beginner': 'success',
        'Intermediate': 'warning',
        'Advanced': 'danger',
        'Expert': 'dark'
    };
    return colors[difficulty] || 'secondary';
}

// Global functions for inline event handlers (maintain backwards compatibility)
window.showReviewModal = showReviewModal;
window.showApprovalModal = showApprovalModal;
window.retryFetchQuestion = retryFetchQuestion;

// Error handling for unhandled promises
window.addEventListener('unhandledrejection', function(event) {
    console.error('Unhandled promise rejection:', event.reason);
    showAlert('An unexpected error occurred. Please refresh the page.', 'error');
});

// Handle page visibility changes to prevent issues when tab is hidden
document.addEventListener('visibilitychange', function() {
    if (document.visibilityState === 'visible' && isLoading) {
        // Reset loading state if page becomes visible and we're stuck in loading
        setTimeout(() => {
            if (isLoading) {
                isLoading = false;
                console.warn('Reset loading state due to page visibility change');
            }
        }, 1000);
    }
});