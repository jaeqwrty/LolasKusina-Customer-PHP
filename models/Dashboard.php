<?php
/**
 * Dashboard Model — Read-only dashboard analytics queries.
 *
 * Handles both legacy and normalized order schemas by checking available columns.
 */
require_once __DIR__ . '/../config/DatabaseInterface.php';
require_once __DIR__ . '/../config/DashboardModelInterface.php';

class Dashboard implements DashboardModelInterface {
    private $db;
    private $ordersColumns = null;
    private $usersColumns = null;
    private $orderPaymentsExists = null;

    public function __construct(DatabaseInterface $db) {
        $this->db = $db;
    }

    public function getAdminOverview(): array {
        $metrics = [
            'total_orders' => 0,
            'pending_prep' => 0,
            'total_revenue' => 0.0,
            'active_resellers' => 0,
        ];

        $ordersTable = $this->detectOrdersTable();
        $usersTable = $this->tableExists('users');

        if ($ordersTable) {
            $totalOrdersQuery = "SELECT COUNT(*) AS total_orders FROM {$ordersTable}";
            $totalOrdersResult = $this->db->execute($totalOrdersQuery);
            if (is_array($totalOrdersResult) && !empty($totalOrdersResult[0]['total_orders'])) {
                $metrics['total_orders'] = (int)$totalOrdersResult[0]['total_orders'];
            }

            $statusColumn = $this->columnExists($ordersTable, 'status') ? 'status' : null;
            if ($statusColumn !== null) {
                $pendingResult = $this->db->execute(
                    "SELECT COUNT(*) AS pending_prep FROM {$ordersTable} WHERE LOWER({$statusColumn}) IN (?, ?, ?)",
                    ['in preparation', 'preparing', 'pending approval']
                );
                if (is_array($pendingResult) && !empty($pendingResult[0]['pending_prep'])) {
                    $metrics['pending_prep'] = (int)$pendingResult[0]['pending_prep'];
                }
            }

            $metrics['total_revenue'] = $this->queryRevenueForOrdersTable($ordersTable);
        }

        if ($usersTable && $this->columnExists('users', 'role')) {
            $resellerResult = $this->db->execute(
                "SELECT COUNT(*) AS active_resellers FROM users WHERE role = ? AND is_active = 1",
                ['reseller']
            );
            if (is_array($resellerResult) && !empty($resellerResult[0]['active_resellers'])) {
                $metrics['active_resellers'] = (int)$resellerResult[0]['active_resellers'];
            }
        }

        return $metrics;
    }

    public function getAdminRecentOrders(int $limit = 6): array {
        $ordersTable = $this->detectOrdersTable();
        if (!$ordersTable) {
            return [];
        }

        $limit = max(1, min(20, $limit));
        $isNormalized = $ordersTable === 'orders' && $this->columnExists('orders', 'order_id');

        if ($isNormalized) {
            $result = $this->db->execute(
                "SELECT
                    o.order_id AS id,
                    o.reference_number AS reference_number,
                    CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) AS customer_name,
                    COALESCE(u.role, 'Direct') AS customer_type,
                    o.status AS status,
                    COALESCE(op.grand_total, 0) AS amount,
                    o.created_at AS created_at
                 FROM orders o
                 LEFT JOIN users u ON u.user_id = o.customer_id
                 LEFT JOIN order_payments op ON op.order_id = o.order_id
                 ORDER BY o.created_at DESC
                 LIMIT {$limit}"
            );
            return $this->normalizeRecentOrders($result, true);
        }

        $result = $this->db->execute(
            "SELECT
                o.id AS id,
                CONCAT('#ORD-', DATE_FORMAT(o.created_at, '%y'), '-', LPAD(o.id, 3, '0')) AS reference_number,
                o.customer_name AS customer_name,
                'Direct' AS customer_type,
                o.status AS status,
                COALESCE(o.total, 0) AS amount,
                o.created_at AS created_at
             FROM orders o
             ORDER BY o.created_at DESC
             LIMIT {$limit}"
        );

