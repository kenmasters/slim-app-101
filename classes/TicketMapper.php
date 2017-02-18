<?php

class TicketMapper {

	function __construct($db) {
		$this->db = $db;
		$this->params = [
			'error'                 => false, 
	        'status_code'           => 200, 
	        'msg'                   => '',
	        'form_errors'           => null,
	        'results'               => [],
	        'results_count'         => 0,
	        'is_logged'             => true,
	        'forced_login'          => false
		];
	}

	function index2() {
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

	public function index($request) {

		$selectStatement = $this->db->select()->from('tickets');
		// $stmt = $this->db->query("SELECT * FROM tickets");
		// $mem = memory_get_usage();
		// while($row = $stmt->fetch());
		// echo "Memory used: ".round((memory_get_usage() - $mem) / 1024 / 1024, 2)."M\n";
		// echo "Memory used: ".memory_get_usage();

		// exit('<br/>');

	    $id = $request->getQueryParam('id');
	    $name = $request->getQueryParam('name');
	    
	    if( $id ) {
	        $selectStatement->where('id', '=', $id);
	    }

	    if( $name ) {
	        $selectStatement->whereLike('name', "$name%");
	    }

	    $selectStatement->limit(1,0);

	    $stmt = $selectStatement->execute();
	    $tickets = $stmt->fetchAll();

	    if ( !$tickets ) {
	    	return $this->params;
	    }

	    // SETUP RESULTS
	    $this->params['results'] = $tickets;
        $this->params['results_count'] =count($tickets);
	    return $this->params;
	}

	function show($id) {
		$selectStatement = $this->db->select()->from('tickets')->where('id', '=', $id);
		$stmt = $selectStatement->execute();
		$ticket = $stmt->fetch();

		if ( !$ticket ) {
			return $this->params;
		}

		// SETUP RESULTS
        $this->params['results'] = $ticket;
        $this->params['results_count'] = 1;
		return  $this->params;
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