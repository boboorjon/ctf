#!/bin/bash
echo "🚀 Starting CTF SQL Injection Challenge Setup..."

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' 

set -e

error_exit() {
    echo -e "${RED}❌ Error: $1${NC}" >&2
    exit 1
}

success_msg() {
    echo -e "${GREEN}✅ $1${NC}"
}

info_msg() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

warning_msg() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

check_docker() {
    info_msg "Checking Docker installation..."
    if ! command -v docker &> /dev/null; then
        error_exit "Docker is not installed. Please install Docker first."
    fi
    
    if ! command -v docker-compose &> /dev/null; then
        error_exit "Docker Compose is not installed. Please install Docker Compose first."
    fi
    
    success_msg "Docker and Docker Compose are installed"
}

check_ports() {
    info_msg "Checking if required ports are available..."
    
    if lsof -Pi :8080 -sTCP:LISTEN -t >/dev/null ; then
        warning_msg "Port 8080 is already in use. Stopping existing services..."
        docker-compose down 2>/dev/null || true
        sleep 2
    fi
    
    if lsof -Pi :3306 -sTCP:LISTEN -t >/dev/null ; then
        warning_msg "Port 3306 is already in use. This might cause conflicts."
        read -p "Do you want to continue? (y/N): " -n 1 -r
        echo
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            exit 1
        fi
    fi
    
    success_msg "Ports are available"
}

create_structure() {
    info_msg "Creating project structure..."
    mkdir -p src
  
    cat > src/.htaccess << 'EOF'
# Security headers
Header always set X-Frame-Options "SAMEORIGIN"
Header always set X-Content-Type-Options "nosniff"
Header always set X-XSS-Protection "1; mode=block"

# PHP security
php_flag display_errors Off
php_flag log_errors On

# Directory browsing off
Options -Indexes

# File access restrictions
<FilesMatch "\.(sql|md|log|conf)$">
    Require all denied
</FilesMatch>
EOF
    
    success_msg "Project structure created"
}

create_challenge_info() {
    info_msg "Creating challenge information..."
    
    cat > CHALLENGE_INFO.txt << 'EOF'
=================================
CTF SQL INJECTION CHALLENGE
=================================

🎯 Target: http://localhost:8080

📋 Challenge Details:
- Type: SQL Injection
- Difficulty: Easy  
- Points: 200 (Primary) + 50 (Bonus)

🔍 Objectives:
1. Find and exploit SQL injection vulnerability
2. Discover database structure
3. Extract hidden flags from secrets table


⚠️  Note: This is an educational challenge. Do not use these techniques on real systems without permission.

🛠️  Reset Database: docker-compose restart db
🔄 Restart Challenge: docker-compose restart
🛑 Stop Challenge: docker-compose down

Good luck! 🚀
EOF
    
    success_msg "Challenge info created"
}

build_images() {
    info_msg "Building Docker images..."
    
    echo "This may take a few minutes on first run..."
    docker-compose build --no-cache
    
    success_msg "Docker images built successfully"
}

start_database() {
    info_msg "Starting database..."
    
    docker-compose up -d db
    
    echo "Waiting for database to be ready..."
    for i in {1..30}; do
        if docker-compose exec -T db mysql -uwebapp -pwebapp123 -e "SELECT 1" employee_portal &>/dev/null; then
            success_msg "Database is ready"
            return 0
        fi
        echo -n "."
        sleep 2
    done
    
    error_exit "Database failed to start properly"
}

start_webserver() {
    info_msg "Starting web server..."
    
    docker-compose up -d web
    
    echo "Waiting for web server to be ready..."
    for i in {1..20}; do
        if curl -s http://localhost:8080 &>/dev/null; then
            success_msg "Web server is ready"
            return 0
        fi
        echo -n "."
        sleep 2
    done
    
    error_exit "Web server failed to start properly"
}

run_tests() {
    info_msg "Running final tests..."
    
    if ! curl -s http://localhost:8080 | grep -q "Employee Management Portal"; then
        error_exit "Web application is not responding correctly"
    fi
    
    if ! docker-compose exec -T db mysql -uwebapp -pwebapp123 -e "SELECT COUNT(*) FROM employees" employee_portal &>/dev/null; then
        error_exit "Database connection test failed"
    fi
    
    if curl -s "http://localhost:8080/?search=test%27&active_tab=search" | grep -q "Search error"; then
        success_msg "SQL injection vulnerability is present (as expected)"
    fi
    
    success_msg "All tests passed"
}

cleanup() {
    info_msg "Cleaning up..."
    docker-compose down &>/dev/null || true
}

main() {
    echo -e "${BLUE}"
    cat << 'EOF'
╔══════════════════════════════════════════════════════════════╗
║                   CTF SQL INJECTION CHALLENGE               ║
║                     Setup & Build Script                    ║
╚══════════════════════════════════════════════════════════════╝
EOF
    echo -e "${NC}"
    
    check_docker
    check_ports
    create_structure
    create_challenge_info
    
    info_msg "Building and starting challenge environment..."
    build_images
    start_database
    start_webserver
    run_tests
    
    echo
    echo -e "${GREEN}🎉 CTF Challenge Successfully Deployed!${NC}"
    echo
    echo -e "${BLUE}📍 Challenge URL: ${YELLOW}http://localhost:8080${NC}"
    echo -e "${BLUE}🗄️  Database: ${YELLOW}localhost:3306${NC}"
    echo -e "${BLUE}📁 Admin Panel: ${YELLOW}Not available (part of challenge)${NC}"
    echo
    echo -e "${BLUE}📖 Read CHALLENGE_INFO.txt for detailed instructions${NC}"
    echo -e "${BLUE}📋 Check README.md for complete documentation${NC}"
    echo
    echo -e "${YELLOW}⚠️  Remember: This is for educational purposes only!${NC}"
    echo
    
    echo -e "${BLUE}🐳 Container Status:${NC}"
    docker-compose ps
    
    echo
    echo -e "${GREEN}Happy Hacking! 🚀${NC}"
}

if [[ "${BASH_SOURCE[0]}" == "${0}" ]]; then
    trap cleanup EXIT
    main "$@"
fi
