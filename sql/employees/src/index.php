<?php
session_start();

// Database connection
$host = $_ENV['DB_HOST'] ?? 'db';
$dbname = $_ENV['DB_NAME'] ?? 'employee_portal';
$username = $_ENV['DB_USER'] ?? 'webapp';
$password = $_ENV['DB_PASS'] ?? 'webapp123';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch(PDOException $e) {
    die("Database service temporarily unavailable. Please try again later.");
}

// Handle search functionality - VULNERABLE SQL INJECTION
$search_results = [];
$search_query = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_query = $_GET['search'];
    
    // VULNERABLE SQL QUERY - Main injection point for CTF
    $sql = "SELECT e.*, p.project_name, p.budget 
            FROM employees e 
            LEFT JOIN projects p ON e.department = 'IT' AND p.status = 'active'
            WHERE e.first_name LIKE '%$search_query%' 
               OR e.last_name LIKE '%$search_query%' 
               OR e.department LIKE '%$search_query%'
            ORDER BY e.id";
    
    try {
        $stmt = $pdo->query($sql);
        if ($stmt) {
            $search_results = $stmt->fetchAll();
        }
    } catch(Exception $e) {
        $search_results = [];
    }
}

// Get department statistics for dashboard
$dept_stats = [];
try {
    $stmt = $pdo->query("SELECT department, COUNT(*) as count, AVG(salary) as avg_salary FROM employees WHERE status = 'active' GROUP BY department");
    if ($stmt) {
        $dept_stats = $stmt->fetchAll();
    }
} catch(Exception $e) {
    $dept_stats = [];
}

// Get recent projects
$recent_projects = [];
try {
    $stmt = $pdo->query("SELECT * FROM projects ORDER BY start_date DESC LIMIT 5");
    if ($stmt) {
        $recent_projects = $stmt->fetchAll();
    }
} catch(Exception $e) {
    $recent_projects = [];
}

