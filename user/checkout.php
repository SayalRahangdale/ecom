<?php
session_start();
include 'header.php';

// Check if user is logged in
if (!isset($_SESSION['USER'])) {
    echo "<script>
    alert('Please login first to checkout');
    window.location.href='form/login.php';
    </script>";
    exit();
}

// Check if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<script>
    alert('Your cart is empty');
    window.location.href='index.php';
    </script>";
    exit();
}

include 'Config.php';

// Process order submission
if (isset($_POST['place_order'])) {
    // Get user ID
    $username = $_SESSION['USER'];
    $user_query = mysqli_query($conn, "SELECT id FROM tbluser WHERE UserName = '$username'");
    $user_data = mysqli_fetch_assoc($user_query);
    $user_id = $user_data['id'];

    // Get delivery details
    $delivery_address = mysqli_real_escape_string($conn, $_POST['address']);
    $delivery_city = mysqli_real_escape_string($conn, $_POST['city']);
    $delivery_state = mysqli_real_escape_string($conn, $_POST['state']);
    $delivery_pincode = mysqli_real_escape_string($conn, $_POST['pincode']);
    $delivery_phone = mysqli_real_escape_string($conn, $_POST['phone']);

    // Create order with delivery details
    mysqli_query($conn, "INSERT INTO orders (user_id, delivery_address, delivery_city, delivery_state, delivery_pincode, delivery_phone) 
                        VALUES ($user_id, '$delivery_address', '$delivery_city', '$delivery_state', '$delivery_pincode', '$delivery_phone')");
    $order_id = mysqli_insert_id($conn);

    // Add order items
    foreach ($_SESSION['cart'] as $item) {
        $product_name = mysqli_real_escape_string($conn, $item['productName']);
        $product_price = floatval($item['productPrice']);
        $quantity = intval($item['productQuantity']);
        $total_price = $product_price * $quantity;

        mysqli_query($conn, "INSERT INTO order_items (order_id, product_name, product_price, product_quantity, total_price) 
                            VALUES ($order_id, '$product_name', $product_price, $quantity, $total_price)");
    }

    // Clear cart
    unset($_SESSION['cart']);

    echo "<script>
    alert('Order placed successfully!');
    window.location.href='my_orders.php';
    </script>";
    exit();
}

// Calculate total
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['productPrice'] * $item['productQuantity'];
}
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Checkout</h2>
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Order Summary</h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($_SESSION['cart'] as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['productName']); ?></td>
                                <td><?php echo htmlspecialchars($item['productQuantity']); ?></td>
                                <td>₹<?php echo htmlspecialchars($item['productPrice']); ?></td>
                                <td>₹<?php echo htmlspecialchars($item['productPrice'] * $item['productQuantity']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Grand Total:</strong></td>
                                <td><strong>₹<?php echo htmlspecialchars($total); ?></strong></td>
                            </tr>
                        </tbody>
                    </table>

                    <form method="POST" class="mt-4">
                        <h5 class="mb-3">Delivery Address</h5>
                        <div class="mb-3">
                            <label class="form-label">Street Address</label>
                            <textarea name="address" class="form-control" required rows="2"></textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">City</label>
                                <input type="text" name="city" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">State</label>
                                <input type="text" name="state" class="form-control" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">PIN Code</label>
                                <input type="text" name="pincode" class="form-control" required pattern="[0-9]{6}" title="Please enter a valid 6-digit PIN code">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" name="phone" class="form-control" required pattern="[0-9]{10}" title="Please enter a valid 10-digit phone number">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" value="cod" checked>
                                <label class="form-check-label">Cash on Delivery</label>
                            </div>
                        </div>
                        <button type="submit" name="place_order" class="btn btn-primary">Place Order</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> 