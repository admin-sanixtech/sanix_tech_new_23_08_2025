/**
 * Enhanced Job Portal JavaScript
 * Handles all interactive functionality for the job portal
 */

// Global variables
let currentJobId = null;
let currentJobEmail = null;
let searchTimeout = null;
let isLoading = false;

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeFilters();
    setupEventListeners();
    animateJobCards();
    initializeTooltips();
});

/**
 * Initialize filter states from URL parameters
 */
function initializeFilters() {
    const urlParams = new URLSearchParams(window.location.search);
    
    // Set filter values from URL
    const cityParam = urlParams.get('city');
    const roleParam = urlParams.get('role');
    const jobTypeParam = urlParams.get('job_type');
    const postedParam = urlParams.get('posted');
    const searchParam = urlParams.get('search');
    
    if (cityParam) document.getElementById('cityFilter').value = cityParam;
    if (roleParam) document.getElementById('roleFilter').value = roleParam;
    if (jobTypeParam) document.getElementById('jobTypeFilter').value = jobTypeParam;
    if (postedParam) document.getElementById('postedFilter').value = postedParam;
    if (searchParam) document.getElementById('searchInput').value = searchParam;
    
    // Update filter chips active state
    updateFilterChipsState();
}

/**
 * Setup all event listeners
 */
function setupEventListeners() {
    // Real-time search with debouncing
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (this.value.length >= 3 || this.value.length === 0) {
                    submitForm();
                }
            }, 500);
        });
    }
    
    // Handle form submission
    const filterForm = document.getElementById('filterForm');
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitForm();
        });
    }
    
    // Auto-refresh latest jobs every 2 minutes
    setInterval(refreshLatestJobs, 120000);
    
    // Add smooth scrolling for all anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
}

/**
 * Submit form with current filter values
 */
function submitForm() {
    if (isLoading) return;
    
    showLoadingState();
    
    // Reset page to 1 when filters change
    document.getElementById('pageHidden').value = '1';
    
    const form = document.getElementById('filterForm');
    form.submit();
}

/**
 * Apply quick filter
 */
function applyQuickFilter(filterType, filterValue) {
    // Clear conflicting filters
    if (filterType === 'job_type') {
        document.getElementById('jobTypeFilter').value = filterValue;
        // Clear work mode if job type is selected
        clearWorkModeFilters();
    } else if (filterType === 'work_mode') {
        document.getElementById('workModeHidden').value = filterValue;
        // Update radio buttons
        const workModeRadios = document.querySelectorAll('input[name="workMode"]');
        workModeRadios.forEach(radio => {
            radio.checked = radio.value === filterValue;
        });
    } else if (filterType === 'experience') {
        document.getElementById('experienceHidden').value = filterValue;
        // Update radio buttons
        const expRadios = document.querySelectorAll('input[name="experienceLevel"]');
        expRadios.forEach(radio => {
            radio.checked = radio.value === filterValue;
        });
    }
    
    updateFilterChipsState();
    submitForm();
}

/**
 * Clear all filters
 */
function clearAllFilters() {
    // Reset all form elements
    document.getElementById('cityFilter').value = '';
    document.getElementById('roleFilter').value = '';
    document.getElementById('jobTypeFilter').value = '';
    document.getElementById('postedFilter').value = '';
    document.getElementById('searchInput').value = '';
    
    // Reset hidden inputs
    document.getElementById('companySizeHidden').value = '';
    document.getElementById('experienceHidden').value = '';
    document.getElementById('salaryHidden').value = '';
    document.getElementById('workModeHidden').value = '';
    document.getElementById('pageHidden').value = '1';
    
    // Reset checkboxes and radio buttons
    const checkboxes = document.querySelectorAll('input[type="checkbox"], input[type="radio"]');
    checkboxes.forEach(cb => cb.checked = false);
    
    updateFilterChipsState();
    
    // Redirect to clean URL
    window.location.href = window.location.pathname;
}

/**
 * Apply experience filter
 */
function applyExperienceFilter(experience) {
    document.getElementById('experienceHidden').value = experience;
    updateFilterChipsState();
    submitForm();
}

/**
 * Apply work mode filter
 */
function applyWorkModeFilter(workMode) {
    document.getElementById('workModeHidden').value = workMode;
    updateFilterChipsState();
    submitForm();
}

/**
 * Clear work mode filters
 */
