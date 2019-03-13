<?php

/**
 * User
 * The controller for user actions
 *
 * @author Steven Snyder <steveasnyder@gmail.com>
 */
class User extends Base {
	
	/**
	 * Default action
	 */
	public function index() {
		$this->login();
	}

	/**
	 * Perform user login
	 */
	public function login() {
		// Attempt login if email and password posted
		if (!empty($_POST['email']) and !empty($_POST['password'])) {
		
			// Build SP payload with hashed password
			$arg = array(
				'email' => $_POST['email'],
				'password' => hash('sha256', 'mysalt'.$_POST['password'])
			);
			
			// Call SP to validate user creds
			if ($user = $this->db->callSP('getUser', $arg)) {
				// Build SP payload with unique token id
				$arg = array(
					'user_id' => $user['id'],
					'token' => uniqid()
				);
				
				// Update user token
				$this->db->callSP('updateUserToken', $arg);
				
				// Set user cookie, never expires
				setcookie(USER_COOKIE, $arg['token'], 0, '/');
				
				// Redirect admin to order admin
				if ($user['admin']) {
					header('location: /order/admin');
				}
			} else {
				// Invalid creds
				$error = 'Invalid Username/Password';
			}
			
		}
		
		// Display login form
		include './tpl/user/login.html';
	}

}