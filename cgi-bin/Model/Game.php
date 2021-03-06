<?php

require_once('ChanceDeck.php');
require_once('CommChestDeck.php');
require_once('Board.php');
require_once('Auction.php');

class ModelGame {
	private $model, $game_id;

	function __construct ($model, $game_id) {
		$this->model = $model;
		$this->game_id = $game_id;
	}

	# XXX:CAVEAT : all submodels will have an incorrect game_id value once a game has been created
	public function createGame ($name) {
		$sth = $this->model->prepare(
			'insert into "game" ("game_name") '.
			'values (:name) '.
			'returning "game_id"' # return the last inserted row id as the result set
		);
		$sth->bindParam(':name', $name, PDO::PARAM_STR);

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

		return $this->game_id;
	}

	public function getGamesList ($my_user_id) {
		if (!isset($my_user_id)) {
			return $this->_getGamesListNoUser();
		}
		return $this->_getGamesListWithUser($my_user_id);
	}

	private function _getGamesListNoUser () {
		$sth = $this->model->prepare(
			'select '.
				'"game"."game_id" as "id", '.
				'"game"."game_name" as "name", '.
				'"foo"."sz", '.
				'rule_or_default("game"."game_id", \'min_players\') as "sz_min", '.
				'rule_or_default("game"."game_id", \'max_players\') as "sz_max" '.
			'from game '.
			'left join ( '.
				'select '.
					'"game_id", '.
					'count(*) as "sz" '.
				'from "c_user_game" '.
				'group by "game_id" '.
			') as "foo" '.
			'on ("game"."game_id" = "foo"."game_id") '.
			'order by "game"."game_id" desc'
		);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetchAll(PDO::FETCH_ASSOC);

		for ($i = 0; $i < count($result); ++$i) {
			# XXX : overcome PDO bug re: all column types being strings
			$result[$i]['id'] = (int)$result[$i]['id'];
			$result[$i]['sz'] = (int)$result[$i]['sz'];
			$result[$i]['sz_min'] = (int)$result[$i]['sz_min'];
			$result[$i]['sz_max'] = (int)$result[$i]['sz_max'];
		}

		return $result;
	}

	private function _getGamesListWithUser ($user_id) {
		$sth = $this->model->prepare(
			'select '.
				'"game"."game_id" as "id", '.
				'"game"."game_name" as "name", '.
				'"foo"."sz", '.
				'rule_or_default("game"."game_id", \'min_players\') as "sz_min", '.
				'rule_or_default("game"."game_id", \'max_players\') as "sz_max", '.
				'is_user_in_game("game"."game_id", :uid) as "ingame" '.
			'from game '.
			'left join ( '.
				'select '.
					'"game_id", '.
					'count(*) as "sz" '.
				'from "c_user_game" '.
				'group by "game_id" '.
			') as "foo" '.
			'on ("game"."game_id" = "foo"."game_id") '.
			'order by "game"."game_id" desc'
		);

		$sth->bindParam(':uid', $user_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetchAll(PDO::FETCH_ASSOC);

		for ($i = 0; $i < count($result); ++$i) {
			# XXX : overcome PDO bug re: all column types being strings
			$result[$i]['id'] = (int)$result[$i]['id'];
			$result[$i]['sz'] = (int)$result[$i]['sz'];
			$result[$i]['sz_min'] = (int)$result[$i]['sz_min'];
			$result[$i]['sz_max'] = (int)$result[$i]['sz_max'];
			$result[$i]['ingame'] = (boolean)$result[$i]['ingame'];
		}

		return $result;
	}

	public function exportModel () {
		$state = $this->getGameState();
		$gameinfos = $this->getGameNameAndLastRoll();
		$auction = $this->auction->exportModel();
		$board = $this->board->exportModel();
		$free_parking = $this->getFreeParking();
		$users = $this->model->user->exportModel();
		$last_update = $this->model->update->exportModel();

		return [
			'state'	=> $state,
			'name'	=> $gameinfos['name'],
			'roll'	=> $gameinfos['roll'],
			'auction'=> $auction,
			'board'	=> $board,
			'free_parking' => $free_parking,
			'users'	=> $users,
			'update'=> $last_update,
		];
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

	public function numPlayers () {
		$sth = $this->model->prepare(
			'select '.
			'count(*) '.
			'from c_user_game '.
			'where "game_id" = :gid'
		);

		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_NUM);

		return @$result[0];
	}

	public function hasEnoughPlayers () {
		$sth = $this->model->prepare(
			'select '.
			'count(*) >= rule_or_default(:gid,\'min_players\')::integer '.
			'from c_user_game '.
			'where "game_id" = :ggid'
		);

		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);
		$sth->bindParam(':ggid', $this->game_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_NUM);

		return @$result[0];
	}

