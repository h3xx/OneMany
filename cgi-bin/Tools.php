<?php

class Tools {
	public static $can_mail = false;
	public static $htdocs_path = '/htdocs'; # may be empty in production

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
}
