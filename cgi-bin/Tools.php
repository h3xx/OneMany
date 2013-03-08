<?php

class Tools {
	public static
			$can_mail = false,
			$htdocs_path = '/htdocs'; # XXX : may be empty in production

	public static function encodeJson ($json_data) {
		return json_encode($json_data, JSON_UNESCAPED_UNICODE);
	}

	public static function absUrl ($subpath) {
		return sprintf('%s://%s%s%s',
			$_SERVER['REQUEST_SCHEME'],
			$_SERVER['HTTP_HOST'],
			self::$htdocs_path,
			$subpath
		);
	}

	public static function loginUrl () {
		return self::absUrl('/user/login.php');
	}
}
