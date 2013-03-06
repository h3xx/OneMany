<?php

require_once('ChanceDeck.php');
require_once('Board.php');

class ModelGame {
	private $model, $game_id;

	function __construct ($model, $game_id) {
		$this->model = $model;
		$this->game_id = $game_id;
	}

	/*private function newGame () {
		
		$sth = $this->model->prepare(
			'insert into game ("game_name") values (:name) '.
			'returning "game_id"' # return the last inserted row id as the result set
		);
		$sth->bindParam(':name', $this->game_name, PDO::PARAM_STR);

		if (!$sth->execute()) {
			return false;
		}
		
		# grab our game_id from the result set
		$res = $sth->fetch(PDO::FETCH_NUM);
		$this->game_id = $res[0];

		$sth_pop = $this->model->prepare(
			'select populate_game(:gid)'
		);
		$sth_pop->bindParam(':gid', $this->game_id, PDO::PARAM_INT);

		if (!$sth_pop->execute()) {
			return false;
		}
		$res = $sth_pop->fetch(PDO::FETCH_NUM);

		return $res[0];

		#logger("Game: inserted game named `{$this->game_name}' : game_id : {$this->game_id}");
	}*/

	private function getChance () {
		if (!isset($this->chance)) {
			$this->chance = new ModelChanceDeck($this->model, $this->game_id);
		}
		return $this->chance;
	}

	private function getCommChest () {
		if (!isset($this->commchest)) {
			#$this->commchest = new ModelCommChestDeck($this->model, $this->game_id);
		}
		return $this->commchest;
	}

	private function getBoard () {
		if (!isset($this->board)) {
			$this->board = new ModelBoard($this->model, $this->game_id);
		}
		return $this->board;
	}

	public function exportModel () {
		$state = $this->getGameState();
		$gameinfos = $this->getGameNameAndLastRoll();
		$auction = $this->getAuctionInfoNoExpired();
		$board = $this->board->exportModel();
		$users = $this->model->user->exportModel();

		return [
			'state'	=> $state,
			'name'	=> $gameinfos['name'],
			'roll'	=> $gameinfos['roll'],
			'auction'=> $auction,
			'board'	=> $board,
			'users'	=> $users,
		];
	}

	public function getAuctionInfo () {
		# deprecated
		$sth = $this->model->prepare(
			'select '.
			'"auction_user" as "auser", '.
			'"auction_space" as "aspace", '.
			'"auction_bid" as "abid", '.
			'"auction_expire" as "aexpire" '.
			'from "game" '.
			'where "game_id" = :gid'
		);

		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_ASSOC);

