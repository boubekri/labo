<?php
class Product{
 
	// database connection and table name
	private $conn;
	private $table_name = "products";
	private $last_insert_id;
 
	// object properties
	public $id;
	public $name;
	public $description;
	public $price;
	public $category_id;
	public $category_name;
	public $created;
 
	// constructor with $db as database connection
	public function __construct($db) {
		$this->conn = $db;
	}

	public function read() {
		$query = "
			SELECT 	c.name as category_name, 
					p.id, 
					p.name, 
					p.description, 
					p.price, 
					p.category_id, 
					p.created
			FROM " . $this->table_name . " p
			LEFT JOIN categories c ON p.category_id = c.id
			ORDER BY p.created DESC";
		$stmt = $this->conn->prepare($query);
		$stmt->execute();

		return $stmt;
	}

	public function readOne() {
		$query = "
			SELECT 	c.name as category_name, 
					p.id, 
					p.name, 
					p.description, 
					p.price, 
					p.category_id, 
					p.created
			FROM " . $this->table_name . " p
			LEFT JOIN categories c ON p.category_id = c.id
			WHERE p.id = :id";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":id", $this->id);
		$stmt->execute();

		$product_item = array();

		if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			extract($row);
			$product_item = array(
				"id" => $id,
				"name" => $name,
				"description" => html_entity_decode($description),
				"price" => $price,
				"category_id" => $category_id,
				"category_name" => $category_name
			);		
		}

		return $product_item;
	}

	public function create() {
		$query = '
			INSERT INTO ' . $this->table_name . ' (
				name,
				description,
				price,
				category_id,
				created
			)
			VALUES (
				:name,
				:description,
				:price,
				:category_id,
				:created
			)
		';

		$stmt = $this->conn->prepare($query);
		// sanitize
		$this->name 		= htmlspecialchars(strip_tags($this->name));
		$this->price 		= htmlspecialchars(strip_tags($this->price));
		$this->description 	= htmlspecialchars(strip_tags($this->description));
		$this->category_id 	= htmlspecialchars(strip_tags($this->category_id));
		$this->created 		= htmlspecialchars(strip_tags($this->created));
		// bind values
		$stmt->bindParam(":name", $this->name);
		$stmt->bindParam(":price", $this->price);
		$stmt->bindParam(":description", $this->description);
		$stmt->bindParam(":category_id", $this->category_id);
		$stmt->bindParam(":created", $this->created);
		// execute query
		$stmt->execute();

		return $this->conn->LastInsertId();
	}

	/*public function getLastInsertId() {
		$query = "
			SELECT 	p.id
			FROM " . $this->table_name . " p 
			ORDER BY p.id DESC LIMIT 1";
		$stmt = $this->conn->prepare($query);
		$stmt->execute();

		$last_insert_id = array();

		if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			extract($row);
			$last_insert_id = $id;		
		}

		return $last_insert_id;
	}*/
}