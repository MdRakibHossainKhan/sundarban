<?php
session_start();
include('../includes/db.php');
include('includes/admin_header.php');
include('../functions/auth.php');
include('../functions/product.php');

// Check admin login
if (!isset($_SESSION['user_id']) || $_SESSION['user_privilege'] !== 'admin') {
    header("Location: ../login.php"); // Redirect to the main login page
    exit();
}

// --- Process Form Submissions for Add/Delete ---
$message = ""; // For success/error messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_product'])) {
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $price = $_POST['price'];
        $quantity = $_POST['quantity'];

        // Enhanced Validation
        if (empty($name) || !is_numeric($price) || floatval($price) < 0 || !ctype_digit($quantity) || intval($quantity) < 0) {
            $message = "<p class='error'>Invalid input. Please check all fields. Price and quantity must be non-negative numbers.</p>";
        } else {
            $price_float = floatval($price);
            $quantity_int = intval($quantity);

            if (addProduct($conn, $name, $description, $price_float, $quantity_int)) {
                $message = "<p class='success'>Product added successfully.</p>";
                // Use JS redirect to allow message display
                echo "<script>window.location.href='products.php?message=" . urlencode($message) . "';</script>";
                exit();
            } else {
                $db_error = $conn->error;
                $message = "<p class='error'>Failed to add product. Please try again. DB Error: " . htmlspecialchars($db_error) . "</p>";
            }
        }
    }
}

// --- Handle Delete Action ---
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if (deleteProduct($conn, $id)) {
        $message = "<p class='success'>Product deleted successfully.</p>";
        echo "<script>window.location.href='products.php?message=" . urlencode($message) . "';</script>";
        exit();
    } else {
        $db_error = $conn->error;
        $message = "<p class='error'>Failed to delete product. It might be linked to existing orders or carts. DB Error: " . htmlspecialchars($db_error). "</p>";
    }
}

// Display message if redirected with one
if (isset($_GET['message'])) {
    // Basic check for success/error in message content for styling
    $msg_class = (stripos($_GET['message'], 'success') !== false) ? 'success' : 'error';
    $message = "<p class='" . $msg_class . "'>" . htmlspecialchars(urldecode($_GET['message'])) . "</p>";
}

?>

    <div class="admin-products container">
        <h2>Manage Products</h2>

        <?php echo $message; ?>

        <div style="margin-bottom: 20px;">
            <a href="products.php?action=add" class="button" style="background-color: #28a745; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;">Add New Product</a>
        </div>


        <?php // Conditionally display Add form ?>
        <?php if (isset($_GET['action']) && $_GET['action'] == 'add'): ?>
            <h3>Add New Product</h3>
            <form method="post" action="products.php">
                <div>
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div>
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows="4" cols="30"></textarea>
                </div>
                <div>
                    <label for="price">Price:</label>
                    <input type="number" id="price" name="price" min="0" step="0.01" required>
                </div>
                <div>
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" value="1" min="0" required>
                </div>
                <button type="submit" name="add_product">Add Product</button>
                <a href="products.php" style="margin-left: 10px;">Cancel</a>
            </form>

        <?php endif; ?>


        <h3 style="margin-top: 30px;">Product List</h3>
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $products = getProducts($conn);
            if ($products) {
                foreach ($products as $product) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($product['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($product['name']) . "</td>";
                    echo "<td>$" . htmlspecialchars(number_format($product['price'], 2)) . "</td>";
                    echo "<td>" . htmlspecialchars($product['quantity']) . "</td>";
                    echo "<td>";

                    // Delete link with confirmation
                    echo "<a href='products.php?action=delete&id=" . htmlspecialchars($product['id']) . "' class='button remove-button' style='padding: 5px 8px; text-decoration: none; border-radius: 3px;' onclick='return confirm(\"Are you sure you want to delete this product? This cannot be undone.\")'>Delete</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No products found.</td></tr>";
            }
            ?>
            </tbody>
        </table>

    </div>

<?php include('includes/admin_footer.php'); ?>