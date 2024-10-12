<?php
	// Load JSON file contents
	$jsonData = file_get_contents('../../JSON/items.json');
	$data = json_decode($jsonData, true);
	
	require_once "../db.php";
	$stmtCheckItemName = mysqli_stmt_init($conn);
	$stmtInsertItem = mysqli_stmt_init($conn);
	$stmtInsertBaseItem = mysqli_stmt_init($conn);
	$stmtInsertDetail = mysqli_stmt_init($conn);
	
	// Insert data into tables
	foreach ($data['items'] as $item) {
		$checkItemNameQuery = "SELECT id FROM items WHERE item_name = ?";
		$prepareCheckItemName = mysqli_stmt_prepare($stmtCheckItemName, $checkItemNameQuery);
		mysqli_stmt_bind_param($stmtCheckItemName, "s", $item['name']);
		mysqli_stmt_execute($stmtCheckItemName);
		$result = mysqli_stmt_get_result($stmtCheckItemName);
		
		// Item with the same name was found, use its item_id
		if ($row = mysqli_fetch_assoc($result)) {
			$item_id = $row['id'];
		} else {
		// Otherwise get the generated id of the items table
		// Insert item into items table
			$insertItemQuery = "INSERT IGNORE INTO items (item_name, category_id) VALUES (?, ?)";
			$prepareInsertItem = mysqli_stmt_prepare($stmtInsertItem, $insertItemQuery);
			mysqli_stmt_bind_param($stmtInsertItem, "si", $item['name'], $item['category']);
			mysqli_stmt_execute($stmtInsertItem);
			$item_id = mysqli_insert_id($conn);
		}
		
		// Insert into base_items table with the new item_id
		$insertBaseItemQuery = "INSERT INTO base_items (item_id) VALUES (?)";
		$prepareInsertBaseItem = mysqli_stmt_prepare($stmtInsertBaseItem, $insertBaseItemQuery);
		mysqli_stmt_bind_param($stmtInsertBaseItem, "i", $item_id);
		mysqli_stmt_execute($stmtInsertBaseItem);
		
		$base_item_id = mysqli_insert_id($conn); // Retrieve the auto-generated base_item_id
		
		// Insert into item_details table
		foreach ($item['details'] as $detail) {
			if ($detail['detail_name'] != 'date added') { // Skip 'date added' details
				$insertDetailQuery = "INSERT INTO item_details (base_item_id, detail_name, detail_value) VALUES (?, ?, ?)";
				$prepareInsertDetail = mysqli_stmt_prepare($stmtInsertDetail, $insertDetailQuery);
				mysqli_stmt_bind_param($stmtInsertDetail, "iss", $base_item_id, $detail['detail_name'], $detail['detail_value']);
				mysqli_stmt_execute($stmtInsertDetail);
			}
		}
	}
	echo "Data inserted successfully";
	// Close connection
	mysqli_close($conn);
?>