        return $this->normalizeRecentOrders($result, false);
    }

    public function getResellerOverview(int $resellerId): array {
        $metrics = [
            'my_orders' => 0,
            'pending_orders' => 0,
            'my_revenue' => 0.0,
            'wallet_balance' => 0.0,
        ];

        $ordersTable = $this->detectOrdersTable();
        if ($ordersTable === 'orders' && $this->columnExists('orders', 'reseller_id')) {
            $orderResult = $this->db->execute(
                "SELECT COUNT(*) AS my_orders FROM orders WHERE reseller_id = ?",
                [$resellerId]
            );
            if (is_array($orderResult) && !empty($orderResult[0]['my_orders'])) {
                $metrics['my_orders'] = (int)$orderResult[0]['my_orders'];
            }

            $pendingResult = $this->db->execute(
                "SELECT COUNT(*) AS pending_orders
                 FROM orders
                 WHERE reseller_id = ? AND LOWER(status) IN (?, ?, ?)",
                [$resellerId, 'pending approval', 'approved', 'in preparation']
            );
            if (is_array($pendingResult) && !empty($pendingResult[0]['pending_orders'])) {
                $metrics['pending_orders'] = (int)$pendingResult[0]['pending_orders'];
            }

            if ($this->tableExists('order_payments')) {
                $revenueResult = $this->db->execute(
                    "SELECT COALESCE(SUM(op.grand_total), 0) AS my_revenue
                     FROM orders o
                     LEFT JOIN order_payments op ON op.order_id = o.order_id
                     WHERE o.reseller_id = ?",
                    [$resellerId]
                );
                if (is_array($revenueResult) && isset($revenueResult[0]['my_revenue'])) {
                    $metrics['my_revenue'] = (float)$revenueResult[0]['my_revenue'];
                }
            }
        }

        if ($this->tableExists('reseller_profiles') && $this->columnExists('reseller_profiles', 'wallet_balance')) {
            $walletResult = $this->db->execute(
                "SELECT COALESCE(wallet_balance, 0) AS wallet_balance FROM reseller_profiles WHERE reseller_id = ? LIMIT 1",
                [$resellerId]
            );
            if (is_array($walletResult) && isset($walletResult[0]['wallet_balance'])) {
                $metrics['wallet_balance'] = (float)$walletResult[0]['wallet_balance'];
            }
        }

        return $metrics;
    }

    public function getResellerRecentOrders(int $resellerId, int $limit = 6): array {
        if (!($this->detectOrdersTable() === 'orders' && $this->columnExists('orders', 'reseller_id'))) {
            return [];
        }

        $limit = max(1, min(20, $limit));
        $result = $this->db->execute(
            "SELECT
                o.order_id AS id,
                o.reference_number AS reference_number,
                CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) AS customer_name,
                COALESCE(op.grand_total, 0) AS amount,
                o.status AS status,
                o.created_at AS created_at
             FROM orders o
             LEFT JOIN users u ON u.user_id = o.customer_id
             LEFT JOIN order_payments op ON op.order_id = o.order_id
             WHERE o.reseller_id = ?
             ORDER BY o.created_at DESC
             LIMIT {$limit}",
            [$resellerId]
        );

        if (!is_array($result)) {
            return [];
        }

        return array_map(function($row) {
            $name = trim((string)($row['customer_name'] ?? ''));
            return [
                'reference_number' => $row['reference_number'] ?: ('#ORD-' . date('y') . '-' . str_pad((string)$row['id'], 3, '0', STR_PAD_LEFT)),
                'customer_name' => $name !== '' ? $name : 'Walk-in Customer',
                'customer_type' => 'Reseller Client',
                'status' => $row['status'] ?? 'Pending',
                'amount' => (float)($row['amount'] ?? 0),
                'created_at' => $row['created_at'] ?? null,
            ];
        }, $result);
    }

    private function normalizeRecentOrders($result, bool $normalizedSchema): array {
        if (!is_array($result)) {
            return [];
        }

        return array_map(function($row) use ($normalizedSchema) {
            $name = trim((string)($row['customer_name'] ?? ''));
            $reference = $row['reference_number'] ?? '';
            if ($reference === '' || $reference === null) {
                $id = (int)($row['id'] ?? 0);
                $reference = '#ORD-' . date('y') . '-' . str_pad((string)$id, 3, '0', STR_PAD_LEFT);
            }

            $type = $row['customer_type'] ?? 'Direct';
            if ($normalizedSchema) {
                $type = strtolower((string)$type) === 'reseller' ? 'Via Reseller' : 'Direct';
            }

            return [
                'reference_number' => $reference,
                'customer_name' => $name !== '' ? $name : 'Walk-in Customer',
                'customer_type' => $type,
                'status' => $row['status'] ?? 'Pending',
                'amount' => (float)($row['amount'] ?? 0),
                'created_at' => $row['created_at'] ?? null,
            ];
        }, $result);
    }

    private function queryRevenueForOrdersTable(string $ordersTable): float {
        if ($ordersTable === 'orders' && $this->columnExists('orders', 'order_id') && $this->tableExists('order_payments')) {
            $result = $this->db->execute("SELECT COALESCE(SUM(grand_total), 0) AS total_revenue FROM order_payments");
            if (is_array($result) && isset($result[0]['total_revenue'])) {
                return (float)$result[0]['total_revenue'];
            }
        }

        if ($this->columnExists($ordersTable, 'total')) {
            $result = $this->db->execute("SELECT COALESCE(SUM(total), 0) AS total_revenue FROM {$ordersTable}");
            if (is_array($result) && isset($result[0]['total_revenue'])) {
                return (float)$result[0]['total_revenue'];
            }
        }

        return 0.0;
    }

    private function detectOrdersTable(): ?string {
        if ($this->tableExists('orders')) {
            return 'orders';
        }
        return null;
    }

    private function tableExists(string $table): bool {
        $safeTable = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
        $result = $this->db->execute('SHOW TABLES LIKE ?', [$safeTable]);
        return is_array($result) && count($result) > 0;
    }

    private function columnExists(string $table, string $column): bool {
        $tableColumns = $this->getColumnsForTable($table);
        return in_array($column, $tableColumns, true);
    }

    private function getColumnsForTable(string $table): array {
        if ($table === 'orders' && $this->ordersColumns !== null) {
            return $this->ordersColumns;
        }

        if ($table === 'users' && $this->usersColumns !== null) {
            return $this->usersColumns;
        }

        $result = $this->db->execute('SHOW COLUMNS FROM ' . $table);
        $columns = [];
        if (is_array($result)) {
            foreach ($result as $row) {
                if (!empty($row['Field'])) {
                    $columns[] = $row['Field'];
                }
            }
        }

        if ($table === 'orders') {
            $this->ordersColumns = $columns;
        }
        if ($table === 'users') {
            $this->usersColumns = $columns;
        }

        return $columns;
    }
}
?>