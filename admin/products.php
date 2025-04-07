<?php
session_start();
include('../includes/db.php');
include('includes/admin_header.php');
include('functions/auth.php');
include('functions/product.php');

// Check admin login
if (!isset($_SESSION['user_id']) || $_SESSION['user_privilege'] !== 'admin') {
    header("Location: login.php");
    exit();
}

?>

    <div class="admin-products">
        <h2>Manage Products</h2>

        <h3>Product List</h3>
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
                    echo "<a href='product_details.php?action=edit&id=" . htmlspecialchars($product['id']) . "'>Edit</a> | ";
                    echo "<a href='product_details.php?action=delete&id=" . htmlspecialchars($product['id']) . "' onclick='return confirm(\"Are you sure?\")'>Delete</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No products found.</td></tr>";
            }
            ?>
            </tbody>
        </table>

        <?php if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])): ?>
            <?php
            $product_id = $_GET['id'];
            $product = getProductById($conn, $product_id);
            if ($product):
                ?>
                <h3>Edit Product</h3>
                <form method="post">
                    <div>
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name"
                               value="<?php echo htmlspecialchars($product['name']); ?>" required>
                    </div>
                    <div>
                        <label for="description">Description:</label>
                        <textarea id="description" name="description" rows="4"
                                  cols="30"><?php echo htmlspecialchars($product['description']); ?></textarea>
                    </div>
                    <div>
                        <label for="price">Price:</label>
                        <input type="number" id="price" name="price"
                               value="<?php echo htmlspecialchars($product['price']); ?>" min="0" step="0.01" required>
                    </div>
                    <div>
                        <label for="quantity">Quantity:</label>
                        <input type="number" id="quantity" name="quantity"
                               value="<?php echo htmlspecialchars($product['quantity']); ?>" min="0" required>
                    </div>
                    <button type="submit" name="update_product">Update Product</button>
                </form>
            <?php endif; ?>
        <?php elseif (isset($_GET['action']) && $_GET['action'] == 'add'): ?>
            <h3>Add New Product</h3>
            <form method="post">
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
            </form>
        <?php endif; ?>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['add_product'])) {
                $name = trim($_POST['name']);
                $description = trim($_POST['description']);
                $price = floatval($_POST['price']);
                $quantity = intval($_POST['quantity']);

                // Validation (server-side)
                if (empty($name) || empty($price) || empty($quantity) || $price < 0 || $quantity < 0) {
                    echo "<p class='error'>Invalid input. Please check all fields.</p>";
                } else {
                    if (addProduct($conn, $name, $description, $price, $quantity)) {
                        echo "<p class='success'>Product added successfully.</p>";
                        header("Location: product_details.php"); // Redirect to product list
                        exit();
                    } else {
                        echo "<p class='error'>Failed to add product. Please try again.</p>";
                    }
                }
            } elseif (isset($_POST['update_product']) && isset($_GET['id'])) {
                $id = $_GET['id'];
                $name = trim($_POST['name']);
                $description = trim($_POST['description']);
                $price = floatval($_POST['price']);
                $quantity = intval($_POST['quantity']);

                // Validation
                if (empty($name) || empty($price) || empty($quantity) || $price < 0 || $quantity < 0) {
                    echo "<p class='error'>Invalid input. Please check all fields.</p>";
                } else {
                    if (updateProduct($conn, $id, $name, $description, $price, $quantity)) {
                        echo "<p class='success'>Product updated successfully.</p>";
                        header("Location: product_details.php"); // Redirect to product list
                        exit();
                    } else {
                        echo "<p class='error'>Failed to update product. Please try again.</p>";
                    }
                }
            }
        }

        if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
            $id = $_GET['id'];
            if (deleteProduct($conn, $id)) {
                echo "<p class='success'>Product deleted successfully.</p>";
                header("Location: product_details.php"); // Redirect to product list
                exit();
            } else {
                echo "<p class='error'>Failed to delete product.</p>";
            }
        }
        ?>
    </div>

<?php include('includes/footer.php'); ?>