<?php

class ModelCommchestDeck {
	private $model, $deck, $game_id;

	private static $table_name = 'commchest',
		       $deck_table_name = 'c_game_commchest';

	function __construct ($model, $game_id) {
		$this->model = $model;
		$this->game_id = $game_id;
	}

	public function drawCard ($user_id) {
		$sth = $this->model->prepare(
			'select '.
			'"RECORDID" as "id", '.
			'"TEXT" as "text", '.
			'"RESULT" as "action" from '.
			'('.self::$table_name.' left join '.self::$deck_table_name.' on ('
				.self::$table_name.'."RECORDID" = '
				.self::$deck_table_name.'."'.self::$table_name.'_recordid")) '.
			'where "game_id" = :gid and "drawn_by" is null '.
			'order by "sequence" asc '.
			'limit 1'
		);

		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_ASSOC);

		if ($result['action'] === 'GOJF') {
			# TODO : mark card drawn???
			$this->model->game->setGojf($user_id, true);
		}
		$this->moveCardToBack($result['id']);

		return $result;
	}

	public function moveCardToBack ($card_id) {
		$sth = $this->model->prepare(
			'update '.self::$deck_table_name.' set '.
				'"drawn_by" = null, '.
				'"sequence" = ('.
					'select max("sequence") from '.self::$deck_table_name.
					' where "game_id" = :gid'.
				') + 1 '.
				'where "game_id" = :gida and "'.self::$table_name.'_recordid" = :cid'
		);
		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);
		$sth->bindParam(':gida', $this->game_id, PDO::PARAM_INT);
		$sth->bindParam(':cid', $card_id, PDO::PARAM_INT);
		if (!$sth->execute()) {
			return false;
		}

		# XXX : update internal structure too?
		return true;
	}

	public function markCardNotDrawn ($card_id) {
		$this->markCardDrawnStatus($card_id, null);
	}

	public function markCardDrawn ($card_id, $user_id) {
		$this->markCardDrawnStatus($card_id, $user_id);
	}

	private function markCardDrawnStatus ($card_id, $user_id) {
		$sth = $this->model->prepare(
			'update '.self::$deck_table_name.' set "drawn_by" = :uid '.
			'where "game_id" = :gid and "'.self::$table_name.'_recordid" = :cid'
		);

		$sth->bindParam(':uid', $user_id, PDO::PARAM_INT);
		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);
		$sth->bindParam(':cid', $card_id, PDO::PARAM_INT);

		return $sth->execute();
	}
}