	public function isFull ($game_id) {
		$sth = $this->model->prepare(
			'select '.
			'count(*) > rule_or_default(:gid,\'max_players\')::integer '.
			'from c_user_game '.
			'where "game_id" = :ggid'
		);

		$sth->bindParam(':gid', $game_id, PDO::PARAM_INT);
		$sth->bindParam(':ggid', $game_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_NUM);

		return @$result[0];
	}

	public function transferCash ($from_uid, $to_uid, $amt) {
		return $this->model->user->addUserCash($from_uid, -$amt) &&
			$this->model->user->addUserCash($to_uid, $amt);
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

	public function allUidsInGame () {
		$sth = $this->model->prepare(
			'select "user_id" '.
			'from "c_user_game" '.
			'where "game_id" = :gid'
		);
		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);
		if (!$sth->execute()) {
			return false;
		}

		$ids = $sth->fetchAll(PDO::FETCH_COLUMN, 0);

		return $ids;
	}

	public function allUidsInGameNoBankrupt () {
		$sth = $this->model->prepare(
			'select "user_id" '.
			'from "c_user_game" '.
			'where "game_id" = :gid and "cash" > 0'
		);
		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);
		if (!$sth->execute()) {
			return false;
		}

		$ids = $sth->fetchAll(PDO::FETCH_COLUMN, 0);