function clearWorkModeFilters() {
    document.getElementById('workModeHidden').value = '';
    const workModeRadios = document.querySelectorAll('input[name="workMode"]');
    workModeRadios.forEach(radio => radio.checked = false);
}

/**
 * Filter by specific role
 */
function filterByRole(role) {
    document.getElementById('roleFilter').value = role;
    updateFilterChipsState();
    submitForm();
}

/**
 * Update filter chips active state
 */
function updateFilterChipsState() {
    const chips = document.querySelectorAll('.filter-chip');
    chips.forEach(chip => {
        chip.classList.remove('active');
    });
    
    // Check current filter values and activate corresponding chips
    const jobType = document.getElementById('jobTypeFilter').value;
    const workMode = document.getElementById('workModeHidden').value;
    const experience = document.getElementById('experienceHidden').value;
    
    if (jobType === 'fulltime') {
        document.querySelector('.filter-chip[onclick*="fulltime"]')?.classList.add('active');
    }
    if (jobType === 'parttime') {
        document.querySelector('.filter-chip[onclick*="parttime"]')?.classList.add('active');
    }
    if (workMode === 'remote') {
        document.querySelector('.filter-chip[onclick*="remote"]')?.classList.add('active');
    }
    if (experience === 'fresher') {
        document.querySelector('.filter-chip[onclick*="fresher"]')?.classList.add('active');
    }
    if (experience === 'senior') {
        document.querySelector('.filter-chip[onclick*="senior"]')?.classList.add('active');
    }
}

/**
 * View job details in modal
 */
function viewJobDetails(jobId) {
    currentJobId = jobId;
    
    // Show loading in modal
    const modalBody = document.getElementById('jobModalBody');
    modalBody.innerHTML = `
        <div class="text-center p-5">
            <div class="spinner mx-auto mb-3"></div>
            <p>Loading job details...</p>
        </div>
    `;
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('jobModal'));
    modal.show();
    
    // Fetch job details via AJAX
    fetch(`get_job_details.php?id=${jobId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayJobDetails(data.job);
            } else {
                modalBody.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Error loading job details. Please try again.
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            modalBody.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Network error. Please check your connection and try again.
                </div>
            `;
        });
}

/**
 * Display job details in modal
 */
function displayJobDetails(job) {
    const modalBody = document.getElementById('jobModalBody');
    
    modalBody.innerHTML = `
        <div class="job-detail-header mb-4">
            <h4 class="text-primary">${job.title}</h4>
            <h6 class="text-muted">${job.company || 'Sanix Technologies'}</h6>
            <div class="job-meta mt-3">
                <div class="meta-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>${job.location}</span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-briefcase"></i>
                    <span>${job.role}</span>
                </div>
                ${job.salary_range ? `
                <div class="meta-item">
                    <i class="fas fa-rupee-sign"></i>
                    <span>${job.salary_range}</span>
                </div>
                ` : ''}
                <div class="meta-item">
                    <i class="fas fa-calendar"></i>
                    <span>Posted: ${job.formatted_date}</span>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <h6 class="fw-bold">Job Description</h6>
            <div style="white-space: pre-line;">${job.description}</div>
        </div>

        ${job.requirements && job.requirements.length > 0 ? `
        <div class="mb-4">
            <h6 class="fw-bold">Requirements</h6>
            <ul class="list-unstyled">
                ${job.requirements.map(req => `<li class="mb-2"><i class="fas fa-check text-success me-2"></i>${req}</li>`).join('')}
            </ul>
        </div>
        ` : ''}

        ${job.technologies && job.technologies.length > 0 ? `
        <div class="mb-4">
            <h6 class="fw-bold">Technologies</h6>
            <div>
                ${job.technologies.map(tech => `<span class="tech-tag">${tech}</span>`).join('')}
            </div>
        </div>
        ` : ''}

        <div class="alert alert-info">
            <i class="fas fa-envelope me-2"></i>
            <strong>Contact:</strong> ${job.email}
        </div>
    `;
    
    // Update apply button
    const applyBtn = document.getElementById('modalApplyBtn');
    applyBtn.onclick = () => applyFromModal(job.id, job.email);
}

/**
 * Apply for job
 */
