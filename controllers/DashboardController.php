<?php
/**
 * Dashboard Controller — Handles admin and reseller dashboard pages.
 */
require_once __DIR__ . '/../config/UserModelInterface.php';
require_once __DIR__ . '/../config/DashboardModelInterface.php';
require_once __DIR__ . '/../config/AuthGuard.php';

class DashboardController {
    private $userModel;
    private $dashboardModel;
    private $authGuard;

    public function __construct(
        UserModelInterface $userModel,
        DashboardModelInterface $dashboardModel,
        AuthGuard $authGuard
    ) {
        $this->userModel = $userModel;
        $this->dashboardModel = $dashboardModel;
        $this->authGuard = $authGuard;
    }

    public function admin(): void {
        $user = $this->requireAuthenticatedUser();
        if (!$user || ($user['role'] ?? '') !== 'admin') {
            $this->redirectByRole($user['role'] ?? 'customer');
        }

        $overview = $this->dashboardModel->getAdminOverview();

        $dashboardVariant = 'admin';
        $dashboardTitle = 'Dashboard Overview';
        $kpiCards = [
            ['label' => 'Total Orders', 'value' => (string)$overview['total_orders'], 'sub' => 'all-time orders', 'theme' => 'blue'],
            ['label' => 'Pending Prep', 'value' => (string)$overview['pending_prep'], 'sub' => 'needs action', 'theme' => 'amber'],
            ['label' => 'Total Revenue', 'value' => 'P' . number_format($overview['total_revenue'], 2), 'sub' => 'gross sales', 'theme' => 'green'],
            ['label' => 'Active Resellers', 'value' => (string)$overview['active_resellers'], 'sub' => 'active partner accounts', 'theme' => 'violet'],
        ];

        $tableTitle = 'Recent Orders';
        $tableRows = $this->dashboardModel->getAdminRecentOrders(8);
        $currentUserName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));

        include __DIR__ . '/../views/dashboard.php';
    }

    public function reseller(): void {
        $user = $this->requireAuthenticatedUser();
        if (!$user || ($user['role'] ?? '') !== 'reseller') {
            $this->redirectByRole($user['role'] ?? 'customer');
        }

        $overview = $this->dashboardModel->getResellerOverview((int)$user['user_id']);

        $dashboardVariant = 'reseller';
        $dashboardTitle = 'Reseller Dashboard';
        $kpiCards = [
            ['label' => 'My Orders', 'value' => (string)$overview['my_orders'], 'sub' => 'orders you referred', 'theme' => 'blue'],
            ['label' => 'Pending Orders', 'value' => (string)$overview['pending_orders'], 'sub' => 'currently in progress', 'theme' => 'amber'],
            ['label' => 'Sales Value', 'value' => 'P' . number_format($overview['my_revenue'], 2), 'sub' => 'total referred sales', 'theme' => 'green'],
            ['label' => 'Wallet Balance', 'value' => 'P' . number_format($overview['wallet_balance'], 2), 'sub' => 'available earnings', 'theme' => 'violet'],
        ];

        $tableTitle = 'My Recent Referred Orders';
        $tableRows = $this->dashboardModel->getResellerRecentOrders((int)$user['user_id'], 8);
        $currentUserName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));

        include __DIR__ . '/../views/dashboard.php';
    }

    private function requireAuthenticatedUser(): ?array {
        $this->authGuard->requireAuth('/login.php');

        $userId = (int)($_SESSION['user_id'] ?? 0);
        if ($userId <= 0) {
            header('Location: ' . BASE_PATH . '/auth_gate.php');
            exit;
        }

        $user = $this->userModel->findById($userId);
        if (!$user) {
            $_SESSION = [];
            session_destroy();
            header('Location: ' . BASE_PATH . '/auth_gate.php');
            exit;
        }

        $_SESSION['user_role'] = $user['role'] ?? 'customer';

        return $user;
    }

    private function redirectByRole(string $role): void {
        if ($role === 'admin') {
            header('Location: ' . BASE_PATH . '/admin/dashboard.php');
            exit;
        }

        if ($role === 'reseller') {
            header('Location: ' . BASE_PATH . '/reseller/dashboard.php');
            exit;
        }

        header('Location: ' . BASE_PATH . '/index.php');
        exit;
    }
}
?>