		return $ids;
	}

	public function hasWinner () {
		return count($this->allUidsInGameNoBankrupt()) == 1;
	}

	public function isUserInGame ($user_id) {
		return $this->_isUserInGame($this->game_id, $user_id);
	}

	private function _isUserInGame ($game_id, $user_id) {
		$sth = $this->model->prepare(
			'select is_user_in_game(:gid, :uid)'
		);

		$sth->bindParam(':uid', $user_id, PDO::PARAM_INT);
		$sth->bindParam(':gid', $game_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_NUM);

		return @$result[0];
	}

	public function awardFreeParking ($user_id) {
		if (!$this->model->rules->getRuleValue('free_parking')) {
			return true;
		}

		$sth = $this->model->prepare(
			'select "free_parking" '.
			'from "game" '.
			'where "game_id" = :gid'
		);

		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_NUM);
		$fp = @$result[0];

		if (is_numeric($fp) && $fp > 0) {
			$this->setFreeParking(0);
			return $this->model->user->addUserCash($user_id, $fp);
		}
	}

	public function payToFreeParking ($user_id, $amt) {
		return 
			$this->addFreeParking($amt) &&
			$this->model->user->addUserCash($user_id, -$amt);
	}

	public function addFreeParking ($amt) {
		$sth = $this->model->prepare(
			'update "game" '.
			'set "free_parking" = "free_parking" + :fpd '.
			'where "game_id" = :gid'
		);

		$sth->bindParam(':fpd', $amt, PDO::PARAM_INT);
		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);

		return $sth->execute();
	}

	public function setFreeParking ($amt) {
		$sth = $this->model->prepare(
			'update "game" '.
			'set "free_parking" = :fp '.
			'where "game_id" = :gid'
		);

		$sth->bindParam(':fp', $amt, PDO::PARAM_INT);
		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);

		return $sth->execute();
	}

	public function getFreeParking () {
		$sth = $this->model->prepare(
			'select "free_parking" from "game" '.
			'where "game_id" = :gid'
		);

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
			'select rotate_turn(:gid)'
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


	public function askBuy ($user_id, $space_id) {
		return $this->model->update->pushUpdate([
			'type'	=> 'askBuy',
			'who'	=> $user_id,
			'space'	=> $space_id,
		]);
	}

	public function noBuy ($user_id, $space_id) {
		return $this->model->update->pushUpdate([
			'type'	=> 'noBuy',
			'who'	=> $user_id,
			'space'	=> $space_id,
		]);
	}

	public function payBail ($user_id) {
		$jail_bail = $this->model->rules->getRuleValue('jail_bail');

		#$newcash = $this->model->user->addUserCash($user_id, -$jail_bail);
		$this->payToFreeParking($user_id, $jail_bail);

		return
			$this->model->user->setInJail($user_id, false) &&
			$this->model->update->pushUpdate([
				'type'	=> 'bail',
				'who'	=> $user_id,
				'paid'	=> $jail_bail,
			]);
	}

	public function useGojf ($user_id) {
		return $this->setGojf($user_id, false) &&
			$this->model->user->setInJail($user_id, false) &&
			$this->model->update->pushUpdate([
				'type'	=> 'gojf',
				'id'	=> $user_id,
			]);
			
	}

	public function hasGojf ($user_id) {
		$sth = $this->model->prepare(
			'select "has_gojf" '.
			'from "c_user_game" '.
			'where "game_id" = :gid '.
			'and "user_id" = :uid'
		);

		$sth->bindParam(':uid', $user_id, PDO::PARAM_INT);
		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_NUM);
		return @$result[0];
	}

	public function setGojf ($user_id, $has_gojf) {
		$sth = $this->model->prepare(
			'update "c_user_game" '.
			'set '.
				'"has_gojf" = :hg '.
			'where "game_id" = :gid '.
			'and "user_id" = :uid'
		);

		$sth->bindParam(':uid', $user_id, PDO::PARAM_INT);
		$sth->bindParam(':hg', $has_gojf, PDO::PARAM_BOOL);
		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		return $this->model->update->pushUpdate([
			'type'	=> 'gojf',
			'id'	=> $user_id,
			'val'	=> $has_gojf,
		]);
	}

	public function joinGame ($user_id, $game_id) {
		if ($this->_isUserInGame($game_id, $user_id)) {
			return true;
		}

		if ($this->isFull($game_id)) {
			return false;
		}

		$sth = $this->model->prepare(
			'insert into "c_user_game" ("user_id", "game_id", "cash") '.
			'values (:uid, :gid, rule_or_default(:ggid,\'starting_cash\')::integer) '.
			'returning "cash"'
		);

		$sth->bindParam(':uid', $user_id, PDO::PARAM_INT);
		$sth->bindParam(':gid', $game_id, PDO::PARAM_INT);
		$sth->bindParam(':ggid', $game_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_NUM);
		$initial_cash = @$result[0];

		# XXX : tell update module about it
		return $this->model->update->pushUpdate([
			'type'	=> 'join',
			'id'	=> $user_id,
			'name'	=> $this->model->user->resolveUserId($user_id),
			'cash'	=> $initial_cash,
		]);
	}

	public function leaveGame ($user_id, $game_id) {
		$sth = $this->model->prepare(
			'delete from "c_user_game" '.
			'where "user_id" = :uid and "game_id" = :gid'
		);

		$sth->bindParam(':uid', $user_id, PDO::PARAM_INT);
		$sth->bindParam(':gid', $game_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		# TODO : tell update module about it?
		# TODO : give up all property
		return true;
	}

# member getters {{{

	private function getChance () {
		if (!isset($this->chance)) {
			$this->chance = new ModelChanceDeck($this->model, $this->game_id);
		}
		return $this->chance;
	}

	private function getCommChest () {
		if (!isset($this->commchest)) {
			$this->commchest = new ModelCommChestDeck($this->model, $this->game_id);
		}
		return $this->commchest;
	}

	private function getBoard () {
		if (!isset($this->board)) {
			$this->board = new ModelBoard($this->model, $this->game_id);
		}
		return $this->board;
	}

	private function getAuction () {
		if (!isset($this->auction)) {
			$this->auction = new ModelAuction($this->model, $this->game_id);
		}
		return $this->auction;
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
			case 'auction':
				return $this->getAuction();
				break;
				;;
		}
	}

# member getters }}}

}

# vi: fdm=marker
