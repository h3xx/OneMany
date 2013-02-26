<?php

class Tools {
	public static function encodeJson ($json_data) {
		return json_encode($json_data, JSON_UNESCAPED_UNICODE);
	}
}
