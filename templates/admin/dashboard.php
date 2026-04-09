<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> - Count Us Kurds Admin</title>
    <style>
        :root {
            --primary: #ED1C24;
            --primary-dark: #c41920;
            --green: #21924F;
            --yellow: #F9DD16;
            --bg: #0f0f1a;
            --sidebar-bg: #1a1a2e;
            --card-bg: #1a1a2e;
            --text: #ffffff;
            --text-muted: rgba(255,255,255,0.6);
            --border: rgba(255,255,255,0.1);
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }
        
        /* Layout */
        .layout {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar */
        .sidebar {
            width: 260px;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border);
            position: fixed;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border);
        }
        
        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--text);
            text-decoration: none;
        }
        
        .sidebar-logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--primary), var(--green));
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .sidebar-logo span {
            font-weight: 700;
            font-size: 1.1rem;
        }
        
        .sidebar-nav {
            flex: 1;
            padding: 1rem 0;
            overflow-y: auto;
        }
        
        .nav-section {
            padding: 0 1rem;
            margin-bottom: 1.5rem;
        }
        
        .nav-section-title {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            padding: 0.5rem 1rem;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: var(--text-muted);
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 0.25rem;
            transition: all 0.2s;
        }
        
        .nav-link:hover {
            background: rgba(255,255,255,0.05);
            color: var(--text);
        }
        
        .nav-link.active {
            background: rgba(237,28,36,0.1);
            color: var(--primary);
        }
        
        .nav-link .icon {
            width: 20px;
            text-align: center;
        }
        
        .sidebar-footer {
            padding: 1rem;
            border-top: 1px solid var(--border);
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            border-radius: 8px;
            background: rgba(255,255,255,0.05);
        }
        
        .user-avatar {
            width: 36px;
            height: 36px;
            background: var(--green);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        
        .user-details {
            flex: 1;
        }
        
        .user-name {
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .user-role {
            font-size: 0.75rem;
            color: var(--text-muted);
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 260px;
            min-height: 100vh;
        }
        
        .top-bar {
            background: var(--sidebar-bg);
            padding: 1rem 2rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        .top-actions {
            display: flex;
            gap: 1rem;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
        }
        
        .btn-secondary {
            background: rgba(255,255,255,0.1);
            color: var(--text);
            border: 1px solid var(--border);
        }
        
        .btn-secondary:hover {
            background: rgba(255,255,255,0.15);
        }
        
        /* Dashboard Content */
        .dashboard-content {
            padding: 2rem;
        }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.5rem;
        }
        
        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }
        
        .stat-icon.red { background: rgba(237,28,36,0.1); }
        .stat-icon.green { background: rgba(33,146,79,0.1); }
        .stat-icon.yellow { background: rgba(249,221,22,0.1); }
        .stat-icon.blue { background: rgba(59,130,246,0.1); }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
        }
        
        .stat-label {
            color: var(--text-muted);
            font-size: 0.875rem;
        }
        
        /* Tables */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-title {
            font-weight: 600;
            font-size: 1rem;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th {
            text-align: left;
            padding: 1rem 1.5rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            background: rgba(255,255,255,0.02);
        }
        
        .table td {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--border);
            font-size: 0.875rem;
        }
        
        .table tr:hover {
            background: rgba(255,255,255,0.02);
        }
        
        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .badge-pending { background: rgba(249,221,22,0.1); color: var(--yellow); }
        .badge-approved { background: rgba(33,146,79,0.1); color: #69db7c; }
        .badge-rejected { background: rgba(237,28,36,0.1); color: #ff6b6b; }
        .badge-reviewed { background: rgba(59,130,246,0.1); color: #74b9ff; }
        
        /* Charts placeholder */
        .chart-container {
            padding: 2rem;
            text-align: center;
            color: var(--text-muted);
        }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .sidebar { width: 80px; }
            .sidebar-logo span, .nav-section-title, .nav-link span, .user-details { display: none; }
            .main-content { margin-left: 80px; }
            .nav-link { justify-content: center; }
        }
        
        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main-content { margin-left: 0; }
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="/admin" class="sidebar-logo">
                    <div class="sidebar-logo-icon">☀️</div>
                    <span>Count Us Kurds</span>
                </a>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Main</div>
                    <a href="/admin?action=dashboard" class="nav-link active">
                        <span class="icon">📊</span>
                        <span>Dashboard</span>
                    </a>
                    <a href="/admin?action=applications" class="nav-link">
                        <span class="icon">📋</span>
                        <span>Applications</span>
                    </a>
                    <a href="/admin?action=statistics" class="nav-link">
                        <span class="icon">📈</span>
                        <span>Statistics</span>
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Admin</div>
                    <a href="/admin?action=activity" class="nav-link">
                        <span class="icon">📝</span>
                        <span>Activity Log</span>
                    </a>
                    <a href="/admin?action=settings" class="nav-link">
                        <span class="icon">⚙️</span>
                        <span>Settings</span>
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Security</div>
                    <a href="/admin?action=change-password" class="nav-link">
                        <span class="icon">🔑</span>
                        <span>Change Password</span>
                    </a>
                    <a href="/admin?action=change-totp" class="nav-link">
                        <span class="icon">📱</span>
                        <span>Change 2FA</span>
                    </a>
                </div>
            </nav>
            
            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="user-avatar"><?= strtoupper(substr($user['username'] ?? 'A', 0, 1)) ?></div>
                    <div class="user-details">
                        <div class="user-name"><?= e($user['username'] ?? 'Admin') ?></div>
                        <div class="user-role"><?= e(ucfirst($user['role'] ?? 'admin')) ?></div>
                    </div>
                </div>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <div class="top-bar">
                <h1 class="page-title"><?= e($pageTitle) ?></h1>
                <div class="top-actions">
                    <a href="/admin?action=export" class="btn btn-secondary">📥 Export CSV</a>
                    <a href="/" target="_blank" class="btn btn-secondary">🌐 View Site</a>
                    <a href="/admin?action=logout" class="btn btn-primary">Logout</a>
                </div>
            </div>
            
            <div class="dashboard-content">
                <!-- Stats -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon red">📋</div>
                        </div>
                        <div class="stat-value"><?= number_format($stats['total']) ?></div>
                        <div class="stat-label">Total Applications</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon yellow">⏳</div>
                        </div>
                        <div class="stat-value"><?= number_format($stats['pending']) ?></div>
                        <div class="stat-label">Pending Review</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon green">✅</div>
                        </div>
                        <div class="stat-value"><?= number_format($stats['approved']) ?></div>
                        <div class="stat-label">Approved</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon blue">📅</div>
                        </div>
                        <div class="stat-value"><?= number_format($stats['today']) ?></div>
                        <div class="stat-label">Today</div>
                    </div>
                </div>
                
                <!-- Recent Applications -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Recent Applications</h2>
                        <a href="/admin?action=applications" class="btn btn-secondary">View All</a>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Country</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentApplications)): ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; color: var(--text-muted);">
                                        No applications yet
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentApplications as $app): ?>
                                    <tr>
                                        <td>
                                            <a href="/admin?action=application-view&id=<?= $app['id'] ?>" style="color: var(--text); text-decoration: none;">
                                                <?= e($app['full_name']) ?>
                                            </a>
                                        </td>
                                        <td><?= e(ucfirst($app['application_type'])) ?></td>
                                        <td><?= e($app['country']) ?></td>
                                        <td>
                                            <span class="badge badge-<?= e($app['status']) ?>">
                                                <?= e(ucfirst($app['status'])) ?>
                                            </span>
                                        </td>
                                        <td><?= date('M j, Y', strtotime($app['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- By Country -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Top Countries</h2>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Country</th>
                                <th>Applications</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($byCountry)): ?>
                                <tr>
                                    <td colspan="2" style="text-align: center; color: var(--text-muted);">
                                        No data yet
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($byCountry as $row): ?>
                                    <tr>
                                        <td><?= e($row['country']) ?></td>
                                        <td><?= number_format($row['count']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
