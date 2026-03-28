<?php
$nowLabel = date('M d, Y');
$displayName = trim($currentUserName ?? '');
if ($displayName === '') {
    $displayName = 'Backoffice User';
}

$menuGroups = [
    'admin' => [
        [
            'title' => 'Operations',
            'items' => [
                ['label' => 'Dashboard', 'href' => BASE_PATH . '/admin/dashboard.php', 'active' => true],
                ['label' => 'Orders', 'href' => '#', 'active' => false],
                ['label' => 'Kitchen List', 'href' => '#', 'active' => false],
            ],
        ],
        [
            'title' => 'Management',
            'items' => [
                ['label' => 'Menu', 'href' => '#', 'active' => false],
                ['label' => 'Resellers', 'href' => '#', 'active' => false],
                ['label' => 'Testimonials', 'href' => '#', 'active' => false],
            ],
        ],
        [
            'title' => 'System',
            'items' => [
                ['label' => 'Analytics', 'href' => '#', 'active' => false],
                ['label' => 'Settings', 'href' => '#', 'active' => false],
            ],
        ],
    ],
    'reseller' => [
        [
            'title' => 'Workspace',
            'items' => [
                ['label' => 'Dashboard', 'href' => BASE_PATH . '/reseller/dashboard.php', 'active' => true],
                ['label' => 'My Orders', 'href' => '#', 'active' => false],
                ['label' => 'Leads', 'href' => '#', 'active' => false],
            ],
        ],
        [
            'title' => 'Earnings',
            'items' => [
                ['label' => 'Commissions', 'href' => '#', 'active' => false],
                ['label' => 'Wallet', 'href' => '#', 'active' => false],
            ],
        ],
        [
            'title' => 'Profile',
            'items' => [
                ['label' => 'Account', 'href' => '#', 'active' => false],
                ['label' => 'Support', 'href' => '#', 'active' => false],
            ],
        ],
    ],
];

$statusClassMap = [
    'approved' => 'status-pill status-approved',
    'in prep' => 'status-pill status-prep',
    'in preparation' => 'status-pill status-prep',
    'pending' => 'status-pill status-pending',
    'pending approval' => 'status-pill status-pending',
    'completed' => 'status-pill status-approved',
    'cancelled' => 'status-pill status-cancelled',
    'rejected' => 'status-pill status-cancelled',
];

function dashboardStatusClass(string $status, array $statusClassMap): string {
    $key = strtolower(trim($status));
    return $statusClassMap[$key] ?? 'status-pill status-prep';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($dashboardTitle); ?> - Lola's Kusina</title>
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/css/dashboard.css">
</head>
<body>
    <div class="backoffice-shell">
        <aside class="sidebar">
            <div class="sidebar-brand">
                <span class="brand-icon">L</span>
                <div>
                    <p class="brand-title">Lola's Kusina</p>
                    <p class="brand-subtitle"><?php echo $dashboardVariant === 'admin' ? 'Admin' : 'Reseller'; ?> Panel</p>
                </div>
            </div>

            <nav class="sidebar-nav">
                <?php foreach ($menuGroups[$dashboardVariant] as $group): ?>
                    <p class="group-title"><?php echo strtoupper(htmlspecialchars($group['title'])); ?></p>
                    <?php foreach ($group['items'] as $item): ?>
                        <a href="<?php echo htmlspecialchars($item['href']); ?>" class="nav-link<?php echo $item['active'] ? ' active' : ''; ?>">
                            <span class="dot"></span>
                            <?php echo htmlspecialchars($item['label']); ?>
                        </a>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </nav>

            <a class="sign-out" href="<?php echo BASE_PATH; ?>/logout.php">Sign Out</a>
        </aside>

        <main class="dashboard-main">
            <header class="dashboard-topbar">
                <div>
                    <p class="crumbs">Home &gt; Dashboard</p>
                    <h1><?php echo htmlspecialchars($dashboardTitle); ?></h1>
                </div>
                <div class="topbar-right">
                    <p class="today-label">Today: <?php echo htmlspecialchars($nowLabel); ?></p>
                    <a class="new-order" href="<?php echo BASE_PATH; ?>/cart.php">+ New Order</a>
                </div>
            </header>

            <section class="welcome-row">
                <p class="welcome-user">Signed in as <?php echo htmlspecialchars($displayName); ?></p>
            </section>

            <section class="kpi-grid">
                <?php foreach ($kpiCards as $card): ?>
                    <article class="kpi-card">
                        <p class="kpi-label"><?php echo htmlspecialchars($card['label']); ?></p>
                        <p class="kpi-value"><?php echo htmlspecialchars($card['value']); ?></p>
                        <p class="kpi-sub <?php echo htmlspecialchars($card['theme']); ?>"><?php echo htmlspecialchars($card['sub']); ?></p>
                    </article>
                <?php endforeach; ?>
            </section>

            <section class="orders-section">
                <div class="orders-header">
                    <h2><?php echo htmlspecialchars($tableTitle); ?></h2>
                    <span>View All</span>
                </div>

                <div class="orders-table-wrap">
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>Ref #</th>
                                <th>Customer</th>
                                <th>Status</th>
                                <th>Type</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($tableRows)): ?>
                                <?php foreach ($tableRows as $row): ?>
                                    <tr>
                                        <td class="order-ref"><?php echo htmlspecialchars($row['reference_number']); ?></td>
                                        <td>
                                            <p class="customer-name"><?php echo htmlspecialchars($row['customer_name']); ?></p>
                                            <p class="customer-type"><?php echo htmlspecialchars($row['customer_type']); ?></p>
                                        </td>
                                        <td>
                                            <span class="<?php echo dashboardStatusClass($row['status'] ?? '', $statusClassMap); ?>">
                                                <?php echo htmlspecialchars($row['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($dashboardVariant === 'admin' ? 'Food Order' : 'Referral'); ?></td>
                                        <td class="amount">P<?php echo number_format((float)$row['amount'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="empty-state">No orders yet. Orders will appear here once transactions are recorded.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</body>
</html>