<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");

// include database and object files
include_once '../config/database.php';
include_once '../objects/product.php';
 
// instantiate database and product object
$database = new Database();
$db = $database->getConnection();
 
// initialize object
$product = new Product($db);

if (isset($_GET['id'])) {
	// set product id
	$product->id = $_GET['id'];
	// query products
	$product_item = $product->readOne();	 
	// check if more than 0 record found
	if($product_item) {
		echo json_encode($product_item);
	} else {
		echo json_encode(
			array("message" => "No product found.")
		);
	}
} else {
	echo json_encode(
		array("message" => "No product id.")
	);
}
