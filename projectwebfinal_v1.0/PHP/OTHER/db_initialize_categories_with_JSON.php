<?php
	// Load JSON file contents
	$jsonData = file_get_contents('../../JSON/categories.json');
	$data = json_decode($jsonData, true);

	require_once "../db.php";
	
	$stmt = mysqli_stmt_init($conn);
	
	foreach ($data['categories'] as $category) {
	
		// Insert categoriy into categories table
		$insertCategoryQuery = "INSERT INTO categories (category_name) VALUES (?)
								ON DUPLICATE KEY UPDATE category_name = VALUES(category_name)";
		$prepare = mysqli_stmt_prepare($stmt, $insertCategoryQuery);
			if($prepare){
				mysqli_stmt_bind_param($stmt, "s", $category['category_name']);
				mysqli_stmt_execute($stmt);
			}
	}
	echo "Data inserted successfully";
	
	// Close connection
	mysqli_close($conn);
?>