function applyNow(jobId, jobEmail) {
    currentJobId = jobId;
    currentJobEmail = jobEmail;
    
    // Find job data
    const job = jobsData.find(j => j.id == jobId);
    const jobTitle = job ? job.title : 'this position';
    
    document.getElementById('applyModalLabel').textContent = `Apply for ${jobTitle}`;
    document.getElementById('applyJobId').value = jobId;
    document.getElementById('applyJobEmail').value = jobEmail;
    
    const modal = new bootstrap.Modal(document.getElementById('applyModal'));
    modal.show();
}

/**
 * Apply from job details modal
 */
function applyFromModal(jobId, jobEmail) {
    const jobModal = bootstrap.Modal.getInstance(document.getElementById('jobModal'));
    jobModal.hide();
    
    setTimeout(() => {
        applyNow(jobId, jobEmail);
    }, 300);
}

/**
 * Submit job application
 */
function submitApplication() {
    const form = document.getElementById('applyForm');
    
    if (form.checkValidity()) {
        // Show loading state
        const submitBtn = document.querySelector('#applyModal .btn-primary');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
        submitBtn.disabled = true;
        
        // Create FormData object
        const formData = new FormData(form);
        
        // Submit via AJAX
        fetch('apply_job.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showNotification('Application submitted successfully! We will contact you soon.', 'success');
                
                // Close modal and reset form
                const modal = bootstrap.Modal.getInstance(document.getElementById('applyModal'));
                modal.hide();
                form.reset();
            } else {
                showNotification('Error submitting application: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Network error. Please try again.', 'error');
        })
        .finally(() => {
            // Restore button state
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    } else {
        form.reportValidity();
    }
}

/**
 * Toggle favorite status
 */
function toggleFavorite(element) {
    if (element.classList.contains('far')) {
        element.classList.remove('far');
        element.classList.add('fas', 'favorited');
        element.style.color = '#ef4444';
        
        // Add to favorites (you can implement localStorage or send to backend)
        const jobCard = element.closest('.job-card');
        const jobId = jobCard.dataset.jobId;
        addToFavorites(jobId);
        
        showNotification('Added to favorites!', 'success');
    } else {
        element.classList.remove('fas', 'favorited');
        element.classList.add('far');
        element.style.color = '#6c757d';
        
        // Remove from favorites
        const jobCard = element.closest('.job-card');
        const jobId = jobCard.dataset.jobId;
        removeFromFavorites(jobId);
        
        showNotification('Removed from favorites', 'info');
    }
}

/**
 * Add job to favorites
 */
function addToFavorites(jobId) {
    // In real implementation, you might want to save to database
    let favorites = JSON.parse(sessionStorage.getItem('favoriteJobs') || '[]');
    if (!favorites.includes(jobId)) {
        favorites.push(jobId);
        sessionStorage.setItem('favoriteJobs', JSON.stringify(favorites));
    }
}

/**
 * Remove job from favorites
 */
function removeFromFavorites(jobId) {
    let favorites = JSON.parse(sessionStorage.getItem('favoriteJobs') || '[]');
    favorites = favorites.filter(id => id !== jobId);
    sessionStorage.setItem('favoriteJobs', JSON.stringify(favorites));
}

/**
 * Toggle view between card and list
 */
function toggleView(viewType) {
    const buttons = document.querySelectorAll('.btn-group .btn');
    buttons.forEach(btn => btn.classList.remove('active'));
    
    const jobListings = document.getElementById('jobListings');
    
    if (viewType === 'card') {
        buttons[0].classList.add('active');
        jobListings.classList.remove('list-view');
        jobListings.classList.add('card-view');
    } else {
        buttons[1].classList.add('active');
        jobListings.classList.remove('card-view');
        jobListings.classList.add('list-view');
        
        // Apply list view styles
        const jobCards = document.querySelectorAll('.job-card');
        jobCards.forEach(card => {
            card.style.marginBottom = '0.5rem';
        });
    }
}

/**
 * Show loading state
 */
function showLoadingState() {
    isLoading = true;
    const jobListings = document.getElementById('jobListings');
    
    // Add loading overlay
    const loadingOverlay = document.createElement('div');
    loadingOverlay.className = 'loading-overlay';
    loadingOverlay.innerHTML = `
        <div>
            <div class="spinner mx-auto mb-3"></div>
            <p class="text-muted">Loading jobs...</p>
        </div>
    `;
    
    jobListings.style.position = 'relative';
    jobListings.appendChild(loadingOverlay);
}

/**
 * Hide loading state
 */
function hideLoadingState() {
    isLoading = false;
    const loadingOverlay = document.querySelector('.loading-overlay');
    if (loadingOverlay) {
        loadingOverlay.remove();
    }
}

/**
 * Animate job cards on load
 */
function animateJobCards() {
    const jobCards = document.querySelectorAll('.job-card');
    
    jobCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

/**
 * Initialize tooltips
 */
function initializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

/**
 * Show notification
 */
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification-toast');
    existingNotifications.forEach(n => n.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification-toast alert alert-${type === 'error' ? 'danger' : type} alert-dismissible`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        border-radius: 12px;
        box-shadow: var(--hover-shadow);
        animation: slideInRight 0.5s ease;
    `;
    
    notification.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
            <span>${message}</span>
        </div>
        <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.style.animation = 'fadeOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }
    }, 5000);
}

