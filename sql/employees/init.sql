USE employee_portal;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    role VARCHAR(20) DEFAULT 'employee',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    department VARCHAR(50),
    position VARCHAR(100),
    salary DECIMAL(10,2),
    hire_date DATE,
    manager_id INT,
    status VARCHAR(20) DEFAULT 'active'
);

CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_name VARCHAR(100) NOT NULL,
    description TEXT,
    start_date DATE,
    end_date DATE,
    budget DECIMAL(12,2),
    status VARCHAR(20) DEFAULT 'active'
);

CREATE TABLE secrets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    flag VARCHAR(100),
    description TEXT
);

INSERT INTO users (username, password, email, role) VALUES
('admin', 'admin123', 'admin@company.com', 'admin'),
('john_doe', 'password123', 'john@company.com', 'employee'),
('jane_smith', 'pass456', 'jane@company.com', 'manager'),
('bob_wilson', 'secret789', 'bob@company.com', 'employee');

INSERT INTO employees (first_name, last_name, department, position, salary, hire_date, manager_id, status) VALUES
('Abdurasul', 'Salimov', 'IT', 'Software Developer', 75000.00, '2023-01-15', NULL, 'active'),
('Xumoyun', 'Nigmatov', 'IT', 'Team Lead', 85000.00, '2022-03-10', NULL, 'active'),
('Bahodir', 'Xudozarov', 'HR', 'HR Specialist', 60000.00, '2023-05-20', NULL, 'active'),
('Jalol', 'Ominov', 'Finance', 'Accountant', 65000.00, '2022-08-05', NULL, 'active'),
('Abduqodir', 'Berdiqulov', 'IT', 'DevOps Engineer', 80000.00, '2023-02-12', 2, 'active'),
('Sherbek', 'Mahmudov', 'Marketing', 'Marketing Manager', 70000.00, '2022-11-30', NULL, 'active'),
('Aziza', 'Kamolova', 'IT', 'Junior Developer', 55000.00, '2024-01-08', 2, 'active'),
('Izzat', 'Odilov', 'Sales', 'Sales Representative', 50000.00, '2023-09-15', NULL, 'active'),
('Dilshod', 'Safarov', 'IT', 'System Administrator', 78000.00, '2023-03-22', NULL, 'active'),
('Ozodbek', 'Sotvoldiyev', 'Finance', 'Financial Analyst', 68000.00, '2023-07-10', NULL, 'active');

INSERT INTO projects (project_name, description, start_date, end_date, budget, status) VALUES
('Website Redesign', 'Complete overhaul of company website', '2024-01-01', '2024-06-30', 150000.00, 'active'),
('Mobile App Development', 'New mobile app for customer portal', '2024-02-15', '2024-12-31', 300000.00, 'active'),
('Database Migration', 'Migrate legacy database to cloud', '2024-03-01', '2024-08-15', 75000.00, 'active'),
('Security Audit', 'Complete security assessment', '2024-04-01', '2024-05-30', 50000.00, 'completed'),
('AI Implementation', 'Integrate AI tools into workflow', '2024-05-01', '2024-11-30', 200000.00, 'active');

INSERT INTO eshmat (flag, description) VALUES
('Flag{99e8e9b6f8eab1b731002df1bb568a9c}', 'Congratulations! You found the hidden flag through SQL injection'),
('3asyP3asyL3monSqu1zzy', 'it is not flag just ez:)');

CREATE VIEW employee_summary AS
SELECT 
    e.id,
    e.first_name,
    e.last_name,
    e.department,
    e.position,
    e.hire_date,
    e.status
FROM employees e
WHERE e.status = 'active';
