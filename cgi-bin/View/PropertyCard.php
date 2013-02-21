<?php

class ViewPropertyCard {
	private $model;

	# background colors for the different groups
	private static $bcolors = [
		'1'	=> '#840274',
		'2'	=> '#1dfbf9',
		'3'	=> '#ec028c',
		'4'	=> '#fc961c',
		'5'	=> '#ec1e24',
		'6'	=> '#ece604',
		'7'	=> '#2c9a3c',
		'8'	=> '#1c529c',
		'RR'	=> '#ffffff',
		'U'	=> '#ffffff',
	];
	private static $color_overrides = [
		'1'	=> '#ffffff',
	];

	private static $regcolor = '#000000'; # text color for regular properties
	private static $nregcolor = '#005300'; # text color for non-regular properties

	function __construct ($model) {
		$this->model = $model;
	}

	public function getPropertyCardData ($space_id) {
		$json_data = $this->model->game->board->getSpaceInfo($space_id);

		if (is_numeric($json_data['group'])) {
			# regular property
			$json_data['type'] = 'regular';
			$col_ovr = @self::$color_overrides[$json_data['group']];
			if (isset($col_ovr)) {
				$json_data['color'] = $col_ovr;
			} else {
				$json_data['color'] = self::$regcolor;
			}
		} else {
			if ($json_data['group'] === 'RR') {
				$json_data['type'] = 'rail';
			} else if ($json_data['group'] === 'U') {
				$json_data['type'] = 'util';
			} else {
				# ERROR - not a valid property type
				return [];
			}
			$col_ovr = @self::$color_overrides[$json_data['group']];
			if (isset($col_ovr)) {
				$json_data['color'] = $col_ovr;
			} else {
				$json_data['color'] = self::$nregcolor;
			}
		}

		$json_data['bcolor'] = self::$bcolors[$json_data['group']];

		return $json_data;
	}
}