// Get employees for directory
$employees = [];
try {
    $stmt = $pdo->query("SELECT id, first_name, last_name, department, position, hire_date, status FROM employees WHERE status = 'active' ORDER BY id");
    if ($stmt) {
        $employees = $stmt->fetchAll();
    }
} catch(Exception $e) {
    $employees = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management Portal</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .main-container {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin: 2rem 0;
            border: 1px solid #e9ecef;
        }
        .nav-pills .nav-link {
            border-radius: 8px;
            margin: 0 4px;
            color: #495057;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            transition: all 0.2s ease;
        }
        .nav-pills .nav-link:hover {
            background-color: #e9ecef;
            color: #495057;
        }
        .nav-pills .nav-link.active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }
        .card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: box-shadow 0.2s ease;
        }
        .card:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            font-weight: 600;
        }
        .search-box {
            border-radius: 8px;
            border: 2px solid #ced4da;
            padding: 0.75rem;
            transition: border-color 0.2s ease;
        }
        .search-box:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
            outline: none;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
            transform: translateY(-1px);
        }
        .table thead th {
            background-color: #343a40;
            color: white;
            border: none;
            font-weight: 500;
        }
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        .stat-card {
            background-color: #007bff;
            color: white;
            border-radius: 8px;
            border: none;
        }
        .badge {
            font-size: 0.875em;
            padding: 0.5em 0.75em;
        }
        .alert {
            border-radius: 8px;
            border: none;
        }
        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
        }
        h1 {
            color: #343a40;
            font-weight: 700;
        }
        h5 {
            color: #495057;
        }
        .text-primary {
            color: #007bff !important;
        }
        .bg-primary {
            background-color: #007bff !important;
        }
        .bg-success {
            background-color: #28a745 !important;
        }
        .bg-info {
            background-color: #17a2b8 !important;
        }
        .bg-warning {
            background-color: #ffc107 !important;
            color: #212529 !important;
        }
        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="main-container p-4">
            <div class="row mb-4">
                <div class="col-12">
                    <h1 class="text-center mb-4">
                        <i class="fas fa-building me-3"></i>
                        Employee Management Portal
                    </h1>
                    
                    <ul class="nav nav-pills justify-content-center mb-4" id="mainTabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="pill" href="#dashboard">
                                <i class="fas fa-chart-dashboard me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="pill" href="#employees">
                                <i class="fas fa-users me-2"></i>Employees
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="pill" href="#projects">
                                <i class="fas fa-project-diagram me-2"></i>Projects
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="pill" href="#search">
                                <i class="fas fa-search me-2"></i>Advanced Search
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="tab-content">
                <!-- Dashboard Tab -->
                <div class="tab-pane fade show active" id="dashboard">
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-users fa-3x mb-3"></i>
                                    <h3><?php echo count($dept_stats); ?></h3>
                                    <p class="mb-0">Active Departments</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-project-diagram fa-3x mb-3"></i>
                                    <h3><?php echo count($recent_projects); ?></h3>
                                    <p class="mb-0">Active Projects</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-chart-line fa-3x mb-3"></i>
                                    <h3>95%</h3>
                                    <p class="mb-0">System Efficiency</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card fade-in">
                                <div class="card-header">
                                    <h5><i class="fas fa-building me-2"></i>Department Overview</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Department</th>
                                                    <th>Employees</th>
                                                    <th>Avg Salary</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($dept_stats as $dept): ?>
                                                <tr>
                                                    <td><i class="fas fa-dot-circle me-2 text-primary"></i><?php echo htmlspecialchars($dept['department']); ?></td>
                                                    <td><span class="badge bg-primary"><?php echo $dept['count']; ?></span></td>
                                                    <td>$<?php echo number_format($dept['avg_salary'], 0); ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card fade-in">
                                <div class="card-header">
                                    <h5><i class="fas fa-tasks me-2"></i>Recent Projects</h5>
                                </div>
                                <div class="card-body">
                                    <?php foreach($recent_projects as $project): ?>
                                    <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                        <div>
                                            <h6 class="mb-1"><?php echo htmlspecialchars($project['project_name']); ?></h6>
                                            <small class="text-muted"><?php echo htmlspecialchars($project['status']); ?></small>
                                        </div>
                                        <span class="badge bg-success">$<?php echo number_format($project['budget']); ?></span>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Employees Tab -->
                <div class="tab-pane fade" id="employees">
                    <div class="card fade-in">
                        <div class="card-header">
                            <h5><i class="fas fa-users me-2"></i>Employee Directory</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="employeeTable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Department</th>
                                            <th>Position</th>
                                            <th>Hire Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($employees as $emp): ?>
                                        <tr>
                                            <td><?php echo $emp['id']; ?></td>
                                            <td><?php echo htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']); ?></td>
                                            <td><span class="badge bg-info"><?php echo htmlspecialchars($emp['department']); ?></span></td>
                                            <td><?php echo htmlspecialchars($emp['position']); ?></td>
                                            <td><?php echo $emp['hire_date']; ?></td>
                                            <td><span class="badge bg-success"><?php echo $emp['status']; ?></span></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Projects Tab -->
                <div class="tab-pane fade" id="projects">
                    <div class="card fade-in">
                        <div class="card-header">
                            <h5><i class="fas fa-project-diagram me-2"></i>Project Management</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="projectTable">
                                    <thead>
                                        <tr>
                                            <th>Project Name</th>
                                            <th>Description</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Budget</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($recent_projects as $project): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($project['project_name']); ?></strong></td>
                                            <td><?php echo htmlspecialchars(substr($project['description'], 0, 50)); ?>...</td>
                                            <td><?php echo $project['start_date']; ?></td>
                                            <td><?php echo $project['end_date']; ?></td>
                                            <td>$<?php echo number_format($project['budget']); ?></td>
                                            <td>
                                                <span class="badge <?php echo $project['status'] == 'active' ? 'bg-success' : 'bg-warning'; ?>">
                                                    <?php echo $project['status']; ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Advanced Search Tab -->
                <div class="tab-pane fade" id="search">
                    <div class="card fade-in">
                        <div class="card-header">
                            <h5><i class="fas fa-search me-2"></i>Advanced Employee Search</h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" class="mb-4">
                                <div class="row">
                                    <div class="col-md-8">
                                        <input type="text" 
                                               name="search" 
                                               class="form-control search-box" 
                                               placeholder="Search employees by name, department..." 
                                               value="<?php echo htmlspecialchars($search_query); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-search me-2"></i>Search
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" name="active_tab" value="search">
                            </form>

                            <?php if(!empty($search_query)): ?>
                            <div class="alert alert-info">
                                Search results for "<?php echo htmlspecialchars($search_query); ?>"
                            </div>
                            
                            <?php if(!empty($search_results)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Department</th>
                                            <th>Position</th>
                                            <th>Salary</th>
                                            <th>Hire Date</th>
                                            <th>Project Info</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($search_results as $result): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($result['id'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars(($result['first_name'] ?? '') . ' ' . ($result['last_name'] ?? '')); ?></td>
                                            <td><span class="badge bg-info"><?php echo htmlspecialchars($result['department'] ?? ''); ?></span></td>
                                            <td><?php echo htmlspecialchars($result['position'] ?? ''); ?></td>
                                            <td><?php echo isset($result['salary']) ? '$' . number_format($result['salary']) : 'N/A'; ?></td>
                                            <td><?php echo htmlspecialchars($result['hire_date'] ?? ''); ?></td>
                                            <td>
                                                <?php if(!empty($result['project_name'])): ?>
                                                    <small><strong><?php echo htmlspecialchars($result['project_name']); ?></strong><br>
                                                    Budget: $<?php echo number_format($result['budget'] ?? 0); ?></small>
                                                <?php else: ?>
                                                    <small class="text-muted">No active projects</small>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="alert alert-warning">
                                No results found for "<?php echo htmlspecialchars($search_query); ?>"
                            </div>
                            <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize DataTables
            $('#employeeTable, #projectTable').DataTable({
                responsive: true,
                pageLength: 10,
                language: {
                    search: "Filter records:"
                }
            });

            // Handle tab switching
            if(new URLSearchParams(window.location.search).get('active_tab') === 'search') {
                $('#mainTabs a[href="#search"]').tab('show');
            }

            // Add smooth transitions
            $('.tab-pane').addClass('fade-in');
        });
    </script>
</body>
</html>
