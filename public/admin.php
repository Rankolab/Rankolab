<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rankolab Admin Dashboard</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
        }
        .container {
            max-width: 1200px;
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
        }
        h1 {
            margin: 0;
            font-size: 24px;
        }
        .user-info {
            display: flex;
            align-items: center;
        }
        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }
        nav {
            background-color: #495057;
            padding: 10px 20px;
        }
        nav ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
        }
        nav ul li {
            margin-right: 20px;
        }
        nav ul li a {
            color: #fff;
            text-decoration: none;
            font-size: 16px;
        }
        nav ul li a:hover {
            text-decoration: underline;
        }
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            display: flex;
            flex-direction: column;
        }
        .stat-card h3 {
            margin-top: 0;
            color: #495057;
            font-size: 16px;
        }
        .stat-card .value {
            font-size: 32px;
            font-weight: bold;
            margin: 10px 0;
            color: #212529;
        }
        .stat-card .change {
            font-size: 14px;
            color: #6c757d;
        }
        .change.positive {
            color: #28a745;
        }
        .change.negative {
            color: #dc3545;
        }
        .tab-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        .tabs {
            display: flex;
            border-bottom: 1px solid #dee2e6;
        }
        .tab {
            padding: 15px 20px;
            cursor: pointer;
            font-weight: 500;
            color: #6c757d;
            border-bottom: 3px solid transparent;
        }
        .tab.active {
            color: #007bff;
            border-bottom-color: #007bff;
        }
        .tab-content {
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        table th {
            font-weight: 600;
            color: #495057;
            background-color: #f8f9fa;
        }
        table tr:hover {
            background-color: #f8f9fa;
        }
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 500;
        }
        .badge-success {
            background-color: #d4edda;
            color: #28a745;
        }
        .badge-warning {
            background-color: #fff3cd;
            color: #ffc107;
        }
        .badge-danger {
            background-color: #f8d7da;
            color: #dc3545;
        }
        .actions {
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            border: none;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <h1>Rankolab Admin Dashboard</h1>
            <div class="user-info">
                <img src="https://via.placeholder.com/40" alt="Admin">
                <span>Administrator</span>
            </div>
        </div>
        <nav>
            <ul>
                <li><a href="#" class="active">Dashboard</a></li>
                <li><a href="#">Licenses</a></li>
                <li><a href="#">Content</a></li>
                <li><a href="#">Domain Analysis</a></li>
                <li><a href="#">Users</a></li>
                <li><a href="#">Settings</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="stats-container">
            <div class="stat-card">
                <h3>Active Licenses</h3>
                <div class="value">327</div>
                <div class="change positive">+12% from last month</div>
            </div>
            <div class="stat-card">
                <h3>Content Generations</h3>
                <div class="value">5,842</div>
                <div class="change positive">+23% from last month</div>
            </div>
            <div class="stat-card">
                <h3>Domain Analyses</h3>
                <div class="value">892</div>
                <div class="change positive">+8% from last month</div>
            </div>
            <div class="stat-card">
                <h3>Revenue</h3>
                <div class="value">$28,450</div>
                <div class="change positive">+15% from last month</div>
            </div>
        </div>

        <div class="tab-container">
            <div class="tabs">
                <div class="tab active" onclick="switchTab('recent-licenses')">Recent Licenses</div>
                <div class="tab" onclick="switchTab('content-requests')">Content Requests</div>
                <div class="tab" onclick="switchTab('domain-analyses')">Domain Analyses</div>
            </div>
            <div id="recent-licenses" class="tab-content">
                <table>
                    <thead>
                        <tr>
                            <th>License Key</th>
                            <th>Plan</th>
                            <th>User</th>
                            <th>Activated</th>
                            <th>Expires</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>RANKO-PRO-1234-5678-9ABC</td>
                            <td>Pro</td>
                            <td>john.doe@example.com</td>
                            <td>2023-04-15</td>
                            <td>2023-12-31</td>
                            <td><span class="badge badge-success">Active</span></td>
                            <td class="actions">
                                <button class="btn btn-primary">Edit</button>
                                <button class="btn btn-danger">Revoke</button>
                            </td>
                        </tr>
                        <tr>
                            <td>RANKO-BUSINESS-2345-6789-ABCD</td>
                            <td>Business</td>
                            <td>jane.smith@company.org</td>
                            <td>2023-03-22</td>
                            <td>2024-06-30</td>
                            <td><span class="badge badge-success">Active</span></td>
                            <td class="actions">
                                <button class="btn btn-primary">Edit</button>
                                <button class="btn btn-danger">Revoke</button>
                            </td>
                        </tr>
                        <tr>
                            <td>RANKO-ENTERPRISE-3456-7890-BCDE</td>
                            <td>Enterprise</td>
                            <td>michael.jones@enterprise.com</td>
                            <td>2023-02-18</td>
                            <td>2025-01-15</td>
                            <td><span class="badge badge-success">Active</span></td>
                            <td class="actions">
                                <button class="btn btn-primary">Edit</button>
                                <button class="btn btn-danger">Revoke</button>
                            </td>
                        </tr>
                        <tr>
                            <td>RANKO-PRO-EXPIRED-1234-5678</td>
                            <td>Pro</td>
                            <td>robert.williams@example.net</td>
                            <td>2022-08-05</td>
                            <td>2023-01-31</td>
                            <td><span class="badge badge-danger">Expired</span></td>
                            <td class="actions">
                                <button class="btn btn-primary">Edit</button>
                                <button class="btn btn-success">Renew</button>
                            </td>
                        </tr>
                        <tr>
                            <td>RANKO-STARTER-4567-8901-CDEF</td>
                            <td>Starter</td>
                            <td>sarah.miller@startup.co</td>
                            <td>2023-05-02</td>
                            <td>2023-08-02</td>
                            <td><span class="badge badge-warning">Expiring Soon</span></td>
                            <td class="actions">
                                <button class="btn btn-primary">Edit</button>
                                <button class="btn btn-success">Renew</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div id="content-requests" class="tab-content" style="display: none;">
                <table>
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>User</th>
                            <th>Topic</th>
                            <th>Word Count</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>CR-2023-05001</td>
                            <td>john.doe@example.com</td>
                            <td>SEO Best Practices 2023</td>
                            <td>1,500</td>
                            <td>2023-05-12</td>
                            <td><span class="badge badge-success">Completed</span></td>
                            <td class="actions">
                                <button class="btn btn-primary">View</button>
                            </td>
                        </tr>
                        <tr>
                            <td>CR-2023-05002</td>
                            <td>jane.smith@company.org</td>
                            <td>Content Marketing Strategies</td>
                            <td>2,200</td>
                            <td>2023-05-11</td>
                            <td><span class="badge badge-success">Completed</span></td>
                            <td class="actions">
                                <button class="btn btn-primary">View</button>
                            </td>
                        </tr>
                        <tr>
                            <td>CR-2023-05003</td>
                            <td>michael.jones@enterprise.com</td>
                            <td>Technical SEO Guide</td>
                            <td>3,000</td>
                            <td>2023-05-10</td>
                            <td><span class="badge badge-success">Completed</span></td>
                            <td class="actions">
                                <button class="btn btn-primary">View</button>
                            </td>
                        </tr>
                        <tr>
                            <td>CR-2023-05004</td>
                            <td>sarah.miller@startup.co</td>
                            <td>Local SEO Optimization</td>
                            <td>1,800</td>
                            <td>2023-05-09</td>
                            <td><span class="badge badge-success">Completed</span></td>
                            <td class="actions">
                                <button class="btn btn-primary">View</button>
                            </td>
                        </tr>
                        <tr>
                            <td>CR-2023-05005</td>
                            <td>robert.williams@example.net</td>
                            <td>E-commerce SEO Guide</td>
                            <td>2,500</td>
                            <td>2023-05-09</td>
                            <td><span class="badge badge-warning">Processing</span></td>
                            <td class="actions">
                                <button class="btn btn-primary">View</button>
                                <button class="btn btn-danger">Cancel</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div id="domain-analyses" class="tab-content" style="display: none;">
                <table>
                    <thead>
                        <tr>
                            <th>Analysis ID</th>
                            <th>User</th>
                            <th>Domain</th>
                            <th>Date</th>
                            <th>Domain Authority</th>
                            <th>Issues</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>DA-2023-05001</td>
                            <td>john.doe@example.com</td>
                            <td>example.com</td>
                            <td>2023-05-12</td>
                            <td>45</td>
                            <td>12</td>
                            <td class="actions">
                                <button class="btn btn-primary">View Report</button>
                            </td>
                        </tr>
                        <tr>
                            <td>DA-2023-05002</td>
                            <td>jane.smith@company.org</td>
                            <td>company.org</td>
                            <td>2023-05-11</td>
                            <td>52</td>
                            <td>8</td>
                            <td class="actions">
                                <button class="btn btn-primary">View Report</button>
                            </td>
                        </tr>
                        <tr>
                            <td>DA-2023-05003</td>
                            <td>michael.jones@enterprise.com</td>
                            <td>enterprise.com</td>
                            <td>2023-05-10</td>
                            <td>67</td>
                            <td>5</td>
                            <td class="actions">
                                <button class="btn btn-primary">View Report</button>
                            </td>
                        </tr>
                        <tr>
                            <td>DA-2023-05004</td>
                            <td>sarah.miller@startup.co</td>
                            <td>startup.co</td>
                            <td>2023-05-09</td>
                            <td>32</td>
                            <td>18</td>
                            <td class="actions">
                                <button class="btn btn-primary">View Report</button>
                            </td>
                        </tr>
                        <tr>
                            <td>DA-2023-05005</td>
                            <td>robert.williams@example.net</td>
                            <td>example.net</td>
                            <td>2023-05-09</td>
                            <td>38</td>
                            <td>15</td>
                            <td class="actions">
                                <button class="btn btn-primary">View Report</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tabId) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.style.display = 'none';
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show the selected tab content
            document.getElementById(tabId).style.display = 'block';
            
            // Set the clicked tab as active
            document.querySelector(`.tab[onclick="switchTab('${tabId}')"]`).classList.add('active');
        }
    </script>
</body>
</html>