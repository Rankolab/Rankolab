<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rankolab API Documentation</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 20px;
        }
        header {
            background-color: #343a40;
            color: white;
            padding: 15px 0;
            margin-bottom: 30px;
        }
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
            max-width: 1100px;
            margin: 0 auto;
        }
        h1 {
            margin: 0;
            font-size: 24px;
        }
        h2 {
            color: #343a40;
            margin-top: 30px;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #dee2e6;
        }
        h3 {
            color: #495057;
            margin-top: 25px;
            margin-bottom: 15px;
        }
        p {
            margin-bottom: 15px;
        }
        .sidebar {
            position: sticky;
            top: 20px;
            width: 250px;
            padding-right: 30px;
        }
        .main-content {
            flex: 1;
        }
        .content-wrapper {
            display: flex;
        }
        .api-section {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            padding: 25px;
        }
        .endpoint {
            margin-bottom: 30px;
            border-left: 4px solid #007bff;
            padding-left: 15px;
        }
        .endpoint h3 {
            margin-top: 0;
            margin-bottom: 10px;
            color: #007bff;
            font-size: 18px;
        }
        .endpoint-url {
            background-color: #f8f9fa;
            padding: 10px 15px;
            border-radius: 4px;
            font-family: Consolas, Monaco, 'Andale Mono', monospace;
            margin-bottom: 15px;
        }
        .method {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            color: white;
            font-weight: bold;
            font-size: 14px;
            margin-right: 10px;
        }
        .method.post {
            background-color: #28a745;
        }
        .method.get {
            background-color: #007bff;
        }
        .method.put {
            background-color: #fd7e14;
        }
        .method.delete {
            background-color: #dc3545;
        }
        pre {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            font-family: Consolas, Monaco, 'Andale Mono', monospace;
            margin: 15px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        table th {
            font-weight: 600;
            background-color: #f8f9fa;
        }
        .nav-list {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }
        .nav-list li {
            margin-bottom: 10px;
        }
        .nav-list li a {
            color: #495057;
            text-decoration: none;
            display: block;
            padding: 8px 10px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .nav-list li a:hover {
            background-color: #e9ecef;
        }
        .nav-list li a.active {
            background-color: #007bff;
            color: white;
        }
        .nav-section {
            margin-bottom: 20px;
        }
        .nav-section-title {
            font-weight: 600;
            color: #343a40;
            margin-bottom: 10px;
            display: block;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <h1>Rankolab API Documentation</h1>
            <div>
                <a href="/" style="color: white; margin-right: 15px;">Home</a>
                <a href="/admin.php" style="color: white;">Admin Dashboard</a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="content-wrapper">
            <div class="sidebar">
                <div class="nav-section">
                    <span class="nav-section-title">Getting Started</span>
                    <ul class="nav-list">
                        <li><a href="#introduction" class="active">Introduction</a></li>
                        <li><a href="#authentication">Authentication</a></li>
                        <li><a href="#errors">Error Handling</a></li>
                    </ul>
                </div>
                
                <div class="nav-section">
                    <span class="nav-section-title">API Reference</span>
                    <ul class="nav-list">
                        <li><a href="#content-generation">Content Generation</a></li>
                        <li><a href="#domain-analysis">Domain Analysis</a></li>
                        <li><a href="#license-management">License Management</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="main-content">
                <div id="introduction" class="api-section">
                    <h2>Introduction</h2>
                    <p>
                        Welcome to the Rankolab API documentation. Rankolab is a comprehensive SEO and content management platform that provides tools for content generation, plagiarism checking, readability assessment, domain analysis, and more.
                    </p>
                    <p>
                        This documentation will guide you through using the Rankolab API to integrate these powerful features into your own applications.
                    </p>
                    <h3>Base URL</h3>
                    <p>
                        All API endpoints are relative to the base URL:
                    </p>
                    <div class="endpoint-url">
                        https://api.rankolab.com
                    </div>
                    <p>
                        For testing and development, you can use:
                    </p>
                    <div class="endpoint-url">
                        http://localhost:5000
                    </div>
                </div>
                
                <div id="authentication" class="api-section">
                    <h2>Authentication</h2>
                    <p>
                        To access the Rankolab API, you need to include your license key in every request. There are two ways to authenticate:
                    </p>
                    <h3>API Key in Header</h3>
                    <p>
                        Include your license key in the <code>X-API-Key</code> header:
                    </p>
                    <pre>X-API-Key: RANKO-PRO-1234-5678-9ABC</pre>
                    <h3>API Key in Request Body</h3>
                    <p>
                        Include your license key in the request body as <code>licenseKey</code>:
                    </p>
                    <pre>{
  "licenseKey": "RANKO-PRO-1234-5678-9ABC",
  "domain": "example.com",
  ...
}</pre>
                </div>
                
                <div id="errors" class="api-section">
                    <h2>Error Handling</h2>
                    <p>
                        The Rankolab API uses conventional HTTP response codes to indicate the success or failure of an API request. In general:
                    </p>
                    <ul>
                        <li>2xx range indicates success</li>
                        <li>4xx range indicates an error that failed given the information provided (e.g., a required parameter was missing)</li>
                        <li>5xx range indicates an error with Rankolab's servers</li>
                    </ul>
                    <h3>Error Response Format</h3>
                    <p>
                        All error responses follow this format:
                    </p>
                    <pre>{
  "success": false,
  "error": "Error Type",
  "message": "A human-readable description of the error"
}</pre>
                    <h3>Common Error Codes</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>400</td>
                                <td>Bad Request - Invalid parameters or missing required fields.</td>
                            </tr>
                            <tr>
                                <td>401</td>
                                <td>Unauthorized - Invalid or expired license key.</td>
                            </tr>
                            <tr>
                                <td>403</td>
                                <td>Forbidden - The license key doesn't have permission for this operation.</td>
                            </tr>
                            <tr>
                                <td>404</td>
                                <td>Not Found - The requested resource doesn't exist.</td>
                            </tr>
                            <tr>
                                <td>429</td>
                                <td>Too Many Requests - Rate limit exceeded.</td>
                            </tr>
                            <tr>
                                <td>500</td>
                                <td>Internal Server Error - Something went wrong on our end.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div id="content-generation" class="api-section">
                    <h2>Content Generation</h2>
                    <p>
                        The Content Generation API allows you to generate SEO-optimized content, check for plagiarism, and assess readability.
                    </p>
                    
                    <div class="endpoint">
                        <h3>Generate Content</h3>
                        <div class="endpoint-url">
                            <span class="method post">POST</span> /api/content/generate
                        </div>
                        <p>
                            Generates SEO-optimized content based on the provided topic and keywords.
                        </p>
                        <h4>Request</h4>
                        <pre>{
  "licenseKey": "RANKO-PRO-1234-5678-9ABC",
  "topic": "SEO Best Practices",
  "keywords": ["keyword research", "on-page SEO", "backlinks", "content optimization"],
  "wordCount": 1500,
  "toneOfVoice": "professional",
  "targetAudience": "marketing professionals"
}</pre>
                        <h4>Response</h4>
                        <pre>{
  "success": true,
  "content": "The topic of SEO Best Practices has been gaining significant attention in recent years...",
  "statistics": {
    "wordCount": 1523,
    "readabilityScore": 75,
    "keywordDensity": {
      "keyword research": {
        "count": 12,
        "density": 0.78
      },
      "on-page SEO": {
        "count": 15,
        "density": 0.98
      },
      "backlinks": {
        "count": 8,
        "density": 0.52
      },
      "content optimization": {
        "count": 10,
        "density": 0.65
      }
    }
  }
}</pre>
                    </div>
                    
                    <div class="endpoint">
                        <h3>Check Plagiarism</h3>
                        <div class="endpoint-url">
                            <span class="method post">POST</span> /api/content/check-plagiarism
                        </div>
                        <p>
                            Checks content for potential plagiarism against web sources.
                        </p>
                        <h4>Request</h4>
                        <pre>{
  "licenseKey": "RANKO-PRO-1234-5678-9ABC",
  "content": "The content you want to check for plagiarism..."
}</pre>
                        <h4>Response</h4>
                        <pre>{
  "success": true,
  "plagiarismScore": 3.2,
  "matches": [
    {
      "text": "This segment of text appears to be similar to another source",
      "source": "https://example.com/article1",
      "matchPercentage": 95
    },
    {
      "text": "Another potentially plagiarized segment",
      "source": "https://blog.example.com/seo-tips",
      "matchPercentage": 85
    }
  ]
}</pre>
                    </div>
                    
                    <div class="endpoint">
                        <h3>Check Readability</h3>
                        <div class="endpoint-url">
                            <span class="method post">POST</span> /api/content/check-readability
                        </div>
                        <p>
                            Analyzes content for readability and provides improvement suggestions.
                        </p>
                        <h4>Request</h4>
                        <pre>{
  "licenseKey": "RANKO-PRO-1234-5678-9ABC",
  "content": "The content you want to check for readability..."
}</pre>
                        <h4>Response</h4>
                        <pre>{
  "success": true,
  "scores": {
    "fleschKincaid": 68.5,
    "smog": 8.2,
    "colemanLiau": 10.1,
    "automatedReadability": 9.6,
    "overallGrade": "B"
  },
  "suggestions": [
    "Use shorter sentences in the third paragraph.",
    "Consider simplifying vocabulary in the introduction.",
    "Add more transition words between paragraphs."
  ]
}</pre>
                    </div>
                </div>
                
                <div id="domain-analysis" class="api-section">
                    <h2>Domain Analysis</h2>
                    <p>
                        The Domain Analysis API allows you to analyze domains for SEO performance, retrieve keyword rankings, and explore backlinks.
                    </p>
                    
                    <div class="endpoint">
                        <h3>Analyze Domain</h3>
                        <div class="endpoint-url">
                            <span class="method post">POST</span> /api/domain/analyze
                        </div>
                        <p>
                            Performs a comprehensive SEO analysis of a domain.
                        </p>
                        <h4>Request</h4>
                        <pre>{
  "licenseKey": "RANKO-PRO-1234-5678-9ABC",
  "domain": "example.com",
  "includeCompetitors": true
}</pre>
                        <h4>Response</h4>
                        <pre>{
  "success": true,
  "domain": "example.com",
  "analysis": {
    "domainAuthority": 45,
    "pageAuthority": 38,
    "spamScore": 2,
    "performanceMetrics": {
      "loadTime": "2.3s",
      "mobileCompatibility": "Good",
      "pagespeedScore": 85
    },
    "seoIssues": {
      "missingAltTags": 12,
      "brokenLinks": 3,
      "duplicateContent": 2,
      "missingMetaDescriptions": 5
    },
    "competitorComparison": {
      "competitorA.com": {
        "domainAuthority": 52,
        "commonKeywords": 145
      },
      "competitorB.com": {
        "domainAuthority": 38,
        "commonKeywords": 98
      }
    }
  }
}</pre>
                    </div>
                    
                    <div class="endpoint">
                        <h3>Get Keywords</h3>
                        <div class="endpoint-url">
                            <span class="method get">GET</span> /api/domain/keywords/{domain}
                        </div>
                        <p>
                            Retrieves keyword rankings for a specific domain.
                        </p>
                        <h4>Parameters</h4>
                        <table>
                            <thead>
                                <tr>
                                    <th>Parameter</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>domain</td>
                                    <td>The domain to retrieve keywords for (e.g., example.com)</td>
                                </tr>
                            </tbody>
                        </table>
                        <h4>Response</h4>
                        <pre>{
  "success": true,
  "domain": "example.com",
  "keywords": [
    {
      "keyword": "seo tools",
      "position": 12,
      "searchVolume": 5400,
      "difficulty": 68,
      "cpc": 4.20
    },
    {
      "keyword": "content optimization",
      "position": 8,
      "searchVolume": 2900,
      "difficulty": 54,
      "cpc": 3.80
    }
  ],
  "topPerformingPage": "https://example.com/blog/seo-strategies-2023",
  "suggestedKeywords": [
    "seo ranking factors",
    "on-page optimization",
    "technical seo guide",
    "seo competitive analysis",
    "local seo strategy"
  ]
}</pre>
                    </div>
                    
                    <div class="endpoint">
                        <h3>Get Backlinks</h3>
                        <div class="endpoint-url">
                            <span class="method get">GET</span> /api/domain/backlinks/{domain}
                        </div>
                        <p>
                            Retrieves backlink data for a specific domain.
                        </p>
                        <h4>Parameters</h4>
                        <table>
                            <thead>
                                <tr>
                                    <th>Parameter</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>domain</td>
                                    <td>The domain to retrieve backlinks for (e.g., example.com)</td>
                                </tr>
                            </tbody>
                        </table>
                        <h4>Response</h4>
                        <pre>{
  "success": true,
  "domain": "example.com",
  "backlinksOverview": {
    "totalBacklinks": 1832,
    "uniqueDomains": 246,
    "dofollow": 1254,
    "nofollow": 578,
    "averageDomainAuthority": 42
  },
  "topBacklinks": [
    {
      "source": "example-blog.com",
      "targetUrl": "https://example.com/features",
      "anchorText": "best SEO analysis tool",
      "domainAuthority": 68,
      "dofollow": true,
      "firstSeen": "2023-02-15"
    }
  ],
  "backlinksGrowth": {
    "lastMonth": 87,
    "last3Months": 234,
    "last6Months": 512
  }
}</pre>
                    </div>
                </div>
                
                <div id="license-management" class="api-section">
                    <h2>License Management</h2>
                    <p>
                        The License Management API allows you to validate, activate, and deactivate licenses.
                    </p>
                    
                    <div class="endpoint">
                        <h3>Validate License</h3>
                        <div class="endpoint-url">
                            <span class="method post">POST</span> /api/license/validate
                        </div>
                        <p>
                            Validates a license key and returns its details.
                        </p>
                        <h4>Request</h4>
                        <pre>{
  "licenseKey": "RANKO-PRO-1234-5678-9ABC",
  "domain": "example.com"
}</pre>
                        <h4>Response</h4>
                        <pre>{
  "success": true,
  "licenseDetails": {
    "licenseKey": "RANKO-PRO-1234-5678-9ABC",
    "plan": "pro",
    "status": "active",
    "expiryDate": "2023-12-31",
    "maxDomains": 10,
    "activeDomains": 3,
    "domainStatus": "active"
  }
}</pre>
                    </div>
                    
                    <div class="endpoint">
                        <h3>Activate License</h3>
                        <div class="endpoint-url">
                            <span class="method post">POST</span> /api/license/activate
                        </div>
                        <p>
                            Activates a license for a specific domain.
                        </p>
                        <h4>Request</h4>
                        <pre>{
  "licenseKey": "RANKO-PRO-1234-5678-9ABC",
  "domain": "example.com",
  "email": "user@example.com"
}</pre>
                        <h4>Response</h4>
                        <pre>{
  "success": true,
  "message": "License successfully activated for example.com",
  "activationDetails": {
    "licenseKey": "RANKO-PRO-1234-5678-9ABC",
    "domain": "example.com",
    "activationDate": "2023-05-14",
    "activationId": "ACT-230514-a1b2c3d4e5",
    "expiryDate": "2023-12-31"
  }
}</pre>
                    </div>
                    
                    <div class="endpoint">
                        <h3>Deactivate License</h3>
                        <div class="endpoint-url">
                            <span class="method post">POST</span> /api/license/deactivate
                        </div>
                        <p>
                            Deactivates a license for a specific domain.
                        </p>
                        <h4>Request</h4>
                        <pre>{
  "licenseKey": "RANKO-PRO-1234-5678-9ABC",
  "domain": "example.com"
}</pre>
                        <h4>Response</h4>
                        <pre>{
  "success": true,
  "message": "License successfully deactivated for example.com",
  "deactivationDetails": {
    "licenseKey": "RANKO-PRO-1234-5678-9ABC",
    "domain": "example.com",
    "deactivationDate": "2023-05-14"
  }
}</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>