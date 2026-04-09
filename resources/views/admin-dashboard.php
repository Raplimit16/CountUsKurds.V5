<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Count Us Kurds Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f3f4f6;
            color: #111827;
        }
        .header {
            background: linear-gradient(135deg, #ed1c24, #ff684f);
            color: white;
            padding: 20px 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }
        .header h1 {
            font-size: 24px;
            font-weight: 800;
        }
        .header-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            border: none;
            transition: opacity 0.2s;
            display: inline-block;
        }
        .btn:hover { opacity: 0.9; }
        .btn-white {
            background: white;
            color: #ed1c24;
        }
        .btn-light {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 24px;
        }
        .alert-success {
            background: #f0fdf4;
            color: #166534;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-weight: 600;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }
        .stat-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .stat-card h3 {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .stat-card .number {
            font-size: 36px;
            font-weight: 800;
            color: #ed1c24;
        }
        
        .filters {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .filters form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
            align-items: end;
        }
        .filter-group label {
            display: block;
            margin-bottom: 6px;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
        }
        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: 10px;
            border: 2px solid #e5e7eb;
            border-radius: 6px;
            font-size: 14px;
        }
        
        .table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            background: #f9fafb;
            padding: 16px;
            text-align: left;
            font-size: 12px;
            font-weight: 700;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 2px solid #e5e7eb;
            white-space: nowrap;
        }
        td {
            padding: 16px;
            border-bottom: 1px solid #f3f4f6;
        }
        tr:hover {
            background: #f9fafb;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }
        .badge-individual {
            background: #dbeafe;
            color: #1e40af;
        }
        .badge-group {
            background: #fef3c7;
            color: #92400e;
        }
        .actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .btn-small {
            padding: 6px 12px;
            font-size: 13px;
            border-radius: 6px;
            white-space: nowrap;
        }
        .btn-view { background: #eff6ff; color: #1e40af; }
        .btn-reply { background: #f0fdf4; color: #166534; }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }
        .empty-state h3 {
            font-size: 20px;
            margin-bottom: 8px;
        }
        @media (max-width: 768px) {
            .header h1 { font-size: 18px; }
            .stats { grid-template-columns: 1fr 1fr; }
            .filters form { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1>📊 <?= $this->t('title') ?></h1>
            <div class="header-actions">
                <select onchange="window.location.href='/admin.php?action=dashboard&lang='+this.value" style="padding:10px;border-radius:8px;border:none;font-weight:600;background:rgba(255,255,255,0.2);color:white;cursor:pointer;">
                    <option value="sv" <?= $this->adminLang === 'sv' ? 'selected' : '' ?>>🇸🇪 Svenska</option>
                    <option value="en" <?= $this->adminLang === 'en' ? 'selected' : '' ?>>🇬🇧 English</option>
                </select>
                <a href="/admin.php?action=export" class="btn btn-white">📥 <?= $this->t('export') ?></a>
                <a href="/" class="btn btn-light" target="_blank">🌐 <?= $this->t('to_website') ?></a>
                <a href="/admin.php?action=logout" class="btn btn-light">🚪 <?= $this->t('logout') ?></a>
            </div>
        </div>
    </div>
    
    <div class="container">
        <?php if (isset($_GET['deleted'])): ?>
            <div class="alert-success">✓ Ansökan raderad</div>
        <?php endif; ?>
        
        <div class="stats">
            <div class="stat-card">
                <h3>Totalt ansökningar</h3>
                <div class="number"><?= $stats['total'] ?></div>
            </div>
            <div class="stat-card">
                <h3>Individuella</h3>
                <div class="number"><?= $stats['individual'] ?></div>
            </div>
            <div class="stat-card">
                <h3>Organisationer</h3>
                <div class="number"><?= $stats['group'] ?></div>
            </div>
            <?php if (isset($stats['today'])): ?>
            <div class="stat-card">
                <h3>Idag</h3>
                <div class="number"><?= $stats['today'] ?></div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="filters">
            <form method="GET">
                <input type="hidden" name="action" value="dashboard">
                
                <div class="filter-group">
                    <label>Typ</label>
                    <select name="filter_type">
                        <option value="all" <?= $filterType === 'all' ? 'selected' : '' ?>>Alla typer</option>
                        <option value="individual" <?= $filterType === 'individual' ? 'selected' : '' ?>>Individuell</option>
                        <option value="group" <?= $filterType === 'group' ? 'selected' : '' ?>>Organisation</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label>Region</label>
                    <select name="filter_region">
                        <option value="all" <?= $filterRegion === 'all' ? 'selected' : '' ?>>Alla regioner</option>
                        <option value="bakur" <?= $filterRegion === 'bakur' ? 'selected' : '' ?>>Bakur</option>
                        <option value="rojava" <?= $filterRegion === 'rojava' ? 'selected' : '' ?>>Rojava</option>
                        <option value="rojhilat" <?= $filterRegion === 'rojhilat' ? 'selected' : '' ?>>Rojhilat</option>
                        <option value="bashur" <?= $filterRegion === 'bashur' ? 'selected' : '' ?>>Bashur</option>
                        <option value="europe" <?= $filterRegion === 'europe' ? 'selected' : '' ?>>Europa</option>
                        <option value="na" <?= $filterRegion === 'na' ? 'selected' : '' ?>>Nordamerika</option>
                        <option value="other" <?= $filterRegion === 'other' ? 'selected' : '' ?>>Annat</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label>Sök</label>
                    <input type="text" name="search" placeholder="Namn eller email..." value="<?= htmlspecialchars($search) ?>">
                </div>
                
                <button type="submit" class="btn btn-white">Filtrera</button>
            </form>
        </div>
        
        <div class="table-container">
            <?php if (count($applications) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Namn</th>
                            <th>Email</th>
                            <th>Typ</th>
                            <th>Region</th>
                            <th>Hantera</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $app): ?>
                            <tr>
                                <td>#<?= $app['id'] ?></td>
                                <td><strong><?= htmlspecialchars($app['name']) ?></strong></td>
                                <td><?= htmlspecialchars($app['email']) ?></td>
                                <td>
                                    <span class="badge badge-<?= $app['application_type'] ?>">
                                        <?= $app['application_type'] === 'individual' ? 'Individuell' : 'Organisation' ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars(ucfirst($app['region'])) ?></td>
                                <td>
                                    <div class="actions">
                                        <a href="/admin.php?action=view&id=<?= $app['id'] ?>" class="btn btn-small btn-view">👁️ Visa</a>
                                        <a href="/admin.php?action=reply&id=<?= $app['id'] ?>" class="btn btn-small btn-reply">📧 Svara</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <h3>Inga ansökningar hittades</h3>
                    <p>Prova att ändra filter eller sökning</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
