<?php
/**
 * Auth Controller — SRP: handles login and registration only
 * 
 * Extracted from login.php and register.php views so they contain
 * only presentation logic.
 * DIP: Depends on User model and ValidatorInterface, not concrete classes.
 */
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/ValidatorInterface.php';

class AuthController {
    private $userModel;
    private $validator;
    
    public function __construct(User $userModel, ValidatorInterface $validator) {
        $this->userModel = $userModel;
        $this->validator = $validator;
    }
    
    /**
     * Handle login page display and form submission.
     * Sets $error, $redirect variables then includes the view.
     */
    public function login() {
        $error = '';
        $redirect = $_GET['redirect'] ?? BASE_PATH . '/index.php';
        
        // Validate redirect URL
        if (!preg_match('/^\/[a-zA-Z0-9\-_.~!$&\'()*+,;=:@\/?%]*$/', $redirect)) {
            $redirect = BASE_PATH . '/index.php';
        }
        
        // If already logged in, redirect
        if (!empty($_SESSION['user_id'])) {
            header('Location: ' . $redirect);
            exit;
        }
        
        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $phone = trim($_POST['phone'] ?? '');
            $password = trim($_POST['password'] ?? '');
            
            if (empty($phone) || empty($password)) {
                $error = 'Please fill in all fields.';
            } else {
                try {
                    $user = $this->userModel->findByPhone($phone);
                    
                    if ($user && password_verify($password, $user['password_hash'])) {
                        // Login successful
                        $_SESSION['user_id'] = $user['user_id'];
                        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                        $this->userModel->updateLastLogin($user['user_id']);
                        
                        header('Location: ' . $redirect);
                        exit;
                    } else {
                        $error = 'Invalid phone number or password.';
                    }
                } catch (Exception $e) {
                    $error = 'An error occurred. Please try again later.';
                }
            }
        }
        
        $pageTitle = "Log In - Lola's Kusina";
        include __DIR__ . '/../views/login.php';
    }
    
    /**
     * Handle registration page display and form submission.
     * Sets $error, $redirect variables then includes the view.
     */
    public function register() {
        $error = '';
        $redirect = $_GET['redirect'] ?? BASE_PATH . '/index.php';
        
        // Validate redirect URL
        if (!preg_match('/^\/[a-zA-Z0-9\-_.~!$&\'()*+,;=:@\/?%]*$/', $redirect)) {
            $redirect = BASE_PATH . '/index.php';
        }
        
        // If already logged in, redirect
        if (!empty($_SESSION['user_id'])) {
            header('Location: ' . $redirect);
            exit;
        }
        
        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullName = trim($_POST['full_name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $confirmPassword = trim($_POST['confirm_password'] ?? '');
            
            // Validation
            if (empty($fullName) || empty($phone) || empty($password) || empty($confirmPassword)) {
                $error = 'Please fill in all fields.';
            } elseif (strlen($password) < 6) {
                $error = 'Password must be at least 6 characters long.';
            } elseif ($password !== $confirmPassword) {
                $error = 'Passwords do not match.';
            } else {
                try {
                    if ($this->userModel->isPhoneTaken($phone)) {
                        $error = 'This phone number is already registered.';
                    } else {
                        $nameParts = explode(' ', $fullName, 2);
                        $firstName = $nameParts[0];
                        $lastName = $nameParts[1] ?? '';
                        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
                        
                        $newUser = $this->userModel->create($firstName, $lastName, $phone, $passwordHash);
                        
                        if ($newUser) {
                            // Auto-login the user
                            $_SESSION['user_id'] = $newUser['user_id'];
                            $_SESSION['user_name'] = $newUser['first_name'] . ' ' . $newUser['last_name'];
                            
                            header('Location: ' . $redirect);
                            exit;
                        }
                    }
                } catch (Exception $e) {
                    $error = 'An error occurred during registration. Please try again later.';
                }
            }
        }
        
        $pageTitle = "Sign Up - Lola's Kusina";
        include __DIR__ . '/../views/register.php';
    }
}
?>
