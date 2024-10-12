<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    session_start();
    require_once "db.php";
	
    $data = ["username"=>$_SESSION["user"],"userId" => $_SESSION["user_id"]];
    echo json_encode($data);
    $conn->close();
?>
