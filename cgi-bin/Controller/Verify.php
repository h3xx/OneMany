<?php

#require_once('../Tools.php');

class ControllerVerify {
	private $model;

	function __construct ($model) {
		$this->model = $model;
	}

	public function processInstruction ($instruction) {
		$buff = preg_split('/:/', $instruction);
		$user_id = @$buff[0];
		$verify_string = @$buff[1];
		return $this->doVerify($user_id, $verify_string);
	}

	private function doVerify ($user_id, $verify_string) {
		if (!isset($user_id) || !is_numeric($user_id) || !isset($verify_string)) {
			return [
				'result'=> false,
				'msg'	=> 'Invalid argument.',
			];
		}

		if (!$this->model->user->verifyLogin($user_id, $verify_string)) {
			return [
				'result'=> false,
				'msg'	=> 'Failed to mark the user verified.',
			];
		}

		return [
			'result'=> true,
			'msg'	=> 'Successfully verified the user.',
		];
	}
}
