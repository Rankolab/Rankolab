<?php
// Set page title
$pageTitle = 'AI Agent';

// Include header
require_once __DIR__ . '/includes/header.php';

// Check if user is super admin
if (!$isSuperAdmin) {
    // Not authorized, redirect to dashboard
    header('Location: /admin/');
    exit;
}

// Handle AI prompt submission
$response = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['prompt'])) {
    $prompt = trim($_POST['prompt']);
    
    if (!empty($prompt)) {
        // In a real implementation, this would call an actual AI service
        // For now, we'll simulate responses based on keywords
        
        if (stripos($prompt, 'server') !== false || stripos($prompt, 'system') !== false) {
            $response = [
                'type' => 'server_info',
                'message' => "System diagnostics report:\n\nServer CPU: 42% utilization\nMemory: 6.2GB / 16GB (38.75%)\nDisk: 124GB / 512GB (24.2%)\nNetwork: Stable, 42ms latency\n\nAll services operational. No critical issues detected."
            ];
        } elseif (stripos($prompt, 'database') !== false) {
            $response = [
                'type' => 'database_info',
                'message' => "Database metrics:\n\nConnections: 12 active / 100 max\nSlow queries detected: 2\nStorage: 856MB\nIndexes: Optimal\n\nRecommendation: Consider optimizing the query in content.php line 142 which is taking >500ms to execute."
            ];
        } elseif (stripos($prompt, 'api') !== false) {
            $response = [
                'type' => 'api_info',
                'message' => "API Status:\n\nEndpoints: 32 active\nAverage response time: 246ms\nError rate: 0.8%\n\nTop endpoints by usage:\n1. /api/license/verify (42%)\n2. /api/content/generate (28%)\n3. /api/domain/analyze (15%)"
            ];
        } elseif (stripos($prompt, 'error') !== false || stripos($prompt, 'fix') !== false || stripos($prompt, 'problem') !== false) {
            $response = [
                'type' => 'error_fix',
                'message' => "I've detected and fixed the following issues:\n\n1. Corrected permissions on upload directory\n2. Fixed missing index in domain_keywords table\n3. Cleared expired cache entries\n\nAll systems now operational. Consider implementing automatic cleanup for the cache directory."
            ];
        } elseif (stripos($prompt, 'backup') !== false) {
            $response = [
                'type' => 'backup_info',
                'message' => "Backup status:\n\nLast successful backup: Today at 03:00 AM\nBackup size: 2.4GB\nBackup retention: 14 days\n\nI can initiate a manual backup if needed. Would you like me to proceed?"
            ];
        } elseif (stripos($prompt, 'user') !== false || stripos($prompt, 'client') !== false) {
            $response = [
                'type' => 'user_info',
                'message' => "User metrics:\n\nTotal users: 1,247\nActive in last 24h: 342\nNew signups today: 18\n\nUser distribution by plan:\n- Pro: 68%\n- Business: 22%\n- Enterprise: 10%"
            ];
        } else {
            $response = [
                'type' => 'general',
                'message' => "I'm here to help with system maintenance and troubleshooting. You can ask me about server status, database performance, API metrics, error resolution, backups, or user statistics. Is there something specific you'd like assistance with?"
            ];
        }
    }
}

// Get chat history (would be from database in a real implementation)
$chatHistory = [
    [
        'sender' => 'admin',
        'message' => 'Check system status',
        'timestamp' => strtotime('-1 hour')
    ],
    [
        'sender' => 'ai',
        'message' => "All systems operational.\n\nCPU: 38% utilization\nMemory: 5.8GB / 16GB\nDisk: 124GB / 512GB\nNetwork: Stable\n\nNo critical issues detected.",
        'timestamp' => strtotime('-1 hour')
    ],
    [
        'sender' => 'admin',
        'message' => 'Are there any slow database queries?',
        'timestamp' => strtotime('-45 minutes')
    ],
    [
        'sender' => 'ai',
        'message' => "I've identified 2 slow queries:\n\n1. SELECT * FROM domain_analyses WHERE created_at > ? ORDER BY id DESC\n2. SELECT * FROM content_generations WHERE user_id = ? AND status = 'completed'\n\nRecommendation: Add indexes for these query patterns to improve performance.",
        'timestamp' => strtotime('-45 minutes')
    ]
];

