from flask import Flask, request, render_template_string, jsonify
import os
import json

app = Flask(__name__)
SAMPLE_DATA = [
    {"id": 1, "title": "Yangi mahsulotlar", "category": "Mahsulotlar", "content": "Bizning eng yangi mahsulotlarimiz bilan tanishing"},
    {"id": 2, "title": "Xizmatlar", "category": "Xizmatlar", "content": "Professional xizmatlar va maslahatlar"},
    {"id": 3, "title": "Kompaniya haqida", "category": "Ma'lumot", "content": "Bizning kompaniya tarixi va missiyasi"},
    {"id": 4, "title": "Aloqa ma'lumotlari", "category": "Aloqa", "content": "Biz bilan bog'laning va savol bering"},
    {"id": 5, "title": "Yangiliklar", "category": "Yangiliklar", "content": "Eng so'nggi yangiliklar va yangilanishlar"},
]

MAIN_TEMPLATE = '''
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>G'ishmat Korporeyshn</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f8fafc;
            color: #1e293b;
            line-height: 1.6;
        }
        
        .header {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: #38bdf8;
        }
        
        .nav-menu {
            display: flex;
            list-style: none;
            gap: 2rem;
        }
        
        .nav-menu a {
            color: #e2e8f0;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .nav-menu a:hover {
            color: #38bdf8;
        }
        
        .hero {
            background: linear-gradient(135deg, #1e40af, #3b82f6);
            color: white;
            padding: 4rem 2rem;
            text-align: center;
        }
        
        .hero h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .hero p {
            font-size: 1.2rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto 2rem;
        }
        
        .search-container {
            max-width: 800px;
            margin: 0 auto;
            position: relative;
        }
        
        .search-box {
            width: 100%;
            padding: 1rem 1.5rem;
            font-size: 1.1rem;
            border: none;
            border-radius: 50px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            outline: none;
            transition: all 0.3s;
        }
        
        .search-box:focus {
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            transform: translateY(-2px);
        }
        
        .search-btn {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: #1e40af;
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s;
        }
        
        .search-btn:hover {
            background: #1d4ed8;
        }
        
        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 4rem 2rem;
        }
        
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 4rem;
        }
        
        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .feature-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #1e293b;
        }
        
        .search-results {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            margin-top: 2rem;
        }
        
        .result-item {
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
            transition: background 0.3s;
        }
        
        .result-item:hover {
            background: #f8fafc;
        }
        
        .result-item:last-child {
            border-bottom: none;
        }
        
        .result-title {
            font-weight: 600;
            color: #1e40af;
            margin-bottom: 0.5rem;
        }
        
        .result-category {
            background: #e0f2fe;
            color: #0369a1;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.875rem;
            display: inline-block;
            margin-bottom: 0.5rem;
        }
        
        .footer {
            background: #1e293b;
            color: #e2e8f0;
            text-align: center;
            padding: 2rem;
            margin-top: 4rem;
        }
        
        .no-results {
            text-align: center;
            color: #64748b;
            font-style: italic;
            padding: 2rem;
        }
        
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2rem;
            }
            
            .nav-menu {
                display: none;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <nav class="nav-container">
            <div class="logo">G'ishHUB</div>
            <ul class="nav-menu">
                <li><a href="#home">Bosh sahifa</a></li>
                <li><a href="#services">Xizmatlar</a></li>
                <li><a href="#about">Biz haqimizda</a></li>
                <li><a href="#contact">Aloqa</a></li>
            </ul>
        </nav>
    </header>

    <section class="hero">
        <h1>G'ishmat Karpareyshn</h1>
        <p>Biznesingizni rivojlantirish uchun zamonaviy texnologiyalar va professional xizmatlar</p>
        
        <form method="POST" class="search-container">
            <input type="text" name="query" class="search-box" 
                   placeholder="Qidiruv: mahsulotlar, xizmatlar, ma'lumotlar..." 
                   value="{{ query or '' }}">
            <button type="submit" class="search-btn">Qidirish</button>
        </form>
    </section>

    <main class="main-content">
        {% if not query %}
        <div class="features">
            <div class="feature-card">
                <div class="feature-icon">🚀</div>
                <h3 class="feature-title">Tez Yechimlar</h3>
                <p>Biznesingiz uchun tezkor va samarali yechimlar taklif etamiz</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔧</div>
                <h3 class="feature-title">Professional Xizmat</h3>
                <p>Tajribali mutaxassislar tomonidan yuqori sifatli xizmatlar</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3 class="feature-title">Analitika</h3>
                <p>Flag yo'q</p>
            </div>
        </div>
        {% endif %}
        
        {% if query %}
        <div class="search-results">
            <h2>{{ query }}"</h2>
            {% if results %}
                {% for result in results %}
                <div class="result-item">
                    <div class="result-category">{{ result.category }}</div>
                    <div class="result-title">{{ result.title }}</div>
                    <p>{{ result.content }}</p>
                </div>
                {% endfor %}
            {% else %}
                <div class="no-results"></div>
            {% endif %}
        </div>
        {% endif %}
    </main>

    <footer class="footer">
        <p>&copy; 2025 G'ishmatCorp. Hech qanday huquqlar himoyalanmagan.</p>
    </footer>
</body>
</html>
'''

def search_data(query):
    """Search function with potential vulnerability"""
    results = []
    if query:
        for item in SAMPLE_DATA:
            if query.lower() in item['title'].lower() or query.lower() in item['content'].lower() or query.lower() in item['category'].lower():
                results.append(item)
    return results

@app.route('/', methods=['GET', 'POST'])
def index():
    query = None
    results = []
    
    if request.method == 'POST':
        query = request.form.get('query', '')
        results = search_data(query)
        
        template = MAIN_TEMPLATE.replace('{{ query }}', query)
        return render_template_string(template, query=query, results=results)
    
    return render_template_string(MAIN_TEMPLATE)

@app.route('/api/search')
def api_search():
    """API endpoint for search"""
    query = request.args.get('q', '')
    results = search_data(query)
    return jsonify({
        'query': query,
        'results': results,
        'count': len(results)
    })

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)
