<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sari-Sari Store Inventory</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Sari-Sari Store Inventory System</h1>
            <div style="position:absolute; top:16px; right:20px;">
                Logged in as <?php echo htmlspecialchars($_SESSION['user']['username']); ?> 
                <button id="logoutBtn" style="margin-left:8px; padding:4px 8px; font-size:13px;">Logout</button>
            </div>
        </header>
        <div class="main-content">
            <aside class="form-section">
                <h2>Item Details</h2>
                <form id="item-form">
                    <label>Name:</label>
                    <input type="text" id="name" name="name" required>

                    <label>Manufacture:</label>
                    <input type="text" id="manufacture" name="manufacture">

                    <label>Types of Item:</label>
                    <input type="text" id="type" name="type">

                    <label>Grams:</label>
                    <input type="number" id="grams" name="grams">

                    <label>Price:</label>
                    <input type="number" id="price" name="price" step="0.01">

                    <label>Expiration Date:</label>
                    <input type="date" id="expiration" name="expiration">

                    <label>Made Date:</label>
                    <input type="date" id="madeDate" name="madeDate">

                    <label>No. Availability:</label>
                    <input type="number" id="availability" name="availability">

                    <div class="button-row">
                        <button type="button" id="saveBtn">save</button>
                        <button type="button" id="retrieveBtn">retrive</button>
                        <button type="button" id="updateBtn">update</button>
                        <button type="button" id="deleteBtn">delete</button>
                    </div>
                    <div class="exit-row">
                        <button type="button" id="exitBtn">exit</button>
                    </div>
                </form>
            </aside>

            <section class="inventory-section">
                <table id="inventory-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Manufacture</th>
                            <th>Types of Item</th>
                            <th>Grams</th>
                            <th>Price</th>
                            <th>Expiration Date</th>
                            <th>Made Date</th>
                            <th>No. Availability</th>
                        </tr>
                    </thead>
                    <tbody id="inventory-list">
                        <!-- rows added dynamically -->
                    </tbody>
                </table>
            </section>
        </div>
    </div>
    <script src="js/main.js"></script>
    <script>
    document.getElementById('logoutLink').addEventListener('click', async function(e) {
        e.preventDefault();
        await fetch('api/auth.php', { method: 'DELETE' });
        window.location = 'login.php';
    });
    </script>
</body>
</html>