/**
 * Refresh latest jobs in sidebar
 */
function refreshLatestJobs() {
    fetch('get_latest_jobs.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateLatestJobsSidebar(data.jobs);
            }
        })
        .catch(error => {
            console.error('Error refreshing latest jobs:', error);
        });
}

/**
 * Update latest jobs sidebar
 */
function updateLatestJobsSidebar(jobs) {
    const latestJobsContainer = document.getElementById('latestJobs');
    
    latestJobsContainer.innerHTML = jobs.map(job => `
        <div class="trending-job" onclick="viewJobDetails(${job.id})">
            <h6 class="mb-1">${job.title}</h6>
            <small class="text-muted">${job.role} • ${job.location}</small>
            <div class="d-flex justify-content-between mt-2">
                <small class="text-primary">${job.salary_range || 'Salary not disclosed'}</small>
                <small class="text-muted">${job.time_ago}</small>
            </div>
        </div>
    `).join('');
}

/**
 * View all jobs (clear filters)
 */
function viewAllJobs() {
    clearAllFilters();
}

/**
 * Handle pagination
 */
function changePage(page) {
    document.getElementById('pageHidden').value = page;
    showLoadingState();
    
    // Scroll to top of job listings
    const jobListings = document.getElementById('jobListings');
    jobListings.scrollIntoView({ behavior: 'smooth', block: 'start' });
    
    // Submit form with new page
    setTimeout(() => {
        submitForm();
    }, 500);
}

/**
 * Share job on social media
 */
function shareJob(jobId, platform) {
    const job = jobsData.find(j => j.id == jobId);
    if (!job) return;
    
    const url = encodeURIComponent(window.location.origin + '/job-details.php?id=' + jobId);
    const text = encodeURIComponent(`Check out this job: ${job.title} at ${job.company}`);
    
    let shareUrl = '';
    
    switch(platform) {
        case 'linkedin':
            shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${url}`;
            break;
        case 'twitter':
            shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${text}`;
            break;
        case 'whatsapp':
            shareUrl = `https://wa.me/?text=${text}%20${url}`;
            break;
        case 'email':
            shareUrl = `mailto:?subject=${encodeURIComponent(job.title + ' - Job Opportunity')}&body=${text}%20${url}`;
            break;
    }
    
    if (shareUrl) {
        window.open(shareUrl, '_blank', 'width=600,height=400');
    }
}

/**
 * Copy job link to clipboard
 */
function copyJobLink(jobId) {
    const url = window.location.origin + '/job-details.php?id=' + jobId;
    
    navigator.clipboard.writeText(url).then(() => {
        showNotification('Job link copied to clipboard!', 'success');
    }).catch(() => {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = url;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showNotification('Job link copied to clipboard!', 'success');
    });
}

/**
 * Advanced search functionality
 */
function advancedSearch() {
    const searchTerm = document.getElementById('searchInput').value;
    
    if (searchTerm.length < 2) {
        showNotification('Please enter at least 2 characters to search', 'warning');
        return;
    }
    
    submitForm();
}

/**
 * Save search preferences
 */
function saveSearchPreferences() {
    const preferences = {
        city: document.getElementById('cityFilter').value,
        role: document.getElementById('roleFilter').value,
        jobType: document.getElementById('jobTypeFilter').value,
        experience: document.getElementById('experienceHidden').value,
        workMode: document.getElementById('workModeHidden').value
    };
    
    sessionStorage.setItem('jobSearchPreferences', JSON.stringify(preferences));
    showNotification('Search preferences saved!', 'success');
}

