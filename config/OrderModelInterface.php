<?php
/**
 * Order Model Interface — DIP abstraction
 * 
 * Controllers depend on this interface, not the concrete Order model.
 */
interface OrderModelInterface {
    /**
     * @param array $data Order data
     * @return int|false   Order ID or false on failure
     */
    public function createOrder($data);

    /**
     * @param int   $orderId Order ID
     * @param array $items   Cart items
     * @return bool
     */
    public function addOrderItems($orderId, $items);

    /**
     * @param int $id Order ID
     * @return array|null
     */
    public function getOrderById($id);

    /**
     * @param string $referenceNumber Reference number
     * @return array|null
     */
    public function getOrderByReferenceNumber($referenceNumber);

    /**
     * @param int $orderId Order ID
     * @return array
     */
    public function getOrderItems($orderId);

    /**
     * @param int $customerId Customer user ID
     * @return array
     */
    public function getCustomerOrders($customerId);

    /**
     * @param int    $orderId Order ID
     * @param string $status  New status
     * @return bool
     */
    public function updateOrderStatus($orderId, $status);

    /**
     * Get customer orders with payment and first item details (for profile).
     *
     * @param int $customerId Customer user ID
     * @return array
     */
    public function getCustomerOrdersWithDetails(int $customerId): array;
}
?>
