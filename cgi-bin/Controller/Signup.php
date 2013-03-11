<?php

#require_once('../Tools.php');

class ControllerSignup {
	private $model;

	function __construct ($model) {
		$this->model = $model;
	}

	# FIXME : use an actual URL
	private static $vfy_url = '/user/verify.php';

	public function processInstruction ($instruction) {
		if (!isset($instruction) || !is_array($instruction)) {
			return [
				'result'=> false,
				'msg'	=> 'Invalid arguments for signup.',
			];
		}

		# making a new login
		$user_name = @$instruction[0];
		$email = @$instruction[1];
		$password = @$instruction[2];
		return $this->doSignup($user_name, $email, $password);
	}

	private function doSignup ($user_name, $email, $password) {

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

		$verify_string = $this->model->user->newLogin($user_name, $email, $password);

		if (!isset($verify_string)) {
			return [
				'result'=> false,
				'msg'	=> 'Failed to create new login (may not be unique).',
			];
		}

		$user_id = $this->model->user->resolveUsername($user_name);

		$url = Tools::absUrl(self::$vfy_url) . '?args='.$user_id.':'.urlencode($verify_string);

		if (Tools::$can_mail) {
			$subject = 'OneMany - Please Verify Your Email';
			$headers = [
				'From: chudanj@dunwoody.edu',
				'Content-Type: text/html; charset=utf8',
			];
			$content = '<a href="'.htmlspecialchars($url).'">Verify your email.</a>';
			if (!mail($email, $subject, $content, implode("\r\n", $headers))) {
				# TODO : just mark as verified - OR - better messages
				return [
					'result'=> true,
					'msg'	=> 'Failed to send email. '.$url,
				];
			}
			return [
				'result'=> true,
				'msg'	=> 'Check email for verification.',
			];
		} else {
			# TODO : better messages
			return [
				'result'=> true,
				'msg'	=> 'Unable to send email. Go here to verify your account: '.$url,
			];
		}


		return [
			'result'=> true,
			'msg'	=> 'Successfully created login.',
		];
	}
}
