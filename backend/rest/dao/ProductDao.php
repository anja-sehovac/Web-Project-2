<?php
require_once __DIR__ . "/BaseDao.php";

class ProductDao extends BaseDao {
    public function __construct()
    {
        parent::__construct('product');
    }

    public function add_product($product)
    {
        return $this->insert('product', $product);
    }
    public function get_product_by_id($product_id) {
        return $this->query_unique("
            SELECT 
                p.id, 
                c.name AS category, 
                p.quantity, 
                p.price_each, 
                p.description
            FROM product p
            JOIN category c ON p.category_id = c.id
            WHERE p.id = :id
        ", ["id" => $product_id]);
    }

    public function get_all_products($search = null, $sort = null, $min_price = null, $max_price = null, $category_id = null) {
        $query = "
            SELECT 
                p.id, 
                p.name,
                c.name AS category_name, 
                p.quantity, 
                p.price_each, 
                p.description
            FROM product p
            JOIN category c ON p.category_id = c.id
            WHERE 1=1
        ";
    
        $params = [];
    
        // Filter by name (search)
        if ($search) {
            $query .= " AND p.name LIKE :search";
            $params['search'] = "%$search%"; // Partial match
        }
    
        // Filter by price range
        if ($min_price !== null) {
            $query .= " AND p.price_each >= :min_price";
            $params['min_price'] = $min_price;
        }
        if ($max_price !== null) {
            $query .= " AND p.price_each <= :max_price";
            $params['max_price'] = $max_price;
        }
    
        // Filter by category
        if ($category_id !== null) {
            $query .= " AND p.category_id = :category_id";
            $params['category_id'] = $category_id;
        }
    
        // Sorting
        if ($sort === 'asc') {
            $query .= " ORDER BY p.price_each ASC";
        } elseif ($sort === 'desc') {
            $query .= " ORDER BY p.price_each DESC";
        }
    
        return $this->query($query, $params);
    }
    
    

    public function update_product($product_id, $product) {
        return $this->update("product", $product_id, $product);
    }

    public function delete_product($product_id) {
        $this->delete("product", $product_id);
    }



}