<?php

class ModelAuction {
	private $model, $game_id;

	function __construct ($model, $game_id) {
		$this->model = $model;
		$this->game_id = $game_id;
	}

	public function exportModel () {
		return $this->getAuctionInfoNoExpired();
	}

	public function startAuction ($space_id, $auctioning_user, $opening_bid) {
		$sth = $this->model->prepare(
			'update "game" '.
			'set '.
				'"auction_space" = :sid, '.
				'"auction_user" = null, '.
				'"auction_bid" = :bd, '.
				'"auction_reportedclosed" = false, '.
				'"auction_expire" = now() + rule_or_default(:ggid,\'auction_timeout\')::interval '.
			'where "game_id" = :gid'
		);

		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);
		$sth->bindParam(':ggid', $this->game_id, PDO::PARAM_INT);
		$sth->bindParam(':sid', $space_id, PDO::PARAM_INT);

		$sth->bindParam(':bd', $opening_bid, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		return $this->model->update->pushUpdate([
			'type'	=> 'auctionStart',
			'who'	=> $auctioning_user,
			'space'	=> $space_id,
			'bid'	=> $opening_bid,
		]);
	}

	public function closeAuction () {
		# note: does not do any checking for expiration
		$sth = $this->model->prepare(
			# database function - returns boolean
			'select close_auction(:gid)'
		);

		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_NUM);

		if (!@$result[0]) {
			# no need to report update
			return true;
		}

		$ainfo = $this->getAuctionInfo();

		if (isset($ainfo['auid']) && isset($ainfo['abid'])) {
			# make the user pay
			if (!$this->model->user->addUserCash($ainfo['auid'], -$ainfo['abid'])) {
				return false;
			}
		}

		$this->clearAuctionInfo();

		return $this->model->update->pushUpdate([
			'type'	=> 'auctionClose',
			'winner'=> $ainfo['auid'],
			'wname'	=> $ainfo['auser'],
			'winbid'=> $ainfo['abid'],
			'space'	=> $ainfo['aspace'],
			'sname'	=> $ainfo['aname'],
		]);
	}

	public function isAuctionClosed () {
		$sth = $this->model->prepare(
			'select count(*) '.
			'from "game" '.
			'where "game_id" = :gid '.
			'and "auction_expire" > now()'
		);

		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_NUM);
		return @$result[0];
	}

	public function setAuctionBid ($user_id, $bid) {
		$sth = $this->model->prepare(
			'update "game" '.
			'set '.
				'"auction_user" = :uid, '.
				'"auction_bid" = :bd '.
			'where "game_id" = :gid'
		);

		$sth->bindParam(':uid', $user_id, PDO::PARAM_INT);
		$sth->bindParam(':bd', $bid, PDO::PARAM_INT);
		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		return $this->model->update->pushUpdate([
			'type'	=> 'bid',
			'who'	=> $user_id,
			'bid'	=> $bid,
		]);
	}

	public function getAuctionTimeleft () {
		$sth = $this->model->prepare(
			'select '.
			'date_part(\'epoch\', "auction_expire" - now()) as "aseconds" '.
			'from "game" '.
			'where "game_id" = :gid'
		);

		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		return $sth->fetch(PDO::FETCH_ASSOC);

	}

	public function getAuctionInfo () {
		$sth = $this->model->prepare(
			'select '.
			'"auction_user" as "auid", '.
			'"user"."user_name" as "auser", '.
			'"auction_space" as "aspace", '.
			'"space"."space_name" as "aname", '.
			'"auction_bid" as "abid", '.
			#'"auction_expire" as "aexpire", '.
			'date_part(\'epoch\', "auction_expire" - now()) as "aseconds" '.
			'from "game" '.
			'left join "space" on ("game"."auction_space" = "space"."space_id") '.
			'left join "user" on ("game"."auction_user" = "user"."user_id") '.
			'where "game_id" = :gid'
		);

		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_ASSOC);

		return $result;

	}

	public function getAuctionInfoNoExpired () {
		$sth = $this->model->prepare(
			'select '.
			'"auction_user" as "auid", '.
			'"user"."user_name" as "auser", '.
			'"auction_space" as "aspace", '.
			'"space"."space_name" as "aname", '.
			'"auction_bid" as "abid", '.
			#'"auction_expire" as "aexpire", '.
			'date_part(\'epoch\', "auction_expire" - now()) as "aseconds" '.
			'from "game" '.
			'left join "space" on ("game"."auction_space" = "space"."space_id") '.
			'left join "user" on ("game"."auction_user" = "user"."user_id") '.
			'where "game_id" = :gid and '.
			# make sure it hasn't expired
			'"auction_expire" > now()'

		);

		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_ASSOC);

		return ($result ? $result : []); # empty array if no auction

	}

	private function clearAuctionInfo () {
		$sth = $this->model->prepare(
			'update "game" set '.
			'"auction_user" = null, '.
			'"auction_space" = null, '.
			'"auction_bid" = null, '.
			'"auction_expire" = null, '.
			'where "game_id" = :gid'
		);

		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);

		return $sth->execute();
	}

}
