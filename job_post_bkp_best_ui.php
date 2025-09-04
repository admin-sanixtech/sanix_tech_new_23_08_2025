<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sanix Technologies - Job Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #6366f1;
            --secondary-color: #8b5cf6;
            --accent-color: #06b6d4;
            --gradient-bg: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --card-shadow: 0 10px 25px rgba(0,0,0,0.1);
            --hover-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.6;
        }

        .header-banner {
            background: var(--gradient-bg);
            color: white;
            padding: 2rem 0;
            position: relative;
            overflow: hidden;
        }

        .header-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 20"><defs><radialGradient id="a" cx="50%" cy="0%" r="100%"><stop offset="0%" stop-color="rgba(255,255,255,0.1)"/><stop offset="100%" stop-color="rgba(255,255,255,0)"/></radialGradient></defs><rect width="100" height="20" fill="url(%23a)"/></svg>');
            animation: shimmer 3s ease-in-out infinite;
        }

        @keyframes shimmer {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 0.8; }
        }

        .filter-section {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            margin-top: -40px;
            position: relative;
            z-index: 10;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .filter-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }

        .filter-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--hover-shadow);
        }

        .form-select, .form-control {
            border-radius: 12px;
            border: 2px solid #e5e7eb;
            transition: all 0.3s ease;
        }

        .form-select:focus, .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .sidebar {
            background: white;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            position: sticky;
            top: 20px;
        }

        .sidebar-header {
            background: var(--gradient-bg);
            color: white;
            border-radius: 20px 20px 0 0;
            padding: 1.5rem;
        }

        .filter-group {
            padding: 1.5rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .filter-group:last-child {
            border-bottom: none;
        }

        .job-card {
            background: white;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            border: none;
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .job-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
        }

        .job-card-header {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 1.5rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .job-title {
            color: var(--primary-color);
            font-weight: 700;
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
        }

        .company-badge {
            background: var(--gradient-bg);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .job-meta {
            display: flex;
            gap: 1rem;
            margin: 1rem 0;
            flex-wrap: wrap;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #64748b;
            font-size: 0.875rem;
        }

        .meta-item i {
            color: var(--accent-color);
        }

        .btn-primary {
            background: var(--gradient-bg);
            border: none;
            border-radius: 12px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.3);
        }

        .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .pagination .page-link {
            border-radius: 12px;
            margin: 0 0.25rem;
            border: none;
            color: var(--primary-color);
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .pagination .page-item.active .page-link {
            background: var(--gradient-bg);
            border: none;
        }

        .sidebar-jobs {
            background: white;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            position: sticky;
            top: 20px;
        }

        .trending-job {
            padding: 1rem;
            border-bottom: 1px solid #f1f5f9;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .trending-job:hover {
            background: #f8fafc;
            transform: translateX(5px);
        }

        .trending-job:last-child {
            border-bottom: none;
        }

        .range-slider {
            width: 100%;
            margin: 1rem 0;
        }

        .filter-chip {
            display: inline-block;
            background: var(--primary-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            margin: 0.25rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-chip:hover {
            background: var(--secondary-color);
            transform: scale(1.05);
        }

        .search-bar {
            border-radius: 25px;
            padding: 1rem 1.5rem;
            border: 2px solid #e5e7eb;
            font-size: 1.1rem;
        }

        .search-btn {
            border-radius: 25px;
            padding: 1rem 2rem;
            background: var(--gradient-bg);
            border: none;
            color: white;
            font-weight: 600;
        }

        .job-stats {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            display: block;
        }

        .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        }

        .modal-header {
            background: var(--gradient-bg);
            color: white;
            border-radius: 20px 20px 0 0;
        }

        .learning-zone {
            background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .tech-tag {
            background: rgba(99, 102, 241, 0.1);
            color: var(--primary-color);
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            margin: 0.25rem;
            display: inline-block;
        }

        @media (max-width: 768px) {
            .sidebar {
                margin-top: 2rem;
            }
            
            .job-meta {
                flex-direction: column;
                gap: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header Banner -->
    <div class="header-banner">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-5 fw-bold mb-2">Find Your Dream Job</h1>
                    <p class="lead mb-0">Discover amazing opportunities at Sanix Technologies</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="job-stats">
                        <div class="row">
                            <div class="col-6 stat-item">
                                <span class="stat-number" id="totalJobs">1,247</span>
                                <small>Active Jobs</small>
                            </div>
                            <div class="col-6 stat-item">
                                <span class="stat-number">89</span>
                                <small>Companies</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Search & Filter Section -->
    <div class="container-fluid px-4">
        <div class="filter-section p-4 mb-4">
            <!-- Search Bar -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="input-group">
                        <input type="text" class="form-control search-bar" placeholder="Search jobs, companies, keywords..." id="searchInput">
                        <button class="btn search-btn" onclick="searchJobs()">
                            <i class="fas fa-search me-2"></i>Search
                        </button>
                    </div>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-outline-primary w-100" onclick="clearAllFilters()">
                        <i class="fas fa-refresh me-2"></i>Clear Filters
                    </button>
                </div>
            </div>

            <!-- Quick Filters -->
            <div class="row mb-3">
                <div class="col-12">
                    <h6 class="mb-3">Quick Filters:</h6>
                    <div class="filter-chips">
                        <span class="filter-chip" onclick="applyQuickFilter('remote')">Remote Jobs</span>
                        <span class="filter-chip" onclick="applyQuickFilter('fulltime')">Full Time</span>
                        <span class="filter-chip" onclick="applyQuickFilter('parttime')">Part Time</span>
                        <span class="filter-chip" onclick="applyQuickFilter('fresher')">Fresher</span>
                        <span class="filter-chip" onclick="applyQuickFilter('senior')">Senior Level</span>
                    </div>
                </div>
            </div>

            <!-- Main Filters -->
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">City</label>
                    <select class="form-select" id="cityFilter" onchange="applyFilters()">
                        <option value="">All Cities</option>
                        <option value="mumbai">Mumbai</option>
                        <option value="delhi">Delhi</option>
                        <option value="bangalore">Bangalore</option>
                        <option value="chennai">Chennai</option>
                        <option value="kolkata">Kolkata</option>
                        <option value="hyderabad">Hyderabad</option>
                        <option value="pune">Pune</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Role Category</label>
                    <select class="form-select" id="roleFilter" onchange="applyFilters()">
                        <option value="">All Roles</option>
                        <option value="developer">Developer</option>
                        <option value="designer">Designer</option>
                        <option value="manager">Manager</option>
                        <option value="analyst">Analyst</option>
                        <option value="tester">Tester</option>
                        <option value="data engineer">Data Engineer</option>
                        <option value="data scientist">Data Scientist</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Job Type</label>
                    <select class="form-select" id="jobTypeFilter" onchange="applyFilters()">
                        <option value="">All Types</option>
                        <option value="fulltime">Full Time</option>
                        <option value="parttime">Part Time</option>
                        <option value="contract">Contract</option>
                        <option value="internship">Internship</option>
                        <option value="remote">Remote</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Company Size</label>
                    <select class="form-select" id="companySizeFilter" onchange="applyFilters()">
                        <option value="">All Sizes</option>
                        <option value="startup">Startup (1-50)</option>
                        <option value="medium">Medium (51-500)</option>
                        <option value="large">Large (500+)</option>
                    </select>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-12">
                    <small class="text-muted">
                        <span id="jobCount">Showing 1-10 of 247 jobs</span>
                        <span id="filterStatus" class="ms-2"></span>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container-fluid px-4">
        <div class="row">
            <!-- Left Sidebar - Advanced Filters -->
            <div class="col-lg-3">
                <div class="sidebar">
                    <div class="sidebar-header">
                        <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Advanced Filters</h5>
                    </div>
                    
                    <!-- Experience Level -->
                    <div class="filter-group">
                        <h6 class="fw-semibold mb-3">Experience Level</h6>
                        <div class="mb-3">
                            <label class="form-label">Years of Experience</label>
                            <input type="range" class="form-range range-slider" min="0" max="15" value="0" id="experienceRange" oninput="updateExperienceLabel()">
                            <div class="d-flex justify-content-between">
                                <small>0 years</small>
                                <small id="experienceLabel">Any</small>
                                <small>15+ years</small>
                            </div>
                        </div>
                        <div class="experience-levels">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="fresher" onchange="applyFilters()">
                                <label class="form-check-label" for="fresher">Fresher (0-1 year)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="junior" onchange="applyFilters()">
                                <label class="form-check-label" for="junior">Junior (1-3 years)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="mid" onchange="applyFilters()">
                                <label class="form-check-label" for="mid">Mid-level (3-7 years)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="senior" onchange="applyFilters()">
                                <label class="form-check-label" for="senior">Senior (7+ years)</label>
                            </div>
                        </div>
                    </div>

                    <!-- Salary Range -->
                    <div class="filter-group">
                        <h6 class="fw-semibold mb-3">Salary Range (₹ LPA)</h6>
                        <div class="mb-3">
                            <input type="range" class="form-range" min="0" max="50" value="0" id="salaryRange" oninput="updateSalaryLabel()">
                            <div class="d-flex justify-content-between">
                                <small>₹0 LPA</small>
                                <small id="salaryLabel">Any</small>
                                <small>₹50+ LPA</small>
                            </div>
                        </div>
                    </div>

                    <!-- Work Mode -->
                    <div class="filter-group">
                        <h6 class="fw-semibold mb-3">Work Mode</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="wfh" onchange="applyFilters()">
                            <label class="form-check-label" for="wfh">Work from Home</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="hybrid" onchange="applyFilters()">
                            <label class="form-check-label" for="hybrid">Hybrid</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="office" onchange="applyFilters()">
                            <label class="form-check-label" for="office">Office</label>
                        </div>
                    </div>

                    <!-- Posted Date -->
                    <div class="filter-group">
                        <h6 class="fw-semibold mb-3">Posted</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="postedDate" id="today" onchange="applyFilters()">
                            <label class="form-check-label" for="today">Today</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="postedDate" id="week" onchange="applyFilters()">
                            <label class="form-check-label" for="week">This Week</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="postedDate" id="month" onchange="applyFilters()">
                            <label class="form-check-label" for="month">This Month</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Middle Content - Job Listings -->
            <div class="col-lg-6">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold">Job Listings</h4>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary active" onclick="toggleView('card')">
                            <i class="fas fa-th-large"></i>
                        </button>
                        <button type="button" class="btn btn-outline-primary" onclick="toggleView('list')">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>

                <div id="jobListings">
                    <!-- Job Card 1 -->
                    <div class="job-card">
                        <div class="job-card-header">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="job-title">Senior React Developer</h5>
                                    <span class="company-badge">TechCorp Solutions</span>
                                </div>
                                <div class="text-end">
                                    <i class="far fa-heart fs-5 text-muted" style="cursor: pointer;" onclick="toggleFavorite(this)"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="job-meta">
                                <div class="meta-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>Mumbai, Maharashtra</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-clock"></i>
                                    <span>Full Time</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-rupee-sign"></i>
                                    <span>₹8-12 LPA</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-calendar"></i>
                                    <span>2 days ago</span>
                                </div>
                            </div>
                            <p class="text-muted mb-3">We are looking for an experienced React developer to join our dynamic team. You'll work on cutting-edge projects...</p>
                            <div class="mb-3">
                                <span class="tech-tag">React</span>
                                <span class="tech-tag">JavaScript</span>
                                <span class="tech-tag">Node.js</span>
                                <span class="tech-tag">MongoDB</span>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary flex-fill" onclick="viewJobDetails(1)">View Details</button>
                                <button class="btn btn-outline-primary" onclick="applyNow(1)">
                                    <i class="fas fa-paper-plane me-1"></i>Apply Now
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Job Card 2 -->
                    <div class="job-card">
                        <div class="job-card-header">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="job-title">Data Scientist</h5>
                                    <span class="company-badge">Analytics Pro</span>
                                </div>
                                <div class="text-end">
                                    <i class="far fa-heart fs-5 text-muted" style="cursor: pointer;" onclick="toggleFavorite(this)"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="job-meta">
                                <div class="meta-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>Bangalore, Karnataka</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-clock"></i>
                                    <span>Full Time</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-rupee-sign"></i>
                                    <span>₹12-18 LPA</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-calendar"></i>
                                    <span>1 day ago</span>
                                </div>
                            </div>
                            <p class="text-muted mb-3">Join our AI/ML team to work on innovative data science projects. Experience with Python, TensorFlow required...</p>
                            <div class="mb-3">
                                <span class="tech-tag">Python</span>
                                <span class="tech-tag">TensorFlow</span>
                                <span class="tech-tag">SQL</span>
                                <span class="tech-tag">Machine Learning</span>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary flex-fill" onclick="viewJobDetails(2)">View Details</button>
                                <button class="btn btn-outline-primary" onclick="applyNow(2)">
                                    <i class="fas fa-paper-plane me-1"></i>Apply Now
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Job Card 3 -->
                    <div class="job-card">
                        <div class="job-card-header">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="job-title">UI/UX Designer</h5>
                                    <span class="company-badge">Creative Studio</span>
                                </div>
                                <div class="text-end">
                                    <i class="far fa-heart fs-5 text-muted" style="cursor: pointer;" onclick="toggleFavorite(this)"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="job-meta">
                                <div class="meta-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>Delhi, NCR</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-clock"></i>
                                    <span>Full Time</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-rupee-sign"></i>
                                    <span>₹6-10 LPA</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-calendar"></i>
                                    <span>3 days ago</span>
                                </div>
                            </div>
                            <p class="text-muted mb-3">Create stunning user experiences for our mobile and web applications. Portfolio review required...</p>
                            <div class="mb-3">
                                <span class="tech-tag">Figma</span>
                                <span class="tech-tag">Adobe XD</span>
                                <span class="tech-tag">Sketch</span>
                                <span class="tech-tag">Prototyping</span>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary flex-fill" onclick="viewJobDetails(3)">View Details</button>
                                <button class="btn btn-outline-primary" onclick="applyNow(3)">
                                    <i class="fas fa-paper-plane me-1"></i>Apply Now
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <nav aria-label="Job pagination" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item">
                            <a class="page-link" href="#" onclick="changePage(1)">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                        <li class="page-item active">
                            <a class="page-link" href="#" onclick="changePage(1)">1</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#" onclick="changePage(2)">2</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#" onclick="changePage(3)">3</a>
                        </li>
                        <li class="page-item">
                            <span class="page-link">...</span>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#" onclick="changePage(25)">25</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#" onclick="changePage(2)">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>

            <!-- Right Sidebar -->
            <div class="col-lg-3">
                <!-- Latest Jobs -->
                <div class="sidebar-jobs mb-4">
                    <div class="sidebar-header">
                        <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Latest Jobs</h5>
                    </div>
                    <div id="latestJobs">
                        <div class="trending-job" onclick="viewJobDetails(4)">
                            <h6 class="mb-1">Full Stack Developer</h6>
                            <small class="text-muted">TechStart Inc • Mumbai</small>
                            <div class="d-flex justify-content-between mt-2">
                                <small class="text-primary">₹10-15 LPA</small>
                                <small class="text-muted">2 hours ago</small>
                            </div>
                        </div>
                        <div class="trending-job" onclick="viewJobDetails(5)">
                            <h6 class="mb-1">DevOps Engineer</h6>
                            <small class="text-muted">CloudTech • Bangalore</small>
                            <div class="d-flex justify-content-between mt-2">
                                <small class="text-primary">₹12-20 LPA</small>
                                <small class="text-muted">5 hours ago</small>
                            </div>
                        </div>
                        <div class="trending-job" onclick="viewJobDetails(6)">
                            <h6 class="mb-1">Product Manager</h6>
                            <small class="text-muted">InnovateLabs • Delhi</small>
                            <div class="d-flex justify-content-between mt-2">
                                <small class="text-primary">₹15-25 LPA</small>
                                <small class="text-muted">1 day ago</small>
                            </div>
                        </div>
                        <div class="trending-job" onclick="viewJobDetails(7)">
                            <h6 class="mb-1">QA Automation</h6>
                            <small class="text-muted">TestPro • Chennai</small>
                            <div class="d-flex justify-content-between mt-2">
                                <small class="text-primary">₹8-12 LPA</small>
                                <small class="text-muted">2 days ago</small>
                            </div>
                        </div>
                    </div>
                    <div class="p-3">
                        <button class="btn btn-outline-primary w-100 btn-sm" onclick="viewAllJobs()">
                            View All Latest Jobs
                        </button>
                    </div>
                </div>

                <!-- Trending Technologies -->
                <div class="sidebar-jobs mb-4">
                    <div class="sidebar-header">
                        <h5 class="mb-0"><i class="fas fa-fire me-2"></i>Trending Skills</h5>
                    </div>
                    <div class="p-3">
                        <div class="trending-tech mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-semibold">React.js</span>
                                <span class="text-primary">247 jobs</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar" role="progressbar" style="width: 85%; background: var(--gradient-bg);"></div>
                            </div>
                        </div>
                        <div class="trending-tech mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-semibold">Python</span>
                                <span class="text-primary">189 jobs</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar" role="progressbar" style="width: 70%; background: var(--gradient-bg);"></div>
                            </div>
                        </div>
                        <div class="trending-tech mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-semibold">Node.js</span>
                                <span class="text-primary">156 jobs</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar" role="progressbar" style="width: 60%; background: var(--gradient-bg);"></div>
                            </div>
                        </div>
                        <div class="trending-tech mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-semibold">AWS</span>
                                <span class="text-primary">134 jobs</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar" role="progressbar" style="width: 50%; background: var(--gradient-bg);"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Learning Zone -->
                <div class="learning-zone">
                    <h6 class="fw-bold mb-3"><i class="fas fa-graduation-cap me-2"></i>Learning Zone</h6>
                    <div class="learning-item mb-2">
                        <a href="#" class="text-decoration-none text-dark">
                            <i class="fas fa-play-circle me-2 text-primary"></i>
                            Complete React Developer Course
                        </a>
                        <div class="text-muted small">1,234 enrolled</div>
                    </div>
                    <div class="learning-item mb-2">
                        <a href="#" class="text-decoration-none text-dark">
                            <i class="fas fa-play-circle me-2 text-primary"></i>
                            Python for Data Science
                        </a>
                        <div class="text-muted small">987 enrolled</div>
                    </div>
                    <div class="learning-item mb-3">
                        <a href="#" class="text-decoration-none text-dark">
                            <i class="fas fa-play-circle me-2 text-primary"></i>
                            AWS Cloud Practitioner
                        </a>
                        <div class="text-muted small">756 enrolled</div>
                    </div>
                    <button class="btn btn-primary btn-sm w-100">Explore All Courses</button>
                </div>

                <!-- Popular Companies -->
                <div class="sidebar-jobs">
                    <div class="sidebar-header">
                        <h5 class="mb-0"><i class="fas fa-building me-2"></i>Top Hiring</h5>
                    </div>
                    <div class="p-3">
                        <div class="company-item d-flex align-items-center mb-3">
                            <div class="company-logo me-3">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-code text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0">TechCorp Solutions</h6>
                                <small class="text-muted">23 open positions</small>
                            </div>
                        </div>
                        <div class="company-item d-flex align-items-center mb-3">
                            <div class="company-logo me-3">
                                <div class="rounded-circle bg-success d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-chart-line text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0">Analytics Pro</h6>
                                <small class="text-muted">15 open positions</small>
                            </div>
                        </div>
                        <div class="company-item d-flex align-items-center mb-3">
                            <div class="company-logo me-3">
                                <div class="rounded-circle bg-warning d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-palette text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0">Creative Studio</h6>
                                <small class="text-muted">8 open positions</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Job Details Modal -->
    <div class="modal fade" id="jobModal" tabindex="-1" aria-labelledby="jobModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="jobModalLabel">Job Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="jobModalBody">
                    <!-- Job details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="applyFromModal()">
                        <i class="fas fa-paper-plane me-2"></i>Apply Now
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Apply Modal -->
    <div class="modal fade" id="applyModal" tabindex="-1" aria-labelledby="applyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="applyModalLabel">Apply for Job</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="applyForm">
                        <div class="mb-3">
                            <label for="applicantName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="applicantName" required>
                        </div>
                        <div class="mb-3">
                            <label for="applicantEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="applicantEmail" required>
                        </div>
                        <div class="mb-3">
                            <label for="applicantPhone" class="form-label">Phone</label>
                            <input type="tel" class="form-control" id="applicantPhone" required>
                        </div>
                        <div class="mb-3">
                            <label for="resume" class="form-label">Resume</label>
                            <input type="file" class="form-control" id="resume" accept=".pdf,.doc,.docx">
                        </div>
                        <div class="mb-3">
                            <label for="coverLetter" class="form-label">Cover Letter (Optional)</label>
                            <textarea class="form-control" id="coverLetter" rows="4"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="submitApplication()">
                        <i class="fas fa-paper-plane me-2"></i>Submit Application
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sample job data (in real implementation, this would come from your PHP backend)
        const jobsData = [
            {
                id: 1,
                title: "Senior React Developer",
                company: "TechCorp Solutions",
                location: "Mumbai, Maharashtra",
                type: "Full Time",
                salary: "₹8-12 LPA",
                posted: "2 days ago",
                experience: "3-5 years",
                description: "We are looking for an experienced React developer to join our dynamic team. You'll work on cutting-edge projects using modern technologies and best practices.",
                requirements: ["3+ years React experience", "Strong JavaScript/TypeScript skills", "Experience with Redux/Context API", "Knowledge of RESTful APIs", "Git version control"],
                technologies: ["React", "JavaScript", "Node.js", "MongoDB"],
                email: "careers@techcorp.com"
            },
            {
                id: 2,
                title: "Data Scientist",
                company: "Analytics Pro",
                location: "Bangalore, Karnataka",
                type: "Full Time",
                salary: "₹12-18 LPA",
                posted: "1 day ago",
                experience: "2-4 years",
                description: "Join our AI/ML team to work on innovative data science projects. Experience with Python, TensorFlow required.",
                requirements: ["2+ years in Data Science", "Strong Python skills", "Experience with ML frameworks", "Statistical analysis expertise", "SQL proficiency"],
                technologies: ["Python", "TensorFlow", "SQL", "Machine Learning"],
                email: "jobs@analyticspro.com"
            },
            {
                id: 3,
                title: "UI/UX Designer",
                company: "Creative Studio",
                location: "Delhi, NCR",
                type: "Full Time",
                salary: "₹6-10 LPA",
                posted: "3 days ago",
                experience: "2-3 years",
                description: "Create stunning user experiences for our mobile and web applications. Portfolio review required.",
                requirements: ["2+ years UI/UX experience", "Proficient in Figma/Adobe XD", "Strong portfolio", "Understanding of user research", "Responsive design skills"],
                technologies: ["Figma", "Adobe XD", "Sketch", "Prototyping"],
                email: "design@creativestudio.com"
            }
        ];

        let currentPage = 1;
        let totalPages = 25;
        let currentFilters = {
            city: '',
            role: '',
            jobType: '',
            companySize: '',
            experience: 0,
            salary: 0,
            workMode: [],
            postedDate: ''
        };

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            loadLatestJobs();
            updateJobCount();
        });

        function applyFilters() {
            // Get all filter values
            currentFilters.city = document.getElementById('cityFilter').value;
            currentFilters.role = document.getElementById('roleFilter').value;
            currentFilters.jobType = document.getElementById('jobTypeFilter').value;
            currentFilters.companySize = document.getElementById('companySizeFilter').value;
            
            // Reset to page 1 when filters change
            currentPage = 1;
            
            // Update job listings
            filterJobs();
            updatePagination();
            updateJobCount();
        }

        function applyQuickFilter(filterType) {
            // Reset other filters and apply quick filter
            document.getElementById('cityFilter').value = '';
            document.getElementById('roleFilter').value = '';
            
            switch(filterType) {
                case 'remote':
                    document.getElementById('jobTypeFilter').value = 'remote';
                    break;
                case 'fulltime':
                    document.getElementById('jobTypeFilter').value = 'fulltime';
                    break;
                case 'parttime':
                    document.getElementById('jobTypeFilter').value = 'parttime';
                    break;
                case 'fresher':
                    document.getElementById('fresher').checked = true;
                    break;
                case 'senior':
                    document.getElementById('senior').checked = true;
                    break;
            }
            
            applyFilters();
        }

        function clearAllFilters() {
            // Reset all form elements
            document.getElementById('cityFilter').value = '';
            document.getElementById('roleFilter').value = '';
            document.getElementById('jobTypeFilter').value = '';
            document.getElementById('companySizeFilter').value = '';
            document.getElementById('experienceRange').value = 0;
            document.getElementById('salaryRange').value = 0;
            
            // Reset checkboxes
            const checkboxes = document.querySelectorAll('input[type="checkbox"], input[type="radio"]');
            checkboxes.forEach(cb => cb.checked = false);
            
            updateExperienceLabel();
            updateSalaryLabel();
            
            currentPage = 1;
            filterJobs();
            updatePagination();
            updateJobCount();
        }

        function updateExperienceLabel() {
            const value = document.getElementById('experienceRange').value;
            const label = document.getElementById('experienceLabel');
            if (value == 0) {
                label.textContent = 'Any';
            } else {
                label.textContent = value + '+ years';
            }
        }

        function updateSalaryLabel() {
            const value = document.getElementById('salaryRange').value;
            const label = document.getElementById('salaryLabel');
            if (value == 0) {
                label.textContent = 'Any';
            } else {
                label.textContent = '₹' + value + '+ LPA';
            }
        }

        function searchJobs() {
            const searchTerm = document.getElementById('searchInput').value;
            console.log('Searching for:', searchTerm);
            // Implement search logic
            applyFilters();
        }

        function filterJobs() {
            // In real implementation, this would make an AJAX call to your PHP backend
            console.log('Applying filters:', currentFilters);
            
            // Show loading state
            const jobListings = document.getElementById('jobListings');
            jobListings.innerHTML = '<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i><p class="mt-3">Loading jobs...</p></div>';
            
            // Simulate API call delay
            setTimeout(() => {
                jobListings.innerHTML = generateJobCards();
            }, 800);
        }

        function generateJobCards() {
            return jobsData.map(job => `
                <div class="job-card">
                    <div class="job-card-header">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="job-title">${job.title}</h5>
                                <span class="company-badge">${job.company}</span>
                            </div>
                            <div class="text-end">
                                <i class="far fa-heart fs-5 text-muted" style="cursor: pointer;" onclick="toggleFavorite(this)"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="job-meta">
                            <div class="meta-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>${job.location}</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-clock"></i>
                                <span>${job.type}</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-rupee-sign"></i>
                                <span>${job.salary}</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-calendar"></i>
                                <span>${job.posted}</span>
                            </div>
                        </div>
                        <p class="text-muted mb-3">${job.description}</p>
                        <div class="mb-3">
                            ${job.technologies.map(tech => `<span class="tech-tag">${tech}</span>`).join('')}
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary flex-fill" onclick="viewJobDetails(${job.id})">View Details</button>
                            <button class="btn btn-outline-primary" onclick="applyNow(${job.id})">
                                <i class="fas fa-paper-plane me-1"></i>Apply Now
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function viewJobDetails(jobId) {
            const job = jobsData.find(j => j.id === jobId);
            if (!job) return;

            const modalBody = document.getElementById('jobModalBody');
            modalBody.innerHTML = `
                <div class="job-detail-header mb-4">
                    <h4 class="text-primary">${job.title}</h4>
                    <h6 class="text-muted">${job.company}</h6>
                    <div class="job-meta mt-3">
                        <div class="meta-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>${job.location}</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-clock"></i>
                            <span>${job.type}</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-rupee-sign"></i>
                            <span>${job.salary}</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-briefcase"></i>
                            <span>${job.experience}</span>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold">Job Description</h6>
                    <p>${job.description}</p>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold">Requirements</h6>
                    <ul class="list-unstyled">
                        ${job.requirements.map(req => `<li class="mb-2"><i class="fas fa-check text-success me-2"></i>${req}</li>`).join('')}
                    </ul>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold">Technologies</h6>
                    <div>
                        ${job.technologies.map(tech => `<span class="tech-tag">${tech}</span>`).join('')}
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-envelope me-2"></i>
                    <strong>Contact:</strong> ${job.email}
                </div>
            `;

            const modal = new bootstrap.Modal(document.getElementById('jobModal'));
            modal.show();
        }

        function applyNow(jobId) {
            const job = jobsData.find(j => j.id === jobId);
            if (!job) return;

            document.getElementById('applyModalLabel').textContent = `Apply for ${job.title}`;
            const modal = new bootstrap.Modal(document.getElementById('applyModal'));
            modal.show();
        }

        function applyFromModal() {
            const jobModal = bootstrap.Modal.getInstance(document.getElementById('jobModal'));
            jobModal.hide();
            
            setTimeout(() => {
                const applyModal = new bootstrap.Modal(document.getElementById('applyModal'));
                applyModal.show();
            }, 300);
        }

        function submitApplication() {
            const form = document.getElementById('applyForm');
            if (form.checkValidity()) {
                // In real implementation, submit to your PHP backend
                alert('Application submitted successfully! We will contact you soon.');
                const modal = bootstrap.Modal.getInstance(document.getElementById('applyModal'));
                modal.hide();
                form.reset();
            } else {
                form.reportValidity();
            }
        }

        function toggleFavorite(element) {
            if (element.classList.contains('far')) {
                element.classList.remove('far');
                element.classList.add('fas');
                element.style.color = '#e74c3c';
            } else {
                element.classList.remove('fas');
                element.classList.add('far');
                element.style.color = '#6c757d';
            }
        }

        function changePage(page) {
            currentPage = page;
            
            // Update active pagination
            document.querySelectorAll('.page-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // In real implementation, load new page data
            console.log('Loading page:', page);
            
            // Scroll to top of job listings
            document.getElementById('jobListings').scrollIntoView({ behavior: 'smooth' });
        }

        function updatePagination() {
            // Update pagination based on current filters and page
            console.log('Updating pagination for page:', currentPage);
        }

        function updateJobCount() {
            const count = Math.floor(Math.random() * 500) + 50; // Simulated count
            document.getElementById('totalJobs').textContent = count.toLocaleString();
            document.getElementById('jobCount').textContent = `Showing ${(currentPage-1)*10+1}-${Math.min(currentPage*10, count)} of ${count} jobs`;
            
            // Update filter status
            const activeFilters = Object.values(currentFilters).filter(v => v && v.length > 0).length;
            if (activeFilters > 0) {
                document.getElementById('filterStatus').innerHTML = `<span class="badge bg-primary">${activeFilters} filters active</span>`;
            } else {
                document.getElementById('filterStatus').innerHTML = '';
            }
        }

        function loadLatestJobs() {
            // In real implementation, this would fetch from your PHP backend
            console.log('Loading latest jobs for sidebar');
        }

        function viewAllJobs() {
            // Clear filters and show all jobs
            clearAllFilters();
        }

        function toggleView(viewType) {
            const buttons = document.querySelectorAll('.btn-group .btn');
            buttons.forEach(btn => btn.classList.remove('active'));
            
            if (viewType === 'card') {
                buttons[0].classList.add('active');
                // Switch to card view
            } else {
                buttons[1].classList.add('active');
                // Switch to list view
            }
        }

        // Auto-refresh latest jobs every 30 seconds
        setInterval(loadLatestJobs, 30000);

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

        // Add loading animation for filter changes
        function showLoadingState() {
            const jobListings = document.getElementById('jobListings');
            jobListings.style.opacity = '0.5';
            jobListings.style.pointerEvents = 'none';
        }

        function hideLoadingState() {
            const jobListings = document.getElementById('jobListings');
            jobListings.style.opacity = '1';
            jobListings.style.pointerEvents = 'auto';
        }

        // Enhanced filter functionality
        function filterByRole(role) {
            document.getElementById('roleFilter').value = role;
            applyFilters();
        }

        // Real-time search with debouncing
        let searchTimeout;
        document.getElementById('searchInput').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                searchJobs();
            }, 500);
        });
    </script>
</body>
</html>