		return @$result[0];

	}

	public function getAuctionInfoNoExpired () {
		$sth = $this->model->prepare(
			'select '.
			'"auction_user", '.
			'"auction_space", '.
			'"auction_bid", '.
			'"auction_expire" '.
			'from "game" '.
			'where "game_id" = :gid and '.
			# make sure it hasn't expired
			'"auction_expire" > now() + rule_or_default(:ggid,\'auction_timeout\')::interval'

		);

		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);
		$sth->bindParam(':ggid', $this->game_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_NUM);

		return isset($result[0]) ? $result[0] : []; # empty array if no auction

	}

	public function getGameState () {
		$sth = $this->model->prepare(
			'select max("game_newstate") from "game_update" '.
			'where "game_id" = :gid'
		);

		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_NUM);

		return isset($result[0]) ? (int)$result[0] : 0;
	}

	# apologies for the horrible name
	public function getGameNameAndLastRoll () {
		$sth = $this->model->prepare(
			'select '.
			'"game_name" as "name", '.
			'array_to_json("last_roll") as "roll" '.
			'from "game" '.
			'where "game_id" = :gid'
		);

		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_ASSOC);

		# decode last_roll
		if (isset($result['roll'])) {
			$result['roll'] = json_decode($result['roll'], true);
		}

		return $result;
	}

	public function getLastRoll () {
		# deprecated
		$sth = $this->model->prepare(
			'select "last_roll" from "game" '.
			'where "game_id" = :gid'
		);

		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_NUM);

		# convert to array
		$roll = @$result[0];

		if (!isset($roll)) {
			return $roll;
		}

		# e.g. '{3,4}' -> [ 3, 4 ]
		preg_match_all('/\d+/', $roll, $m);
		# $m is multidimensional array
		return $m[0];
	}

	public function getUserOnSpace ($user_id) {
		$sth = $this->model->prepare(
			'select "on_space" '.
			'from "c_user_game" '.
			'where "game_id" = :gid and "user_id" = :uid'
		);

		$sth->bindParam(':uid', $user_id, PDO::PARAM_INT);
		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_NUM);

		return @$result[0];
	}

	public function isUserInGame ($user_id) {
		$sth = $this->model->prepare(
			'select count("user_id") from "c_user_game" '.
			'where "user_id" = :uid '.
			'and "game_id" = :gid'
		);

		$sth->bindParam(':uid', $user_id, PDO::PARAM_INT);
		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_NUM);

		return @$result[0];
	}


	public function doRoll ($user_id, $num_dice) {

		$rolls = [];
		for ($throw = 0; $throw < $num_dice; ++$throw) {
			$rolls []= rand(1, 6);
		}

		# format for insertion
		$last_roll = '{' . implode(',', $rolls) . '}';

		$sth = $this->model->prepare(
			'update "game" '.
			'set "last_roll" = :lroll '.
			'where "game_id" = :gid'
		);

		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);
		$sth->bindParam(':lroll', $last_roll, PDO::PARAM_STR);

		if (!$sth->execute()) {
			return false;
		}

		# XXX : tell update module about it
		if (!$this->model->update->pushUpdate([
			'type'	=> 'roll',
			'val'	=> $rolls,
			'id'	=> $user_id,
		])) {
			return false;
		}

		return $rolls;
	}

	public function rotateTurn () {
		$sth = $this->model->prepare(
			'select update_turn(:gid)'
		);

		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_NUM);

		$user_id = @$result[0];

		if (!isset($user_id)) {
			return false;
		}

		# XXX : tell update module about it
		if (!$this->model->update->pushUpdate([
			'type'	=> 'turn',
			'id'	=> $user_id,
		])) {
			return false;
		}

		return $user_id;
	}

	public function giveExtraTurn ($user_id) {
		$sth = $this->model->prepare(
			'update "c_user_game" '.
			'set "extra_turn" = true '.
			'where "game_id" = :gid '.
			'and "user_id" = :uid'
		);

		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);
		$sth->bindParam(':uid', $user_id, PDO::PARAM_INT);

		return $sth->execute();
	}

	public function setWhoseTurn ($user_id) {
		# deprecated
		$sth = $this->model->prepare(
			'select set_turn(:gid, :uid)'
		);

		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);
		$sth->bindParam(':uid', $user_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		# XXX : tell update module about it
		return $this->model->update->pushUpdate([
			'type'	=> 'turn',
			'id'	=> $user_id,
		]);
	}

	public function whoseTurn () {
		$sth = $this->model->prepare(
			'select "user_id" '.
			'from "c_user_game" '.
			'where "game_id" = :gid '.
			'order by "sequence" '.
			'limit 1'
		);

		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_NUM);

		return @$result[0];
	}

	public function setAuctionBid ($space_id, $user_id, $bid) {
		$sth = $this->model->prepare(
			'update "game" '.
			'set '.
				'"auction_space" = :sid, '.
				'"auction_user" = :uid, '.
				'"auction_bid" = :bd '.
			'where "game_id" = :gid'
		);

		$sth->bindParam(':uid', $user_id, PDO::PARAM_INT);
		$sth->bindParam(':sid', $space_id, PDO::PARAM_INT);
		$sth->bindParam(':bd', $bid, PDO::PARAM_INT);
		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		return $this->model->update->pushUpdate([
			'type'	=> 'bid',
			'id'	=> $user_id,
			'space'	=> $space_id,
			'bid'	=> $bid,
		]);
	}

	function __get ($name) {
		switch ($name) {
			case 'chance':
				return $this->getChance();
				break;
				;;
			case 'commchest':
				return $this->getCommChest();
				break;
				;;
			case 'board':
				return $this->getBoard();
				break;
				;;
			#case 'game_id':
			#	return $this->game_id;
			#	;;
		}
	}
}
