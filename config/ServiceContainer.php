<?php
/**
 * Service Container — DIP: Centralized dependency wiring
 * 
 * Lazy-loads and wires all dependencies so that controllers never
 * instantiate concrete classes directly. Replaces the global getDb()/getValidator()
 * functions that previously lived in the router.
 * 
 * SRP: This class only manages object creation and wiring.
 */
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/ValidatorInterface.php';
require_once __DIR__ . '/CalculatorInterface.php';
require_once __DIR__ . '/DeliveryFeeCalculatorInterface.php';
require_once __DIR__ . '/ReferenceGeneratorInterface.php';
require_once __DIR__ . '/FileUploadValidatorInterface.php';

class ServiceContainer {
    private $instances = [];
    
    /**
     * Get the Database instance (lazy-loaded singleton).
     *
     * @return DatabaseInterface
     */
    public function getDatabase(): DatabaseInterface {
        if (!isset($this->instances['db'])) {
            $this->instances['db'] = new Database();
        }
        return $this->instances['db'];
    }
    
    /**
     * Get the InputValidator instance.
     *
     * @return ValidatorInterface
     */
    public function getValidator(): ValidatorInterface {
        if (!isset($this->instances['validator'])) {
            require_once __DIR__ . '/../services/InputValidator.php';
            $this->instances['validator'] = new InputValidator();
        }
        return $this->instances['validator'];
    }
    
    /**
     * Get the OrderTotalCalculator instance.
     *
     * @return CalculatorInterface
     */
    public function getCalculator(): CalculatorInterface {
        if (!isset($this->instances['calculator'])) {
            require_once __DIR__ . '/../services/OrderTotalCalculator.php';
            $this->instances['calculator'] = new OrderTotalCalculator();
        }
        return $this->instances['calculator'];
    }
    
    /**
     * Get the DeliveryFeeCalculator instance.
     *
     * @return DeliveryFeeCalculatorInterface
     */
    public function getDeliveryFeeCalculator(): DeliveryFeeCalculatorInterface {
        if (!isset($this->instances['deliveryFee'])) {
            require_once __DIR__ . '/../services/DeliveryFeeCalculator.php';
            $this->instances['deliveryFee'] = new DeliveryFeeCalculator();
        }
        return $this->instances['deliveryFee'];
    }
    
    /**
     * Get the ReferenceNumberGenerator instance.
     *
     * @return ReferenceGeneratorInterface
     */
    public function getReferenceGenerator(): ReferenceGeneratorInterface {
        if (!isset($this->instances['referenceGen'])) {
            require_once __DIR__ . '/../services/ReferenceNumberGenerator.php';
            $this->instances['referenceGen'] = new ReferenceNumberGenerator();
        }
        return $this->instances['referenceGen'];
    }
    
    /**
     * Get the User model instance.
     *
     * @return User
     */
    public function getUserModel(): User {
        if (!isset($this->instances['user'])) {
            require_once __DIR__ . '/../models/User.php';
            $this->instances['user'] = new User($this->getDatabase());
        }
        return $this->instances['user'];
    }
    
    /**
     * Get the Order model instance.
     *
     * @return Order
     */
    public function getOrderModel(): Order {
        if (!isset($this->instances['order'])) {
            require_once __DIR__ . '/../models/Order.php';
            $this->instances['order'] = new Order($this->getDatabase());
        }
        return $this->instances['order'];
    }
    
    /**
     * Get the Package model instance.
     *
     * @return Package
     */
    public function getPackageModel(): Package {
        if (!isset($this->instances['package'])) {
            require_once __DIR__ . '/../models/Package.php';
            $this->instances['package'] = new Package($this->getDatabase());
        }
        return $this->instances['package'];
    }
    
    /**
     * Get the MenuItem model instance.
     *
     * @return MenuItem
     */
    public function getMenuItemModel(): MenuItem {
        if (!isset($this->instances['menuItem'])) {
            require_once __DIR__ . '/../models/MenuItem.php';
            $this->instances['menuItem'] = new MenuItem($this->getDatabase());
        }
        return $this->instances['menuItem'];
    }
}
?>