/**
 * Load saved search preferences
 */
function loadSearchPreferences() {
    const preferences = JSON.parse(sessionStorage.getItem('jobSearchPreferences') || '{}');
    
    if (Object.keys(preferences).length > 0) {
        document.getElementById('cityFilter').value = preferences.city || '';
        document.getElementById('roleFilter').value = preferences.role || '';
        document.getElementById('jobTypeFilter').value = preferences.jobType || '';
        document.getElementById('experienceHidden').value = preferences.experience || '';
        document.getElementById('workModeHidden').value = preferences.workMode || '';
        
        // Update radio buttons
        if (preferences.experience) {
            const expRadio = document.getElementById(preferences.experience);
            if (expRadio) expRadio.checked = true;
        }
        
        if (preferences.workMode) {
            const workRadio = document.getElementById(preferences.workMode === 'remote' ? 'wfh' : preferences.workMode);
            if (workRadio) workRadio.checked = true;
        }
        
        updateFilterChipsState();
        showNotification('Search preferences loaded!', 'info');
    }
}

/**
 * Export job listings to CSV
 */
function exportJobs() {
    const csvContent = generateCSV();
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    
    const a = document.createElement('a');
    a.href = url;
    a.download = 'job_listings_' + new Date().toISOString().split('T')[0] + '.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
    
    showNotification('Job listings exported successfully!', 'success');
}

/**
 * Generate CSV content
 */
function generateCSV() {
    const headers = ['Title', 'Company', 'Location', 'Role', 'Posted Date', 'Email'];
    const rows = jobsData.map(job => [
        `"${job.title}"`,
        `"${job.company}"`,
        `"${job.location}"`,
        `"${job.role}"`,
        `"${job.formatted_date}"`,
        `"${job.email}"`
    ]);
    
    return [headers.join(','), ...rows.map(row => row.join(','))].join('\n');
}

/**
 * Handle keyboard shortcuts
 */
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + K for search focus
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        document.getElementById('searchInput').focus();
    }
    
    // Escape to close modals
    if (e.key === 'Escape') {
        const openModals = document.querySelectorAll('.modal.show');
        openModals.forEach(modal => {
            const modalInstance = bootstrap.Modal.getInstance(modal);
            if (modalInstance) modalInstance.hide();
        });
    }
});

/**
 * Infinite scroll for job listings (optional feature)
 */
function enableInfiniteScroll() {
    let loading = false;
    
    window.addEventListener('scroll', () => {
        if (loading) return;
        
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const windowHeight = window.innerHeight;
        const docHeight = document.documentElement.offsetHeight;
        
        if (scrollTop + windowHeight >= docHeight - 1000) {
            loading = true;
            loadMoreJobs();
        }
    });
}

/**
 * Load more jobs for infinite scroll
 */
function loadMoreJobs() {
    // This would make an AJAX call to load more jobs
    // For now, just a placeholder
    console.log('Loading more jobs...');
}

/**
 * Initialize job card animations
 */
function initializeJobCardAnimations() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
            }
        });
    }, { threshold: 0.1 });
    
    document.querySelectorAll('.job-card').forEach(card => {
        observer.observe(card);
    });
}

/**
 * Format salary display
 */
function formatSalary(min, max) {
    if (!min && !max) return 'Salary not disclosed';
    if (min && max) return `₹${min}-${max} LPA`;
    if (min) return `₹${min}+ LPA`;
    return 'Competitive salary';
}

/**
 * Calculate reading time for job description
 */
function calculateReadingTime(text) {
    const wordsPerMinute = 200;
    const words = text.trim().split(/\s+/).length;
    const minutes = Math.ceil(words / wordsPerMinute);
    return minutes === 1 ? '1 min read' : `${minutes} min read`;
}

/**
 * Validate form inputs
 */
