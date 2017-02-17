<?php

class TicketMapper {

	function __construct($pdo) {
		$this->pdo = $pdo;
	}

	function index() {
		$params = [];
		$q = 'SELECT * FROM tickets WHERE 1';

		// $stmt = $this->pdo->prepare('SELECT * FROM tickets');
		
		if (isset($_GET['name']) && $_GET['name'] ) {

			$params['name'] = $_GET['name'];
			// $stmt = $this->pdo->prepare('SELECT * FROM tickets WHERE name = :name');
			// $stmt->bindParam(':name', $_GET['name']);
		}

		if( $params ) {
			foreach ($params as $key => $value) {
				$q .= sprintf(' AND `%s` = :%s' , $key, $key);
			}
		}

		$stmt = $this->pdo->prepare($q);

		if( $params ) {
			foreach ($params as $key => $value) {
				$stmt->bindParam(':'.$key, $value);
			}
		}

		$stmt->execute();
		$tickets = $stmt->fetchAll();
		// return json_encode($tickets);
		return $tickets;
	}

	function show($id) {
		$stmt = $this->pdo->prepare('SELECT * FROM tickets WHERE id = :id');
		$stmt->execute(['id' => $id]);
		$ticket = $stmt->fetchObject();
		// return json_encode($ticket);
		return $ticket;
	}

	function store() {
		$input = $request->getParsedBody();
		$sql = "INSERT INTO tickets (name, created_at, updated_at) VALUES (:name, NOW(), NOW())";
		 $sth = $this->db->prepare($sql);
		$sth->bindParam("name", $input['name']);
		$sth->execute();
		$input['id'] = $this->db->lastInsertId();
		return $response->withJson($input);
	}
	
	function update($id) {}
	function destroy($id) {}

}

// $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? AND status=?');
// $stmt->execute([$email, $status]);
// $user = $stmt->fetch();
// // or
// $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email AND status=:status');
// $stmt->execute(['email' => $email, 'status' => $status]);
// $user = $stmt->fetch();