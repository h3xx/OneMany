<?php

class ControllerLogin {
	private $model;

	function __construct ($model) {
		$this->model = $model;
	}

	# FIXME : use an actual URL
	private static $rst_url = 'http://localhost:801/t/passreset/responder.php';

	public function processInstruction ($instruction) {
		$buff = preg_split('/:/', $instruction);

		switch (@$buff[0]) {
			case 'reset':
				if (isset($buff[2])) {

				}
		}

		return [
			'result'=> false,
			'msg'	=> 'Invalid request.',
		];
	}

	public function requestResetPassword ($user_email) {
		# XXX : security: don't report any failure, just send email if it worked

		$user_id = $this->model->user->resolveUserEmail($user_email);

		if (!isset($user_id)) {
			return true;
		}

		$rst = $this->model->user->addPwResetRequest($user_id);

		if (!isset($rst)) {
			return true;
		}

		$url = self::$rst_url . '?method=tell&func=login&args=reset:'.$user_id.':'.urlencode($rst);

		mail($user_email, 'OneMany Password Reset',
			'<a href="'.htmlspecialchars($url).'">Reset your password</a>',
			'Content-Type: text/html; charset=utf8'
		);

		return true;
	}

	public function doResetPassword ($user_id, $reset_string, $new_pass) {
	}
}
