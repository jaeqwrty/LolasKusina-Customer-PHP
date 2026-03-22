<?php
/**
 * Profile Controller — SRP: handles profile viewing and updating only
 * 
 * Extracted from profile.php view so it contains only presentation logic.
 * DIP: Depends on User model, not concrete Database class.
 */
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/auth.php';

class ProfileController {
    private $userModel;
    
    public function __construct(User $userModel) {
        $this->userModel = $userModel;
    }
    
    /**
     * Display the profile page with user data and order history.
     * Handles profile update on POST.
     */
    public function show() {
        // Auth guard — redirect unauthenticated users
        requireAuth('/profile.php');
        
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
        
        // Fetch user's real orders
        $ordersResult = $this->userModel->getCustomerOrders($userId);
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
