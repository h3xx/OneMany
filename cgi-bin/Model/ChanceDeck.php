<?php

class ModelChanceDeck {
	private $model, $deck, $game_id;

	private static $table_name = 'chance',
		       $deck_table_name = 'c_game_chance';

	function __construct ($model, $game_id) {
		$this->model = $model;
		$this->game_id = $game_id;
	}

	function init () {
		# XXX : method stub
		#       ( Nothing to do here! )
		#       v
		#    /\o
		#     /\/
		#    /\
		#   /  \
		#  LOL LOL
	}

	# initial creation inside the database
	function create () {
		$this->newDeck();
	}

	private function getCards ($limit) {
		if (!isset($this->deck)) {
			## don't give a shit about ordering
			#$sth = $this->model->prepare('select "RECORDID", "TEXT", "RESULT" from '.self::$table_name);

			$sth = $this->model->prepare(
				'select "RECORDID", "TEXT", "RESULT" from'.
				'('.self::$table_name.' left join '.self::$deck_table_name.' on ('
					.self::$table_name.'."RECORDID" = '
					.self::$deck_table_name.'."chance_recordid"))'.
				'where "game_id" = :gid and not "is_drawn"'.
				'order by "sequence" asc'.
				($limit > 0 ? ' limit '.$limit : '')
			);

			$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);

			$sth->execute();
			$this->deck = $sth->fetchAll(PDO::FETCH_ASSOC);
		}
		return $this->deck;
	}

	public function drawCard () {
		# TODO : implement
		$card = $this->getCards(1);
	}

	public function moveCardToBack ($card_id) {
		$sth = $this->model->prepare(
			'update '.self::$deck_table_name.' set '.
				'"is_drawn" = false, '.
				'"sequence" = ('.
					'select max("sequence") from '.self::$deck_table_name.
					' where "game_id" = :gid'.
				') + 1 '.
				'where "game_id" = :gida and "chance_recordid" = :cid'
		);
		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);
		$sth->bindParam(':gida', $this->game_id, PDO::PARAM_INT);
		$sth->bindParam(':cid', $card_id, PDO::PARAM_INT);
		$sth->execute();

		# XXX : update internal structure too?
	}

	public function markCardNotDrawn ($card_id) {
		$this->markCardDrawnStatus($card_id, false);
	}

	public function markCardDrawn ($card_id) {
		$this->markCardDrawnStatus($card_id, true);
	}

	private function markCardDrawnStatus ($card_id, $status) {
		$sth = $this->model->prepare(
			'update '.self::$deck_table_name.' set "is_drawn" = :drawn '.
			'where "game_id" = :gid and "chance_recordid" = :cid'
		);

		$sth->bindParam(':drawn', $status, PDO::PARAM_BOOL);
		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);
		$sth->bindParam(':cid', $card_id, PDO::PARAM_INT);
	}

	private function deleteDeck () {
		$sth = $this->model->prepare('delete from '.self::$deck_table_name.' where "game_id" = :gid');
		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);
		$sth->execute();
	}

	private function newDeck () {
		$this->deleteDeck();
		
		$sth = $this->model->prepare('select "RECORDID" from '.self::$table_name.' order by random()');
		$sth->execute();
		$ids = $sth->fetchAll(PDO::FETCH_NUM);

		$sth_ins = $this->model->prepare('insert into '.self::$deck_table_name.
			' ("game_id", "chance_recordid", "sequence")'.
			' values (:gid, :cid, :seq)');
		$sth_ins->bindParam(':gid', $this->game_id, PDO::PARAM_INT);

		foreach ($ids as $seq => $row) {
			$cid = $row[0];
			$sth_ins->bindParam(':cid', $cid, PDO::PARAM_INT);
			$sth_ins->bindParam(':seq', $seq, PDO::PARAM_INT);
			$sth_ins->execute();
		}
	}

	function __get ($name) {
		switch ($name) {
			case 'deck':
				return $this->getCards(-1);
				;;
		}
	}
}
