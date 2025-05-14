<?php
// Include necessary model files
require_once __DIR__ . '/../../models/Settings.php';

// Get all settings grouped by their group
$generalSettings = Settings::getByGroup('general');
$contentSettings = Settings::getByGroup('content');
$domainSettings = Settings::getByGroup('domain');
$apiSettings = Settings::getByGroup('api');
$emailSettings = Settings::getByGroup('email');

// Handle form submissions
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'update_settings') {
        $group = $_POST['settings_group'];
        $settings = $_POST['settings'];
        
        foreach ($settings as $key => $value) {
            // Get existing setting to determine if it's public
            $existing = Settings::get($key);
            $isPublic = isset($_POST['public'][$key]) ? true : false;
            
            // Update the setting
            Settings::set($key, $value, $group, $isPublic);
        }
        
        $message = "Settings updated successfully";
        
        // Redirect to the same page to refresh the view
        header("Location: settings.php?success=1&group=" . $group);
        exit;
    }
}

// Check if there's a success message from redirect
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $message = "Settings updated successfully";
}

// Determine which settings group to show
$activeGroup = isset($_GET['group']) ? $_GET['group'] : 'general';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Rankolab Admin</title>
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
        .panel {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 30px;
        }
        .tabs {
            display: flex;
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 20px;
        }
        .tab {
            padding: 10px 20px;
            cursor: pointer;
            border: 1px solid transparent;
            border-top-left-radius: 4px;
            border-top-right-radius: 4px;
            margin-bottom: -1px;
        }
        .tab.active {
            border-color: #dee2e6 #dee2e6 #fff;
            background-color: #fff;
            color: #495057;
            font-weight: 500;
        }
        .form-group {
            margin-bottom: 15px;
            padding: 15px;
            border-bottom: 1px solid #f2f2f2;
        }
        .form-group:last-child {
            border-bottom: none;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        .form-group .description {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 10px;
        }
        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            box-sizing: border-box;
        }
        textarea.form-control {
            min-height: 100px;
        }
        .checkbox-label {
            display: flex;
            align-items: center;
            margin-top: 5px;
        }
        .checkbox-label input {
            margin-right: 8px;
        }
        .btn {
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            border: none;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        .btn-primary:hover {
            background-color: #0069d9;
        }
        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .setting-key {
            font-family: monospace;
            font-size: 14px;
            color: #6c757d;
            margin-left: 5px;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <h1>Rankolab Admin Dashboard</h1>
            <div style="display: flex; align-items: center;">
                <div style="margin-right: 20px;">
                    <a href="/" style="color: white; margin-right: 15px;">Home</a>
                    <a href="/docs.php" style="color: white;">API Docs</a>
                </div>
            </div>
        </div>
        <nav>
            <ul>
                <li><a href="/admin/">Dashboard</a></li>
                <li><a href="/admin/licenses.php">Licenses</a></li>
                <li><a href="/admin/users.php">Users</a></li>
                <li><a href="/admin/content.php">Content</a></li>
                <li><a href="/admin/domains.php">Domain Analysis</a></li>
                <li><a href="/admin/settings.php" class="active">Settings</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="panel">
            <h2>System Settings</h2>
            
            <?php if (!empty($message)): ?>
                <div class="alert alert-success">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <div class="tabs">
                <a href="?group=general" class="tab <?php echo $activeGroup === 'general' ? 'active' : ''; ?>">General</a>
                <a href="?group=content" class="tab <?php echo $activeGroup === 'content' ? 'active' : ''; ?>">Content</a>
                <a href="?group=domain" class="tab <?php echo $activeGroup === 'domain' ? 'active' : ''; ?>">Domain</a>
                <a href="?group=api" class="tab <?php echo $activeGroup === 'api' ? 'active' : ''; ?>">API</a>
                <a href="?group=email" class="tab <?php echo $activeGroup === 'email' ? 'active' : ''; ?>">Email</a>
            </div>
            
            <?php
            // Determine which settings to display based on active group
            $activeSettings = [];
            switch ($activeGroup) {
                case 'general':
                    $activeSettings = $generalSettings;
                    break;
                case 'content':
                    $activeSettings = $contentSettings;
                    break;
                case 'domain':
                    $activeSettings = $domainSettings;
                    break;
                case 'api':
                    $activeSettings = $apiSettings;
                    break;
                case 'email':
                    $activeSettings = $emailSettings;
                    break;
            }
            ?>
            
            <form method="post">
                <input type="hidden" name="action" value="update_settings">
                <input type="hidden" name="settings_group" value="<?php echo $activeGroup; ?>">
                
                <?php if (empty($activeSettings)): ?>
                    <p>No settings found for this group. Default values will be used.</p>
                <?php else: ?>
                    <?php foreach ($activeSettings as $setting): ?>
                        <div class="form-group">
                            <label for="<?php echo $setting['setting_key']; ?>">
                                <?php echo ucwords(str_replace('_', ' ', $setting['setting_key'])); ?>
                                <span class="setting-key">(<?php echo $setting['setting_key']; ?>)</span>
                            </label>
                            <?php if (strpos($setting['setting_key'], 'description') !== false || strlen($setting['setting_value']) > 100): ?>
                                <textarea name="settings[<?php echo $setting['setting_key']; ?>]" id="<?php echo $setting['setting_key']; ?>" class="form-control"><?php echo htmlspecialchars($setting['setting_value']); ?></textarea>
                            <?php elseif (in_array($setting['setting_value'], ['true', 'false', '1', '0', 'yes', 'no'])): ?>
                                <select name="settings[<?php echo $setting['setting_key']; ?>]" id="<?php echo $setting['setting_key']; ?>" class="form-control">
                                    <option value="true" <?php echo in_array($setting['setting_value'], ['true', '1', 'yes']) ? 'selected' : ''; ?>>Yes</option>
                                    <option value="false" <?php echo in_array($setting['setting_value'], ['false', '0', 'no']) ? 'selected' : ''; ?>>No</option>
                                </select>
                            <?php else: ?>
                                <input type="text" name="settings[<?php echo $setting['setting_key']; ?>]" id="<?php echo $setting['setting_key']; ?>" class="form-control" value="<?php echo htmlspecialchars($setting['setting_value']); ?>">
                            <?php endif; ?>
                            
                            <div class="checkbox-label">
                                <input type="checkbox" name="public[<?php echo $setting['setting_key']; ?>]" id="public_<?php echo $setting['setting_key']; ?>" <?php echo $setting['is_public'] === 't' ? 'checked' : ''; ?>>
                                <label for="public_<?php echo $setting['setting_key']; ?>">Make publicly accessible</label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <div style="margin-top: 20px;">
                    <button type="submit" class="btn btn-primary">Save <?php echo ucfirst($activeGroup); ?> Settings</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Simple client-side validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const inputs = document.querySelectorAll('input[type="text"], textarea');
            for (let input of inputs) {
                if (input.hasAttribute('required') && !input.value.trim()) {
                    e.preventDefault();
                    alert('Please fill in all required fields');
                    input.focus();
                    return;
                }
            }
        });
    </script>
</body>
</html>