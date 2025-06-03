###CTF Challenge: Employee Portal SQL Injection
##Challenge Description
#Welcome to a modern Employee Management Portal. Hidden within the application is a critical SQL injection vulnerability. Your mission is to identify and exploit this weakness to uncover sensitive data and hidden secrets within the system.

Difficulty Level: Easy
Category: Web Exploitation - SQL Injection

Setup Instructions
1. Requirements
Docker

Docker Compose

2. Build and Run

# Clone the repository
git clone <repository-url>

# Start the Docker containers
docker-compose up -d

# Open the application in your browser
http://localhost:8080
3. Challenge Environment
Web Application: http://localhost:8080

Database: MySQL 8.0 (port 3306)

Technologies: PHP 8.1, Apache, Bootstrap 5

Challenge Details
Objective
There are multiple hidden secrets within the system, including flags placed for discovery through SQL injection and advanced techniques.

Features
Modern UI: Built with Bootstrap 5 for a clean interface

Interactive Tables: DataTables.js for user-friendly interactions

Multiple Tabs: Includes Dashboard, Employees, Projects, and Advanced Search

Hidden Vulnerability: The SQL injection vulnerability is concealed in the Advanced Search tab

Database Schema Overview

- users       (admin accounts)
- employees   (employee data)
- projects    (project-related information)
- secrets     (contains hidden flags)
Hints for Participants
Start Simple: Try basic search inputs and observe how the system reacts

Error Messages: Analyze errors for insights into the database structure

UNION Attacks: Consider using SQL UNION techniques to extract more data

Information Schema: Leverage information_schema to enumerate database internals

Table Exploration: Multiple tables exist; investigate them thoroughly

Advanced Techniques: Try blind or time-based SQL injection approaches if necessary

Scoring
Basic SQL Injection Detection: 25 points

Database Structure Enumeration: 25 points

Primary Flag Retrieval: 100 points

Bonus Discovery: 50 points

Advanced Exploitation (Blind/Time-based): 25 points

Learning Objectives
By completing this challenge, participants will learn:
SQL Injection Basics: How to identify and exploit injectable parameters

UNION-based Attacks: Extracting data from other tables

Database Enumeration: Using information_schema to understand the DB structure

Payload Crafting: Constructing various SQL injection payloads

Web App Security Awareness: Understanding vulnerabilities in modern web applications

Vulnerability Overview
The application includes insecure code that concatenates user input directly into SQL queries without proper validation or prepared statements, leading to a severe SQL injection vulnerability.

Why It’s Vulnerable:

Direct input concatenation

No sanitization or escaping

Lack of prepared statements

Potential Impact:

Unauthorized access to sensitive data

Disclosure of admin information and internal secrets

Potential for full database compromise

Deployment Notes
For Event Organizers:
Isolated Containers: Run a separate instance per team for isolation

Monitoring: Enable logs to track progress or detect issues

Reset Scripts: Provide an easy way to reset the environment between sessions

Reset Environment:

docker-compose down
docker-compose up -d
Change Default Port:

export WEBAPP_PORT=8081
docker-compose up -d
Educational Resources
Recommended Reading:
OWASP SQL Injection Prevention Cheat Sheet

PortSwigger Web Security Academy - SQL Injection

SANS SQL Injection Fundamentals

Practice Platforms:
SQLi-Labs

DVWA (Damn Vulnerable Web Application)

bWAPP (Buggy Web Application)

Technical Details
Security Posture (Intentionally Weak for Educational Purposes):
Error Handling: Some database errors may be visible to users

Input Validation: Minimal, mostly absent

Permissions: The web application user has limited access (read-only for realism)

Challenge Complexity Levels:
Beginner: Basic detection and UNION injections

Intermediate: Enumeration of tables and columns

Advanced: Blind/time-based and deeper exploitation

Database Design Notes:
Realistic employee data

Complex joins with projects

Hidden secrets table with embedded challenges

Multiple discovery paths

Troubleshooting
Common Issues:
Port Conflicts: If port 8080 is in use, change it in docker-compose.yml

Container Readiness: Wait a few seconds after up -d for containers to fully initialize

Permission Errors: Ensure Docker has write permissions to project files

Helpful Debugging Commands:

# View logs
docker-compose logs web
docker-compose logs db

# Connect directly to the MySQL database
docker exec -it <container_name> mysql -u webapp -p employee_portal

# Access the web container shell
docker exec -it <container_name> bash
Author
Created by: Boboor
Contact: t.me/realbobur
Version: 1.0
Last Updated: June 2025
