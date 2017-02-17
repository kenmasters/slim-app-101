<?php

class TicketMapper {

	function __construct($pdo) {
		$this->pdo = $pdo;
	}

	function index() {
		
		$stmt = $this->pdo->prepare('SELECT * FROM tickets');
		
		if (isset($_GET['name']) && $_GET['name'] ) {
			$stmt = $this->pdo->prepare('SELECT * FROM tickets WHERE name = :name');
			$stmt->bindParam(':name', $_GET['name']);
		}

		$stmt->execute();
		$tickets = $stmt->fetchAll();
		return json_encode($tickets);
	}

	function show($id) {
		$stmt = $this->pdo->prepare('SELECT * FROM tickets WHERE id = :id');
		$stmt->execute(['id' => $id]);
		$ticket = $stmt->fetch();
		return json_encode($ticket);
	}

	function getTickets() {
		$stmt = $this->pdo->prepare('SELECT * FROM tickets');
		$stmt->execute();
		$tickets = $stmt->fetchAll();
		return json_encode($tickets);
	}
}

// $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? AND status=?');
// $stmt->execute([$email, $status]);
// $user = $stmt->fetch();
// // or
// $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email AND status=:status');
// $stmt->execute(['email' => $email, 'status' => $status]);
// $user = $stmt->fetch();