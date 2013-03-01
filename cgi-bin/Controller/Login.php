<?php

require_once('../Tools.php');

class ControllerLogin {
	private $model;

	function __construct ($model) {
		$this->model = $model;
	}

	# FIXME : use an actual URL
	private static $rst_url = 'http://localhost:801/tricks/passreset/pwreset.php';

	public function processInstruction ($instruction) {
		$buff = preg_split('/:/', $instruction);

		switch (@$buff[0]) {
			case 'reset':
				if (isset($buff[2])) {
					return $this->doResetPassword($buff[1], $buff[2], $buff[3]);

				}
				break;
				;;
			case 'request':
				if (!isset($buff[1])) {
					return [
						'result'=> false,
						'msg'	=> 'No email address specified.',
					];
				}
				return $this->requestResetPassword($buff[1]);
				break;
				;;
		}

		return [
			'result'=> false,
			'msg'	=> 'Invalid request.',
		];
	}

	public function requestResetPassword ($user_email) {
		# XXX : security: don't report any failure, just send email if it worked
		$response = [
			'result'=> true,
			'msg'	=> 'Submitted.',
		];

		$user_id = $this->model->user->resolveUserEmail($user_email);

		if (!isset($user_id)) {
			$response['msg'] .= ' no uid'; # debugging
			return $response;
		}

		$rst = $this->model->user->addPwResetRequest($user_id);

		if (!isset($rst)) {
			$response['msg'] .= ' request failed'; # debugging
			return $response;
		}

		$url = self::$rst_url . '?args='.$user_id.':'.urlencode($rst);

		if (Tools::$can_mail) {
			$subject = 'OneMany Password Reset';
			$headers = [
				'From: chudanj@dunwoody.edu',
				'Content-Type: text/html; charset=utf8',
			];
			$content = '<a href="'.htmlspecialchars($url).'">Reset your password</a>';
			if (!mail($user_email, $subject, $content, implode("\r\n", $headers))) {
				$response['msg'] .= ' mailing failed'; # debugging
				return $response;
			}
		} else {
			$response['msg'] .= ' ' . $url; # debugging
		}

		return $response;
	}

	public function doResetPassword ($user_id, $reset_string, $new_pass) {
		$result = $this->model->user->doPwReset($user_id, $reset_string, $new_pass);
		if (!$result) {
			return [
				'result'=> false,
				'msg'	=> 'Failed to set password.',
			];
		}

		return [
			'result'=> true,
			'msg'	=> 'Successfully set new password.',
		];
	}
}
