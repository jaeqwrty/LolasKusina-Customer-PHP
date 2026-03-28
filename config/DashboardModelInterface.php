<?php
/**
 * Dashboard Model Interface — DIP abstraction for backoffice dashboards.
 */
interface DashboardModelInterface {
    /**
     * Return aggregate metrics for the admin dashboard.
     *
     * @return array
     */
    public function getAdminOverview(): array;

    /**
     * Return latest orders visible in admin dashboard.
     *
     * @param int $limit
     * @return array
     */
    public function getAdminRecentOrders(int $limit = 6): array;

    /**
     * Return reseller-specific metrics.
     *
     * @param int $resellerId
     * @return array
     */
    public function getResellerOverview(int $resellerId): array;

    /**
     * Return latest orders for a reseller.
     *
     * @param int $resellerId
     * @param int $limit
     * @return array
     */
    public function getResellerRecentOrders(int $resellerId, int $limit = 6): array;
}
?>