<?php include('header.php'); ?> <!-- Include your common header -->

<div class="container my-5">
    <h2 class="text-center mb-4">Microsoft Azure Course Content</h2>

    <div class="accordion" id="azureCourse">
        <!-- Module 1 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#moduleOne" aria-expanded="true" aria-controls="moduleOne">
                    Module 1: Introduction to Cloud and Azure
                </button>
            </h2>
            <div id="moduleOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#azureCourse">
                <div class="accordion-body">
                    <ul>
                        <li>What is Cloud Computing?</li>
                        <li>Cloud Service Models (IaaS, PaaS, SaaS)</li>
                        <li>Overview of Microsoft Azure</li>
                        <li>Azure Global Infrastructure</li>
                        <li>Creating an Azure Free Account</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 2 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleTwo" aria-expanded="false" aria-controls="moduleTwo">
                    Module 2: Core Azure Services
                </button>
            </h2>
            <div id="moduleTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#azureCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Azure Portal and Resource Groups</li>
                        <li>Azure Virtual Machines</li>
                        <li>Azure Storage (Blob, File, Queue, Table)</li>
                        <li>Azure App Services</li>
                        <li>Azure Databases (SQL, Cosmos DB)</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 3 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingThree">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleThree" aria-expanded="false" aria-controls="moduleThree">
                    Module 3: Azure Networking
                </button>
            </h2>
            <div id="moduleThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#azureCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Virtual Networks (VNet)</li>
                        <li>Network Security Groups (NSG)</li>
                        <li>Azure Load Balancer</li>
                        <li>Azure VPN Gateway</li>
                        <li>Azure DNS and Private Endpoints</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 4 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingFour">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleFour" aria-expanded="false" aria-controls="moduleFour">
                    Module 4: Identity, Access & Security
                </button>
            </h2>
            <div id="moduleFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#azureCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Azure Active Directory (Azure AD)</li>
                        <li>Role-Based Access Control (RBAC)</li>
                        <li>Multi-Factor Authentication (MFA)</li>
                        <li>Azure Policies and Blueprints</li>
                        <li>Key Vault and Security Center</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 5 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingFive">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleFive" aria-expanded="false" aria-controls="moduleFive">
                    Module 5: Azure DevOps & Automation
                </button>
            </h2>
            <div id="moduleFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#azureCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Introduction to Azure DevOps</li>
                        <li>CI/CD Pipelines</li>
                        <li>ARM Templates</li>
                        <li>Azure Automation and Runbooks</li>
                        <li>GitHub Actions with Azure</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 6 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingSix">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleSix" aria-expanded="false" aria-controls="moduleSix">
                    Module 6: Monitoring and Management
                </button>
            </h2>
            <div id="moduleSix" class="accordion-collapse collapse" aria-labelledby="headingSix" data-bs-parent="#azureCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Azure Monitor</li>
                        <li>Azure Log Analytics</li>
                        <li>Application Insights</li>
                        <li>Alerts and Action Groups</li>
                        <li>Cost Management and Billing</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 7 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingSeven">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleSeven" aria-expanded="false" aria-controls="moduleSeven">
                    Module 7: Real-World Use Cases
                </button>
            </h2>
            <div id="moduleSeven" class="accordion-collapse collapse" aria-labelledby="headingSeven" data-bs-parent="#azureCourse">
                <div class="accordion-body">
                    <ul>
                        <li>Hosting Web Applications</li>
                        <li>Serverless Computing (Azure Functions)</li>
                        <li>Data Backup and Recovery</li>
                        <li>Scalable Architecture Design</li>
                        <li>DevTest Labs and Sandboxes</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Module 8 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingEight">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#moduleEight" aria-expanded="false" aria-controls="moduleEight">
                    Module 8: Capstone Project & Certification Prep
                </button>
            </h2>
            <div id="moduleEight" class="accordion-collapse collapse" aria-labelledby="headingEight" data-bs-parent="#azureCourse">
                <div class="accordion-body">
                    <ul>
                        <li>End-to-End Azure Project</li>
                        <li>Azure Architecture Design</li>
                        <li>Performance and Cost Optimization</li>
                        <li>AZ-900 & AZ-104 Exam Preparation</li>
                        <li>Mock Tests & Hands-on Labs</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?> <!-- Include your common footer -->
