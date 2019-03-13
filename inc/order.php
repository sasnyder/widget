<?php

/**
 * Order
 * The controller for orde actions
 *
 * @author Steven Snyder <steveasnyder@gmail.com>
 */
class Order extends Base {
	
	/**
	 * Default action
	 */
	public function index() {
		$this->form();
	}
	
	/**
	 * Display order form and process submission
	 */
	public function form() {
		// Get form options
		$this->data['color'] = $this->db->callSP('getColor');
		$this->data['type']  = $this->db->callSP('getType');
		
		// Validate form submission if posted
		if ($order = $_POST and !$this->data['error'] = $this->validate($_POST)) {
			// Generate unique order ID
			$order['unique_id'] = uniqid();
			
			// Create new order
			$this->data['order'] = $this->db->callSP('createOrder', $order);
			
			// Send email confirmation
			ob_start();
			include './tpl/order/email.html';
			$message = ob_get_clean();
			@mail($order['email'], 'Widget Order', $message);   // suppress local warning
			
			// Display thank you page and order detail
			include './tpl/order/confirmation.html';
			include './tpl/order/detail.html';
		} else {
			// Display order form
			include './tpl/order/form.html';
		}
	}
	
	/**
	 * Retrieve and display order details
	 *
	 * @param integer @id  The unique order ID
	 */
	public function detail($id) {
		// Try finding order
		if ($id and $this->data['order'] = $this->db->callSP('getOrder', array('unique_id' => $id))) {
			// Display order details
			include './tpl/order/detail.html';
		} else {
			// Order not found
			include './tpl/404.html';
		}
	}	
	
	/**
	 * Display all orders, admin only
	 */
	public function admin() {
		// Check for admin flag
		if (!$this->isAdmin) {
			header('location: /user/login');
		}
		
		// Get status options
		$this->data['status'] = $this->db->callSP('getStatus');
		
		// Display all orders
		$this->data['order'] = $this->db->callSP('getAllOrders');
		include './tpl/order/admin.html';
	}
	
	/**
	 * Change the status of an order, admin only, async
	 */
	public function updateStatus() {
		// All parameters required
		if (empty($_GET['token']) or empty($_GET['unique_id']) or empty($_GET['status_id'])) {
			die(json_encode(array('status' => 'error', 'message' => 'Missing parameters','unique_id' => $_GET['unique_id'])));
		}
		
		// Validate admin user token
		if (!$user = $this->db->callSP('getUserByToken', array('token' => $_GET['token'])) or !$user['admin']) {
			die(json_encode(array('status' => 'error', 'message' => 'Admin only', 'unique_id' => $_GET['unique_id'])));
		}
		
		// Validate status ID
		$status = $this->db->callSP('getStatus');
		if (array_search($_GET['status_id'], array_column($status, 'id')) === false) {
			die(json_encode(array('status' => 'error', 'message' => 'Invalid status', 'unique_id' => $_GET['unique_id'])));
		}
		
		// Build SP payload
		$arg = array(
			'unique_id' => $_GET['unique_id'],
			'status_id' => $_GET['status_id']
		);
		
		// Update order status
		$this->db->callSP('updateOrderStatus', $arg);
		
		// Send success message
		die(json_encode(array('status' => 'success', 'message' => 'Status updated', 'unique_id' => $_GET['unique_id'])));
	}
	
	/**
	 * Validate the order form
	 *
	 * @param array $form  The order form
	 * @return array $error  The errors found in the order
	 */
	private function validate($form) {
		$error = array();
	
		// Is email not valid?
		if (!filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
			$error['email'] = 'Email is invalid';
		}
		
		// Was email provided?
		if (!$form['email']) {
			$error['email'] = 'Email is required';
		}
		
		// Is quantity an integer?
		if (!filter_var($form['quantity'], FILTER_VALIDATE_INT)) {
			$error['quantity'] = 'Quantity must be an integer';
		}
		
		// Is quantity postive?
		if ($form['quantity'] <= 0) {
			$error['quantity'] = 'Quantity must be positive';
		}
		
		// Was quantity provided?
		if (!$form['quantity']) {
			$error['quantity'] = 'Quantity is required';
		}
		
		// Is color valid?
		if (array_search($form['color_id'], array_column($this->data['color'], 'id')) === false) {
			$error['color_id'] = 'Color is invalid';
		}
		
		// Was color provided?
		if (!$form['color_id']) {
			$error['color_id'] = 'Color is required';
		}
		
		// Is date needed 1 week from today?
		if ($form['needed'] < date('Y-m-d', strtotime('+7 days'))) {
			$error['needed'] = 'Date Needed must be at least one week from today';
		}
		
		// Was date needed provided?
		if (!$form['needed']) {
			$error['needed'] = 'Date Needed is required';
		}
		
		// Is widget type valid?
		if (array_search($form['type_id'], array_column($this->data['type'], 'id')) === false) {
			$error['type_id'] = 'Widget Type is invalid';
		}
		
		// Was widget type provided?
		if (!$form['type_id']) {
			$error['type_id'] = 'Widget Type is required';
		}
		
		return $error;
	}
	
}