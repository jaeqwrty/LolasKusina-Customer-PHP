<?php
/**
 * Menu Item Model Interface — DIP abstraction
 * 
 * Controllers depend on this interface, not the concrete MenuItem model.
 */
interface MenuItemModelInterface {
    /**
     * @return array
     */
    public function getAllItems();

    /**
     * @param string $category Category name
     * @return array
     */
    public function getItemsByCategory($category);

    /**
     * @param int $id Item ID
     * @return array|null
     */
    public function getItemById($id);

    /**
     * @param array $ids Item IDs
     * @return array
     */
    public function getItemsByIds($ids);
}
?>
