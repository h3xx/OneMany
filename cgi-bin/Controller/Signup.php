<?php

class ControllerSignup {
	private $model;

	function __construct ($model) {
		$this->model = $model;
	}

	public function processInstruction ($instruction) {
		if (!isset($instruction) || !is_array($instruction)) {
			return [
				'result'=> false,
				'msg'	=> 'Invalid arguments for signup.',
			];
		}

		$user_name = @$instruction[0];
		$email = @$instruction[1];
		$password = @$instruction[2];

		if (!isset($user_name) || !strlen($user_name)) {
			return [
				'result'=> false,
				'msg'	=> 'Invalid user name.',
			];
		}

		if (!isset($email) ||
			!strlen($email) ||
			# email regex care of: http://www.regular-expressions.info/email.html
			# modified to be PCRE and case-insensitive
			!preg_match('/^[\w._%+-]+@[\w.-]+\.[A-z]{2,4}$/', $email)) {
			return [
				'result'=> false,
				'msg'	=> 'Invalid email.',
			];
		}

		if (!isset($password) || !strlen($password)) {
			return [
				'result'=> false,
				'msg'	=> 'Invalid password.',
			];
		}

		if (!$this->model->user->newLogin($user_name, $email, $password)) {
			return [
				'result'=> false,
				'msg'	=> 'Failed to create new login (may not be unique).',
			];
		}

		return [
			'result'=> true,
			'msg'	=> 'Successfully created login.',
		];
	}
}
