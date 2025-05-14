<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Rankolab</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        header {
            background-color: #343a40;
            color: white;
            padding: 20px 0;
        }
        .header-content {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        h1 {
            margin: 0;
            font-size: 28px;
        }
        nav a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            font-size: 16px;
        }
        nav a:hover {
            text-decoration: underline;
        }
        .main-content {
            flex-grow: 1;
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .hero {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            padding: 40px;
            margin-bottom: 40px;
            text-align: center;
        }
        .hero h2 {
            font-size: 32px;
            margin-top: 0;
            color: #343a40;
        }
        .hero p {
            font-size: 18px;
            color: #6c757d;
            margin-bottom: 30px;
        }
        .button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 12px 24px;
            font-size: 16px;
            border-radius: 4px;
            text-decoration: none;
            margin: 0 10px;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #0069d9;
        }
        .button.secondary {
            background-color: #6c757d;
        }
        .button.secondary:hover {
            background-color: #5a6268;
        }
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }
        .feature-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            text-align: center;
        }
        .feature-card h3 {
            font-size: 20px;
            margin-top: 20px;
            margin-bottom: 15px;
            color: #343a40;
        }
        .feature-card p {
            color: #6c757d;
            margin-bottom: 0;
        }
        .feature-icon {
            font-size: 48px;
            color: #007bff;
        }
        footer {
            background-color: #343a40;
            color: white;
            padding: 20px 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <h1>Rankolab</h1>
            <nav>
                <a href="/">Home</a>
                <a href="/docs.php">API Documentation</a>
                <a href="/admin.php">Admin Dashboard</a>
                <a href="/api">API Endpoints</a>
            </nav>
        </div>
    </header>

    <div class="main-content">
        <div class="hero">
            <h2>Welcome to Rankolab Backend</h2>
            <p>The ultimate SEO and content management platform with powerful API capabilities.</p>
            <div>
                <a href="/docs.php" class="button">View API Documentation</a>
                <a href="/admin.php" class="button secondary">Access Admin Dashboard</a>
            </div>
        </div>

        <div class="features">
            <div class="feature-card">
                <div class="feature-icon">📝</div>
                <h3>Content Generation</h3>
                <p>Generate SEO-optimized content, check for plagiarism, and assess readability with our powerful API.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔍</div>
                <h3>Domain Analysis</h3>
                <p>Analyze domains for SEO performance, retrieve keyword rankings, and explore backlink profiles.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔑</div>
                <h3>License Management</h3>
                <p>Easily validate, activate, and deactivate licenses for seamless integration with your applications.</p>
            </div>
        </div>

        <div class="hero">
            <h2>Getting Started</h2>
            <p>To start using the Rankolab API, check out our comprehensive documentation and explore the admin dashboard.</p>
            <div>
                <a href="/api/content/generate" class="button">Generate Sample Content</a>
                <a href="/api/domain/analyze" class="button secondary">Analyze Domain</a>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2023 Rankolab. All rights reserved.</p>
    </footer>
</body>
</html>