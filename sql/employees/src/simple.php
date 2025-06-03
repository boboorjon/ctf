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
    die("Database connection failed");
}

// Handle search - VULNERABLE SQL INJECTION
$search_results = [];
$search_query = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_query = $_GET['search'];
    
    // VULNERABLE CODE - DO NOT USE IN PRODUCTION
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

// Get employees for display
$employees = [];
try {
    $stmt = $pdo->query("SELECT * FROM employees WHERE status = 'active' ORDER BY id");
    if ($stmt) {
        $employees = $stmt->fetchAll();
    }
} catch(Exception $e) {
    $employees = [];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>CTF SQL Injection Challenge</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <h1 class="text-center mb-4">Employee Management Portal</h1>
        
        <!-- Search Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Advanced Search</h5>
            </div>
            <div class="card-body">
                <form method="GET">
                    <div class="row">
                        <div class="col-md-8">
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Search employees..." 
                                   value="<?php echo htmlspecialchars($search_query); ?>">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">Search</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Search Results -->
        <?php if (!empty($search_query)): ?>
            <div class="card mb-4">
                <div class="card-header">
                    Search Results for "<?php echo htmlspecialchars($search_query); ?>"
                </div>
                <div class="card-body">
                    <?php if (!empty($search_results)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Department</th>
                                        <th>Position</th>
                                        <th>Salary</th>
                                        <th>Hire Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($search_results as $row): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['id'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')); ?></td>
                                        <td><?php echo htmlspecialchars($row['department'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($row['position'] ?? ''); ?></td>
                                        <td><?php echo isset($row['salary']) ? '$' . number_format($row['salary']) : ''; ?></td>
                                        <td><?php echo htmlspecialchars($row['hire_date'] ?? ''); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p>No results found.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Employee List -->
        <div class="card">
            <div class="card-header">
                <h5>Employee Directory</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Position</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($employees as $emp): ?>
                            <tr>
                                <td><?php echo $emp['id']; ?></td>
                                <td><?php echo htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($emp['department']); ?></td>
                                <td><?php echo htmlspecialchars($emp['position']); ?></td>
                                <td><span class="badge bg-success"><?php echo $emp['status']; ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
