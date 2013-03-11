<?php

class ControllerCreate {
	private $model, $user_id;

	function __construct ($model, $user_id) {
		$this->model = $model;
		$this->user_id = $user_id;
	}

	public function processGameInstruction ($args) {
		if (!is_array($args)) {
			$args = preg_split('/:/', $args);
		}

		if (!isset($args['name'])) {
			return [
				'result'=> false,
				'msg'	=> 'No name given.',
			];
		}

		if (!$this->model->game->createGame($args['name'])) {
			return [
				'result'=> false,
				'msg'	=> 'Error creating game.',
			];
		}

		if (is_array(@$args['rules'])) {
			// IF UNSET, IDGAF - JUST USE DEFAULT RULES
			// dude, why are you yelling?
			foreach ($args['rules'] as $name => $val) {
				if (!$this->model->rules->setRule($name, $val)) {
					return [
						'result'=> false,
						'msg'	=> 'Failed to set rule \'' . $name . '\' to \'' . $val . '\'',
					];
				}
			}
		}

		return [
			'result'=> false,
			'msg'	=> 'gcpoop: ' . @$args['name'],
		];
	}
}
