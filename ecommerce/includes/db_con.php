<?php
class DB_Ecom {
    private $host;
    private $username;
    private $password;
    private $database;
    private $conn;

    public function __construct() {
        if (in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1'])) {
            $this->host = 'localhost';
            $this->username = 'root';
            $this->password = '';
            $this->database = 'automotive2';
        } else {
            $this->host = 'localhost'; // or your host's DB host if different
            $this->username = 'nati';
            $this->password = 'Nati-0911';
            $this->database = 'automotive';
        }
        try {
            $this->conn = new PDO("mysql:host={$this->host};dbname={$this->database}", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    // Get user's orders
    public function get_user_orders($user_id) {
        $sql = "SELECT o.*, p.name as product_name, p.price, p.image_url 
                FROM tbl_orders o 
                JOIN tbl_products p ON o.product_id = p.id 
                WHERE o.user_id = ? 
                ORDER BY o.order_date DESC";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$user_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return false;
        }
    }

    // Get user's cart items
    public function get_user_cart($user_id) {
        $sql = "SELECT c.*, p.name as product_name, p.price, p.image_url, p.stock 
                FROM tbl_cart c 
                JOIN tbl_products p ON c.product_id = p.id 
                WHERE c.user_id = ?";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$user_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return false;
        }
    }

    // Get user's wishlist items
    public function get_user_wishlist($user_id) {
        $sql = "SELECT w.*, p.name as product_name, p.price, p.image_url, p.stock 
                FROM tbl_wishlist w 
                JOIN tbl_products p ON w.product_id = p.id 
                WHERE w.user_id = ?";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$user_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return false;
        }
    }

    // Get user profile
    public function get_user_profile($user_id) {
        $sql = "SELECT * FROM tbl_user WHERE id = ?";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$user_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return false;
        }
    }

    // Cancel order
    public function cancel_order($order_id, $user_id) {
        $sql = "UPDATE tbl_orders SET status = 'cancelled' WHERE id = ? AND user_id = ? AND status = 'pending'";
        
        try {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$order_id, $user_id]);
        } catch(PDOException $e) {
            return false;
        }
    }

    // Add item to cart
    public function add_to_cart($user_id, $product_id, $quantity = 1) {
        try {
            // Check if product exists and has enough stock
            $sql = "SELECT stock FROM tbl_products WHERE id = ? AND status = 'active'";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$product_id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product || $product['stock'] < $quantity) {
                return false;
            }

            // Check if item already exists in cart
            $sql = "SELECT id, quantity FROM tbl_cart WHERE user_id = ? AND product_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$user_id, $product_id]);
            $cart_item = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($cart_item) {
                // Update quantity if item exists
                $new_quantity = $cart_item['quantity'] + $quantity;
                if ($new_quantity > $product['stock']) {
                    return false;
                }

                $sql = "UPDATE tbl_cart SET quantity = ? WHERE id = ?";
                $stmt = $this->conn->prepare($sql);
                return $stmt->execute([$new_quantity, $cart_item['id']]);
            } else {
                // Insert new item if it doesn't exist
                $sql = "INSERT INTO tbl_cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
                $stmt = $this->conn->prepare($sql);
                return $stmt->execute([$user_id, $product_id, $quantity]);
            }
        } catch(PDOException $e) {
            return false;
        }
    }

    // Remove item from cart
    public function remove_from_cart($cart_id, $user_id) {
        $sql = "DELETE FROM tbl_cart WHERE id = ? AND user_id = ?";
        
        try {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$cart_id, $user_id]);
        } catch(PDOException $e) {
            return false;
        }
    }

    // Update cart item quantity
    public function update_cart_quantity($cart_id, $user_id, $quantity) {
        try {
            // Get product info to check stock
            $sql = "SELECT p.stock FROM tbl_cart c 
                    JOIN tbl_products p ON c.product_id = p.id 
                    WHERE c.id = ? AND c.user_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$cart_id, $user_id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product || $product['stock'] < $quantity) {
                return false;
            }

            $sql = "UPDATE tbl_cart SET quantity = ? WHERE id = ? AND user_id = ?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$quantity, $cart_id, $user_id]);
        } catch(PDOException $e) {
            return false;
        }
    }

    // Add item to wishlist
    public function add_to_wishlist($user_id, $product_id) {
        // Check if item already exists in wishlist
        $sql = "SELECT id FROM tbl_wishlist WHERE user_id = ? AND product_id = ?";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$user_id, $product_id]);
            
            if ($stmt->rowCount() > 0) {
                return false; // Already in wishlist
            }

            // Insert new item
            $sql = "INSERT INTO tbl_wishlist (user_id, product_id) VALUES (?, ?)";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$user_id, $product_id]);
        } catch(PDOException $e) {
            return false;
        }
    }

    // Remove item from wishlist
    public function remove_from_wishlist($wishlist_id, $user_id) {
        $sql = "DELETE FROM tbl_wishlist WHERE id = ? AND user_id = ?";
        
        try {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$wishlist_id, $user_id]);
        } catch(PDOException $e) {
            return false;
        }
    }

    // Get cart count for user
    public function get_cart_count($user_id) {
        $sql = "SELECT COUNT(*) as count FROM tbl_cart WHERE user_id = ?";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$user_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch(PDOException $e) {
            return 0;
        }
    }

    // Destructor - PDO automatically closes connections
    public function __destruct() {
        $this->conn = null;
    }
} 