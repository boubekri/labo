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

		$stmt->execute();

		$this->last_insert_id = $this->conn->lastInsertId();

		// execute query
		return ($stmt->execute())? true: false;
	}

	public function getLastInsertId() {

		return $this->last_insert_id;
	}
}