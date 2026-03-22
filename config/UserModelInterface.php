<?php
/**
 * User Model Interface — DIP abstraction
 * 
 * Controllers depend on this interface, not the concrete User model.
 */
interface UserModelInterface {
    /**
     * @param string $phone Phone number to search
     * @return array|null   User record or null
     */
    public function findByPhone(string $phone): ?array;

    /**
     * @param int $userId User ID
     * @return array|null User record or null
     */
    public function findById(int $userId): ?array;

    /**
     * @param string $firstName    First name
     * @param string $lastName     Last name
     * @param string $phone        Phone number
     * @param string $passwordHash Bcrypt-hashed password
     * @return array|null          Created user record or null
     */
    public function create(string $firstName, string $lastName, string $phone, string $passwordHash): ?array;

    /**
     * @param int         $userId    User ID
     * @param string      $firstName First name
     * @param string      $lastName  Last name
     * @param string      $phone     Phone number
     * @param string|null $email     Email (nullable)
     * @return bool
     */
    public function updateProfile(int $userId, string $firstName, string $lastName, string $phone, ?string $email): bool;

    /**
     * @param int $userId User ID
     * @return bool
     */
    public function updateLastLogin(int $userId): bool;

    /**
     * @param string   $phone         Phone number
     * @param int|null $excludeUserId User ID to exclude
     * @return bool
     */
    public function isPhoneTaken(string $phone, ?int $excludeUserId = null): bool;
}
?>
