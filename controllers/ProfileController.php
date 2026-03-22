<?php
/**
 * Profile Controller — SRP: handles profile viewing and updating only
 * 
 * DIP: Depends on UserModelInterface, OrderModelInterface, AuthGuard — no concrete classes.
 */
require_once __DIR__ . '/../config/UserModelInterface.php';
require_once __DIR__ . '/../config/OrderModelInterface.php';
require_once __DIR__ . '/../config/AuthGuard.php';

class ProfileController {
    private $userModel;
    private $orderModel;
    private $authGuard;
    
    public function __construct(
        UserModelInterface $userModel,
        OrderModelInterface $orderModel,
        AuthGuard $authGuard
    ) {
        $this->userModel = $userModel;
        $this->orderModel = $orderModel;
        $this->authGuard = $authGuard;
    }
    
    /**
     * Display the profile page with user data and order history.
     * Handles profile update on POST.
     */
    public function show() {
        // Auth guard — redirect unauthenticated users (DIP: injected, not global fn)
        $this->authGuard->requireAuth('/profile.php');
        
        $userId = (int)$_SESSION['user_id'];
        
        // Fetch authenticated user from DB
        $userData = $this->userModel->findById($userId);
        if (!$userData) {
            $_SESSION = [];
            session_destroy();
            header('Location: ' . BASE_PATH . '/auth_gate.php');
            exit;
        }
        
        $fullName      = trim($userData['first_name'] . ' ' . $userData['last_name']);
        $phone         = $userData['phone_number'];
        $email         = $userData['email'] ?? '';
        $avatarInitial = strtoupper(substr($userData['first_name'], 0, 1));
        
        // Handle profile update — POST/Redirect/GET to prevent re-submission
        $updateError = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_profile') {
            $newName  = trim($_POST['full_name'] ?? '');
            $newPhone = trim($_POST['phone']     ?? '');
            $newEmail = trim($_POST['email']     ?? '');
            
            if (empty($newName) || empty($newPhone)) {
                $updateError = 'Full name and phone number are required.';
            } else {
                if ($this->userModel->isPhoneTaken($newPhone, $userId)) {
                    $updateError = 'That phone number is already used by another account.';
                } else {
                    $nameParts = explode(' ', $newName, 2);
                    $this->userModel->updateProfile(
                        $userId,
                        $nameParts[0],
                        $nameParts[1] ?? '',
                        $newPhone,
                        $newEmail ?: null
                    );
                    $_SESSION['user_name'] = $newName;
                    header('Location: ' . BASE_PATH . '/profile.php?updated=1');
                    exit;
                }
            }
            // Keep submitted values visible on error
            $fullName      = $newName;
            $phone         = $newPhone;
            $email         = $newEmail;
            $avatarInitial = strtoupper(substr(trim(explode(' ', $newName)[0] ?? 'U'), 0, 1));
        }
        
        // SRP: Order queries delegated to OrderModel, not User model
        $ordersResult = $this->orderModel->getCustomerOrdersWithDetails($userId);
        $orders = array_map(fn($o) => [
            'ref'    => $o['reference_number'] ?? ('PH-' . str_pad($o['order_id'], 5, '0', STR_PAD_LEFT)),
            'date'   => date('M j, Y • g:i A', strtotime($o['created_at'])),
            'name'   => $o['first_item'] ?? ('Order #' . $o['order_id']),
            'price'  => (float)($o['grand_total'] ?? 0),
            'status' => match($o['status']) {
                'Completed' => 'Delivered',
                'Cancelled', 'Rejected' => 'Cancelled',
                default => 'Ongoing',
            },
            'id'     => $o['order_id'],
        ], $ordersResult);
        
        $pageTitle = "My Profile - Lola's Kusina";
        $currentPage = "account";
        include __DIR__ . '/../views/layouts/header.php';
        include __DIR__ . '/../views/profile.php';
    }
}
?>

