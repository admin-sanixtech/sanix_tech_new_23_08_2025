<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sanix Technologies</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/user_styles.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
       <?php include('header.php'); ?> <!-- Include your common header -->

      <?php include 'navbar.php'; ?> <!-- includew your common navbar -->

  <div class="container-fluid my-5 px-0">
  <div class="row gx-0">
     <!-- Sidebar Accordion -->
    <aside class="col-md-3 px-3"> 
    <h2 class="text-center mb-4">Data Engineer Interview Preparation Roadmap</h2>

    <div class="row g-4">
        <!-- Step 1: Foundations -->
        <div class="col-md-6">
            <div class="card shadow rounded-4 h-100">
                <div class="card-body">
                    <h4 class="card-title">Step 1: Core Foundations</h4>
                    <ul>
                        <li>SQL (Joins, Window Functions, CTEs, Aggregations)</li>
                        <li>Data Modeling (Star, Snowflake, OLTP vs OLAP)</li>
                        <li>Basic DB Concepts (ACID, Indexing, Partitioning)</li>
                        <li>Python for Data Processing (Pandas, File I/O, Scripts)</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Step 2: Big Data & Distributed Systems -->
        <div class="col-md-6">
            <div class="card shadow rounded-4 h-100">
                <div class="card-body">
                    <h4 class="card-title">Step 2: Big Data & Distributed Systems</h4>
                    <ul>
                        <li>Apache Hadoop Ecosystem (HDFS, MapReduce)</li>
                        <li>Apache Spark (RDDs, DataFrames, Transformations)</li>
                        <li>Partitioning, Shuffling, DAG Execution</li>
                        <li>Performance Optimization in Spark</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Step 3: ETL & Data Pipelines -->
        <div class="col-md-6">
            <div class="card shadow rounded-4 h-100">
                <div class="card-body">
                    <h4 class="card-title">Step 3: ETL & Workflow Tools</h4>
                    <ul>
                        <li>Apache Airflow (DAGs, Operators, Scheduling)</li>
                        <li>DataBricks Notebooks and Jobs</li>
                        <li>ETL Design Best Practices</li>
                        <li>Delta Lake, Auto Loader</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Step 4: Cloud Platforms -->
        <div class="col-md-6">
            <div class="card shadow rounded-4 h-100">
                <div class="card-body">
                    <h4 class="card-title">Step 4: Cloud Platforms</h4>
                    <ul>
                        <li>Azure (Data Factory, Data Lake, Synapse, ADLS)</li>
                        <li>AWS (S3, Glue, Athena, Redshift)</li>
                        <li>Google Cloud (BigQuery, Cloud Composer)</li>
                        <li>IAM, Networking, and Data Security Basics</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Step 5: Programming & DSA -->
        <div class="col-md-6">
            <div class="card shadow rounded-4 h-100">
                <div class="card-body">
                    <h4 class="card-title">Step 5: Coding & Problem Solving</h4>
                    <ul>
                        <li>Data Structures (Arrays, Hashmaps, Queues)</li>
                        <li>Algorithms (Sorting, Recursion, Sliding Window)</li>
                        <li>Practice Platforms: LeetCode, HackerRank</li>
                        <li>SQL + Python-based problems</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Step 6: System Design -->
        <div class="col-md-6">
            <div class="card shadow rounded-4 h-100">
                <div class="card-body">
                    <h4 class="card-title">Step 6: System Design & Architecture</h4>
                    <ul>
                        <li>Data Lakehouse Architecture</li>
                        <li>Real-time vs Batch Processing</li>
                        <li>Designing Scalable Data Pipelines</li>
                        <li>Handling Schema Evolution, Late Arriving Data</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Step 7: Mock Interviews -->
        <div class="col-12">
            <div class="card shadow rounded-4">
                <div class="card-body">
                    <h4 class="card-title">Step 7: Mock Interviews & Resume Prep</h4>
                    <ul>
                        <li>Prepare Project Explanations (Achievements, Challenges)</li>
                        <li>Mock Interviews with Peers or Mentors</li>
                        <li>Behavioral Interview Questions (STAR Method)</li>
                        <li>Tailor Resume with Data Tools and Metrics</li>
                    </ul>
                </div>
            </div>
        </div>
</aside>
    </div>
</div>

<script>
 // Handle list item click
  document.querySelectorAll('.accordion-body li').forEach(item => {
    item.style.cursor = 'pointer';
    item.addEventListener('click', function () {
      const text = this.textContent.trim();
      const content = contentMap[text] || `<h4>${text}</h4><p>Details coming soon...</p>`;
      document.getElementById('contentDisplay').innerHTML = content;

      // Optional: Visually highlight the active item
      document.querySelectorAll('.accordion-body li').forEach(li => li.classList.remove('active'));
      this.classList.add('active');
    });
  });
</script>

<?php include('footer.php'); ?>
</body>
</html>