// Add new response to chat history if exists
if ($response) {
    array_push($chatHistory, [
        'sender' => 'admin',
        'message' => $prompt,
        'timestamp' => time()
    ]);
    
    array_push($chatHistory, [
        'sender' => 'ai',
        'message' => $response['message'],
        'timestamp' => time()
    ]);
}

?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title">AI System Agent</h1>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h6>AI Assistant Chat</h6>
            </div>
            <div class="card-body">
                <div class="ai-agent-chat" id="chatContainer">
                    <?php foreach ($chatHistory as $chat): ?>
                        <div class="chat-message <?php echo $chat['sender'] === 'admin' ? 'user' : 'ai'; ?>">
                            <div class="chat-avatar">
                                <?php if ($chat['sender'] === 'admin'): ?>
                                    <i class="fas fa-user"></i>
                                <?php else: ?>
                                    <i class="fas fa-robot"></i>
                                <?php endif; ?>
                            </div>
                            <div class="chat-content">
                                <div class="chat-name">
                                    <?php echo $chat['sender'] === 'admin' ? 'You' : 'Rankolab AI'; ?>
                                    <span class="chat-time">
                                        <?php echo date('h:i A', $chat['timestamp']); ?>
                                    </span>
                                </div>
                                <div class="chat-text">
                                    <?php echo nl2br(htmlspecialchars($chat['message'])); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <form method="post" class="ai-agent-input">
                    <input type="text" name="prompt" placeholder="Ask the AI assistant for help..." required>
                    <button type="submit">
                        <i class="fas fa-paper-plane"></i> Send
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6>AI Capabilities</h6>
            </div>
            <div class="card-body">
                <div class="ai-capabilities">
                    <div class="capability-item">
                        <div class="capability-icon">
                            <i class="fas fa-server"></i>
                        </div>
                        <div class="capability-text">
                            <h5>System Diagnostics</h5>
                            <p>Monitor and diagnose server health, performance, and resource usage.</p>
                        </div>
                    </div>
                    
                    <div class="capability-item">
                        <div class="capability-icon">
                            <i class="fas fa-database"></i>
                        </div>
                        <div class="capability-text">
                            <h5>Database Optimization</h5>
                            <p>Identify slow queries, suggest indexes, and optimize database performance.</p>
                        </div>
                    </div>
                    
                    <div class="capability-item">
                        <div class="capability-icon">
                            <i class="fas fa-code"></i>
                        </div>
                        <div class="capability-text">
                            <h5>API Monitoring</h5>
                            <p>Track API performance, error rates, and usage patterns.</p>
                        </div>
                    </div>
                    
                    <div class="capability-item">
                        <div class="capability-icon">
                            <i class="fas fa-wrench"></i>
                        </div>
                        <div class="capability-text">
                            <h5>Automated Fixes</h5>
                            <p>Automatically detect and resolve common system issues.</p>
                        </div>
                    </div>
                    
                    <div class="capability-item">
                        <div class="capability-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="capability-text">
                            <h5>Security Monitoring</h5>
                            <p>Detect suspicious activities and enforce security best practices.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h6>System Status</h6>
            </div>
            <div class="card-body">
                <div class="system-status">
                    <div class="status-item">
                        <div class="status-label">CPU Usage</div>
                        <div class="status-bar">
                            <div class="status-progress" style="width: 42%;"></div>
                        </div>
                        <div class="status-value">42%</div>
                    </div>
                    
                    <div class="status-item">
                        <div class="status-label">Memory Usage</div>
                        <div class="status-bar">
                            <div class="status-progress" style="width: 68%;"></div>
                        </div>
                        <div class="status-value">68%</div>
                    </div>
                    
                    <div class="status-item">
                        <div class="status-label">Disk Usage</div>
                        <div class="status-bar">
                            <div class="status-progress" style="width: 35%;"></div>
                        </div>
                        <div class="status-value">35%</div>
                    </div>
                    
                    <div class="status-item">
                        <div class="status-label">Database Connections</div>
                        <div class="status-bar">
                            <div class="status-progress" style="width: 28%;"></div>
                        </div>
                        <div class="status-value">28%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .ai-agent-chat {
        height: 400px;
        overflow-y: auto;
        padding: 1rem;
        background-color: #f8f9fc;
        border-radius: 0.35rem;
        margin-bottom: 1rem;
    }
    
    .chat-message {
        display: flex;
        margin-bottom: 1rem;
    }
    
    .chat-message.user {
        flex-direction: row-reverse;
    }
    
    .chat-avatar {
        width: 2.5rem;
        height: 2.5rem;
        background-color: #4e73df;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
    }
    
    .chat-message.user .chat-avatar {
        margin-right: 0;
        margin-left: 1rem;
        background-color: #1cc88a;
    }
    
    .chat-content {
        max-width: 70%;
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        background-color: white;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        border: 1px solid #e3e6f0;
    }
    
    .chat-message.user .chat-content {
        background-color: #4e73df;
        color: white;
    }
    
    .chat-name {
        font-weight: 600;
        margin-bottom: 0.25rem;
        font-size: 0.85rem;
    }
    
    .chat-message.user .chat-name {
        color: rgba(255, 255, 255, 0.9);
    }
    
    .chat-time {
        font-weight: 400;
        font-size: 0.75rem;
        color: #858796;
        margin-left: 0.5rem;
    }
    
    .chat-message.user .chat-time {
        color: rgba(255, 255, 255, 0.7);
    }
    
    .chat-text {
        font-size: 0.9rem;
        line-height: 1.4;
        white-space: pre-wrap;
    }
    
    .ai-agent-input {
        display: flex;
        margin-top: 1rem;
    }
    
    .ai-agent-input input {
        flex: 1;
        padding: 0.75rem 1rem;
        border: 1px solid #d1d3e2;
        border-radius: 0.35rem 0 0 0.35rem;
        font-size: 0.9rem;
    }
    
    .ai-agent-input button {
        padding: 0 1.5rem;
        background-color: #4e73df;
        color: white;
        border: none;
        border-radius: 0 0.35rem 0.35rem 0;
    }
    
    .capability-item {
        display: flex;
        margin-bottom: 1.25rem;
    }
    
    .capability-icon {
        width: 2.5rem;
        height: 2.5rem;
        background-color: rgba(78, 115, 223, 0.1);
        color: #4e73df;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
    }
    
    .capability-text h5 {
        font-size: 1rem;
        margin: 0 0 0.25rem 0;
        font-weight: 600;
    }
    
    .capability-text p {
        font-size: 0.85rem;
        color: #858796;
        margin: 0;
    }
    
    .status-item {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }
    
    .status-label {
        width: 35%;
        font-size: 0.85rem;
        font-weight: 500;
    }
    
    .status-bar {
        width: 45%;
        height: 0.5rem;
        background-color: #eaecf4;
        border-radius: 0.25rem;
        margin-right: 1rem;
        overflow: hidden;
    }
    
    .status-progress {
        height: 100%;
        background-color: #4e73df;
    }
    
    .status-value {
        width: 15%;
        font-size: 0.85rem;
        text-align: right;
        font-weight: 600;
    }
</style>

<script>
    // Scroll chat to bottom on page load
    document.addEventListener('DOMContentLoaded', function() {
        const chatContainer = document.getElementById('chatContainer');
        chatContainer.scrollTop = chatContainer.scrollHeight;
    });
</script>

<?php
// Include footer
require_once __DIR__ . '/includes/footer.php';
?>