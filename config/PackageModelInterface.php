<?php
/**
 * Package Model Interface — DIP abstraction
 * 
 * Controllers depend on this interface, not the concrete Package model.
 */
interface PackageModelInterface {
    /**
     * @return array
     */
    public function getAllPackages();

    /**
     * @param int $limit Max results
     * @return array
     */
    public function getFeaturedPackages($limit = 6);

    /**
     * @param int $id Package ID
     * @return array|null
     */
    public function getPackageById($id);

    /**
     * @param int $packageId Package ID
     * @return array
     */
    public function getPackageItems($packageId);

    /**
     * @param string $category Category name
     * @return array
     */
    public function getPackagesByCategory($category);

    /**
     * @param int $limit Max results
     * @return array
     */
    public function getBestSellers($limit = 3);
}
?>