function validateForm(formId) {
    const form = document.getElementById(formId);
    const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        const value = input.value.trim();
        const errorElement = input.parentElement.querySelector('.invalid-feedback');
        
        // Remove existing error messages
        if (errorElement) errorElement.remove();
        
        // Email validation
        if (input.type === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                showFieldError(input, 'Please enter a valid email address');
                isValid = false;
            }
        }
        
        // Phone validation
        if (input.type === 'tel' && value) {
            const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
            if (!phoneRegex.test(value.replace(/[\s\-\(\)]/g, ''))) {
                showFieldError(input, 'Please enter a valid phone number');
                isValid = false;
            }
        }
        
        // File validation
        if (input.type === 'file' && input.files.length > 0) {
            const file = input.files[0];
            const maxSize = 5 * 1024 * 1024; // 5MB
            const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            
            if (file.size > maxSize) {
                showFieldError(input, 'File size must be less than 5MB');
                isValid = false;
            }
            
            if (!allowedTypes.includes(file.type)) {
                showFieldError(input, 'Only PDF, DOC, and DOCX files are allowed');
                isValid = false;
            }
        }
        
        // Required field validation
        if (input.hasAttribute('required') && !value) {
            showFieldError(input, 'This field is required');
            isValid = false;
        }
    });
    
    return isValid;
}

/**
 * Show field error
 */
function showFieldError(input, message) {
    input.classList.add('is-invalid');
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'invalid-feedback';
    errorDiv.textContent = message;
    
    input.parentElement.appendChild(errorDiv);
}

/**
 * Clear field errors
 */
function clearFieldErrors(formId) {
    const form = document.getElementById(formId);
    const invalidInputs = form.querySelectorAll('.is-invalid');
    const errorMessages = form.querySelectorAll('.invalid-feedback');
    
    invalidInputs.forEach(input => input.classList.remove('is-invalid'));
    errorMessages.forEach(error => error.remove());
}

/**
 * Auto-save form data
 */
function autoSaveFormData(formId) {
    const form = document.getElementById(formId);
    const inputs = form.querySelectorAll('input, textarea, select');
    
    inputs.forEach(input => {
        input.addEventListener('input', () => {
            const formData = new FormData(form);
            const dataObject = {};
            
            for (let [key, value] of formData.entries()) {
                dataObject[key] = value;
            }
            
            sessionStorage.setItem(`formData_${formId}`, JSON.stringify(dataObject));
        });
    });
}

/**
 * Restore form data
 */
function restoreFormData(formId) {
    const savedData = sessionStorage.getItem(`formData_${formId}`);
    if (!savedData) return;
    
    const data = JSON.parse(savedData);
    const form = document.getElementById(formId);
    
    Object.keys(data).forEach(key => {
        const input = form.querySelector(`[name="${key}"]`);
        if (input && input.type !== 'file') {
            input.value = data[key];
        }
    });
}

/**
 * Track user interactions for analytics
 */
function trackEvent(eventName, properties = {}) {
    // This would integrate with your analytics service
    console.log('Event tracked:', eventName, properties);
    
    // Example: Send to Google Analytics
    if (typeof gtag !== 'undefined') {
        gtag('event', eventName, properties);
    }
}

/**
 * Initialize page with all features
 */
function initializePage() {
    initializeFilters();
    setupEventListeners();
    animateJobCards();
    initializeTooltips();
    initializeJobCardAnimations();
    
    // Auto-save application form data
    autoSaveFormData('applyForm');
    
    // Restore any saved form data
    restoreFormData('applyForm');
    
    // Track page view
    trackEvent('job_portal_page_view', {
        total_jobs: totalJobs,
        current_page: currentPage,
        filters_applied: Object.values(currentFilters).filter(v => v && v.length > 0).length
    });
}

/**
 * Enhanced error handling
 */
window.addEventListener('error', function(e) {
    console.error('JavaScript error:', e.error);
    showNotification('An unexpected error occurred. Please refresh the page.', 'error');
});

/**
 * Performance monitoring
 */
function measurePerformance() {
    if ('performance' in window) {
        window.addEventListener('load', () => {
            const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
            console.log('Page load time:', loadTime + 'ms');
            
            // Track performance
            trackEvent('page_performance', {
                load_time: loadTime,
                page_type: 'job_portal'
            });
        });
    }
}

// Initialize everything when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializePage);
} else {
    initializePage();
}

// Measure performance
measurePerformance();

// Add CSS animations dynamically
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeOut {
        from { opacity: 1; transform: translateX(0); }
        to { opacity: 0; transform: translateX(100%); }
    }
    
    .notification-toast {
        animation: slideInRight 0.5s ease !important;
    }
    
    .job-card.loading {
        opacity: 0.6;
        pointer-events: none;
    }
    
    .filter-chip.loading {
        opacity: 0.6;
        pointer-events: none;
    }
`;
document.head.appendChild(style);
