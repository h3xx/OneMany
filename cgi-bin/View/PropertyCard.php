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
	# text color overrides for different groups
	private static $tcolor_overrides = [
		'1'	=> '#ffffff',
	];

	private static $regcolor = '#000000'; # text color for regular properties
	private static $nregcolor = '#005300'; # text color for non-regular properties

	function __construct ($model) {
		$this->model = $model;
	}

	private static function selectColorsTypeForGroup ($group_name) {
		$buff = [];
		if (is_numeric($group_name)) {
			# regular property
			$buff['type'] = 'regular';
			$col_ovr = @self::$tcolor_overrides[$group_name];
			if (isset($col_ovr)) {
				$buff['color'] = $col_ovr;
			} else {
				$buff['color'] = self::$regcolor;
			}
		} else {
			if ($group_name === 'RR') {
				$buff['type'] = 'rail';
			} else if ($group_name === 'U') {
				$buff['type'] = 'util';
			} else {
				# ERROR - not a valid property type
				return [];
			}
			$col_ovr = @self::$tcolor_overrides[$group_name];
			if (isset($col_ovr)) {
				$buff['color'] = $col_ovr;
			} else {
				$buff['color'] = self::$nregcolor;
			}
		}

		$buff['bcolor'] = self::$bcolors[$group_name];

		return $buff;
	}

	public function getPropertyCardData ($space_id) {
		$json_data = $this->model->game->board->getSpaceAndOwnershipInfo($space_id);

		if (!is_array($json_data)) {
			return null;
		}

		return array_merge($json_data, self::selectColorsTypeForGroup($json_data['group']));
	}
}
