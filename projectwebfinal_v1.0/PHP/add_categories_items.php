<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}
if ($_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once "db.php"; 

$successMessage = ''; 


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_category'])) {
        $categoryName = filter_input(INPUT_POST, 'category_name', FILTER_SANITIZE_STRING);

        if ($categoryName) {
            $categoryQuery = "INSERT INTO categories (category_name) VALUES (?) ON DUPLICATE KEY UPDATE category_name = VALUES(category_name)";
            $categoryStmt = $conn->prepare($categoryQuery);
            $categoryStmt->bind_param("s", $categoryName);
            $categoryStmt->execute();
            $categoryId = $conn->insert_id; 
            $categoryStmt->close();
            $successMessage = "Category successfully added.";
        } else {
            die('Invalid category name');
        }
    }

    if (isset($_POST['add_item'])) {
        $itemName = filter_input(INPUT_POST, 'item_name', FILTER_SANITIZE_STRING);
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
        $categoryName = filter_input(INPUT_POST, 'category_name', FILTER_SANITIZE_STRING);
        $details = filter_input(INPUT_POST, 'details', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

        if ($itemName && $quantity !== false && $categoryName) {
            
            $categoryQuery = "SELECT id FROM categories WHERE category_name = ?";
            $categoryStmt = $conn->prepare($categoryQuery);
            $categoryStmt->bind_param("s", $categoryName);
            $categoryStmt->execute();
            $categoryResult = $categoryStmt->get_result();
            $category = $categoryResult->fetch_assoc();
            $categoryId = $category['id'] ?? null;
            $categoryStmt->close();

            if ($categoryId) {
                
                $itemQuery = "INSERT INTO items (category_id, item_name, quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE item_name = VALUES(item_name), quantity = VALUES(quantity)";
                $itemStmt = $conn->prepare($itemQuery);
                $itemStmt->bind_param("isi", $categoryId, $itemName, $quantity);
                $itemStmt->execute();
                $itemId = $conn->insert_id; 

               
                if (is_array($details)) {
                    $detailQuery = "INSERT INTO item_details (item_id, detail_name, detail_value) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE detail_value = VALUES(detail_value)";
                    $detailStmt = $conn->prepare($detailQuery);

                    foreach ($details as $detail) {
                        $detailName = filter_var($detail['name'], FILTER_SANITIZE_STRING);
                        $detailValue = filter_var($detail['value'], FILTER_SANITIZE_STRING);
                        $detailStmt->bind_param("iss", $itemId, $detailName, $detailValue);
                        $detailStmt->execute();
                    }

                    $detailStmt->close();
                }

                $itemStmt->close();
                $successMessage = "Item successfully added.";
            } else {
                die('Invalid category name');
            }
        } else {
            die('Invalid item data');
        }
    }

    
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Categories and Items</title>
    <link rel="stylesheet" href="../CSS/MY_stylesheet.css">
    <link rel="stylesheet" href="../CSS/MY_stylesheetNavbar.css">
    <style>
        #container {
            background-color: #1f2933;
            background-color: rgba(0, 0, 0, 0.3);
            margin-top: 2%;
            margin-left: auto;
            margin-right: auto;
            width:fit-content;
            border-radius: 20px;
            padding:20px;
            padding-top:2%;
            padding-bottom:2%;
        }
        #detailButtons{
            display: inline-flex;
            flex-direction: row;
            margin: 1%;
            gap: 10px; 
        }
        #indent {margin-left:10px;}
        h3{margin-left:-15px;}
    </style>
</head>
<body>
    <div class="navbar">
        <div class="usertype" onclick="window.location.href='loggedadmin.php'">Go back to the dashboard</div>
    </div>
    <?php if ($successMessage): ?>
        <p><?php echo htmlspecialchars($successMessage); ?></p>
    <?php endif; ?>

    <div id="container">
        <div id="indent">
        <form method="post">
            <h3>Add Category</h3>
            <label>Category Name:</label><br>
            <input type="text" name="category_name" required>
            <input type="submit" name="add_category" value="Add Category">
        </form>
        
        <form method="post">
            <h3>Add Item</h3>
            <div>
                <label>Category Name:</label><br>
                <select id="categorySelect" name="category_name" required>
                    <option value="">Select a Category</option>
                </select><br>
                <label>Item Name:</label><br>
                <input type="text" name="item_name" required><br>
                <label>Quantity:</label><br>
                <input type="number" name="quantity" value="0" min="0" required>
            </div>
            <h3>Add Details</h3>
            
            <div id="details">
                <div>
                    <label>Detail Name:</label>
                    <input type="text" name="details[0][name]">
                    <label>Detail Value:</label>
                    <input type="text" name="details[0][value]">
                </div>
            </div>
            <div id="detailButtons">
                <button type="button" onclick="addDetail()">Add More Details</button>
                <input type="submit" name="add_item" value="Add Item">
            </div>
        </form>
        </div>
    </div>
    <script>
    let detailIndex = 1;
    function addDetail() {
        const detailsDiv = document.getElementById('details');
        const newDetailDiv = document.createElement('div');
        newDetailDiv.innerHTML = `
            <label>Detail Name:</label>
            <input type="text" name="details[${detailIndex}][name]">
            <label>Detail Value:</label>
            <input type="text" name="details[${detailIndex}][value]">
        `;
        detailsDiv.appendChild(newDetailDiv);
        detailIndex++;
    }
    function fetchCategories() {
        fetch('get_categories.php')
            .then(response => response.json())
            .then(categories => {
                const categorySelect = document.getElementById('categorySelect');
                categorySelect.innerHTML = '<option value="">Select a Category</option>';
                categories.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category.category_name;
                    option.textContent = category.category_name;
                    categorySelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error fetching categories:', error));
    }
    fetchCategories();

    </script>
</body>
</html>






