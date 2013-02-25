<?php

class ModelBoard {
	private $model, $game_name, $game_id;

	function __construct ($model, $game_id) {
		$this->model = $model;
		$this->game_id = $game_id;
	}

	# used for constructing property cards in the view
	public function getSpaceInfo ($space_id) {
		$sth = $this->model->prepare(
			'select '.
				'"space_group" as "group",'.
				'"space_name" as "name",'.
				'"cost",'.
				'"rent",'.
				'"rent1",'.
				'"rent2",'.
				'"rent3",'.
				'"rent4",'.
				'"rent5",'.
				'"housecost",'.
				'"mortgage"'.
			' from "space" where "space_id" = :sid'
		);

		$sth->bindParam(':sid', $space_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_ASSOC);

		return $result;
	}

	public function getBuyFromBankCost ($space_id) {
		$sth = $this->model->prepare(
			'select "cost" '.
			'from "space" where "space_id" = :sid'
		);

		$sth->bindParam(':sid', $space_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_NUM);

		return @$result[0];
	}

	public function whoOwnsSpace ($space_id) {
		$sth = $this->model->prepare(
			/*
			'select "user_name" from "c_game_space" '.
			'left join "user" on '.
			'("user"."user_id" = "c_game_space"."owner_id") '.
			'where "game_id" = :gid and "space_id" = :sid'
			*/
			'select "owner_id" from "c_game_space" '.
			'where "game_id" = :gid and "space_id" = :sid'
		);

		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);
		$sth->bindParam(':sid', $space_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_NUM);

		return @$result[0];
	}

	public function rentForSpace ($space_id) {
		# FIXME : replace with database function?
		$sth = $this->model->prepare(
			'select '.
				'"is_mortgaged", '.
				'"houses", '.
				'"space_group" as "group", '.
				'"foo"."owned_in_group", '.
				'"rent", '.
				'"rent1", '.
				'"rent2", '.
				'"rent3", '.
				'"rent4", '.
				'"rent5" '.
	'from "c_game_space" '.
	'left join "space" on ("c_game_space"."space_id" = "space"."space_id") '.
	'left join ( '.
		'select "space_group", "owner_id", count(*) as "owned_in_group" from "c_game_space" '.
			'left join "space" on ("c_game_space"."space_id" = "space"."space_id") '.
			'where "game_id" = :gid '.
			'group by "space_group" '.
	') as "foo" '.
	'on ("foo"."space_group" = "space"."space_group" '.
		'and "foo"."owner_id" = "c_game_space"."owner_id") '.
	'where "c_game_space"."space_id" = :sid'
		);

		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);
		$sth->bindParam(':sid', $space_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_ASSOC);

		if ($result['is_mortgaged']) {
			# no rent if mortgaged
			return 0;
		}

		if (is_numeric($result['group'])) {
			# regular, buyable property
			# (XXX : assuming it's owned)
			switch ($result['houses']) {
				case 0:
					return $result['rent'];
					break;
					;;
				case 1:
				case 2:
				case 3:
				case 4:
				case 5:
					return $result['rent' . $result['houses']];
					break;
					;;
			}
		}
		# FIXME
	}

	public function hasMonopoly ($user_id, $space_id) {
		$sth = $this->model->prepare(
			'select has_monopoly(:gid, :uid, :sid)'
		);

		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);
		$sth->bindParam(':uid', $user_id, PDO::PARAM_INT);
		$sth->bindParam(':sid', $space_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_NUM);

		return @$result[0];
	}

	public function housesOnSpace ($space_id) {
		$sth = $this->model->prepare(
			'select "houses" from "c_game_space" '.
			'where "game_id" = :gid and "space_id" = :sid'
		);

		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);
		$sth->bindParam(':sid', $space_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_NUM);

		return @$result[0];
	}

	public function isPropertyMortgaged ($space_id) {
		$sth = $this->model->prepare(
			'select "is_mortgaged" from "c_game_space" '.
			'where "game_id" = :gid and "space_id" = :sid'
		);

		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);
		$sth->bindParam(':sid', $space_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_NUM);

		return @$result[0];
	}

	public function rentForHouses ($space_id, $num_houses) {
		switch ($num_houses) {
			case 0:
				$column = 'rent';
				break;
			case 1:
			case 2:
			case 3:
			case 4:
			case 5:
				$column = 'rent' . $num_houses;
				break;
			default:
				return null;
		}

		$sth = $this->model->prepare(
			'select "'.$column.'" from "space" '.
			'where "space_id" = :sid'
		);

		$sth->bindParam(':sid', $space_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_NUM);

		return @$result[0];
	}

	public function ownedInGroup ($user_id, $group) {
		$sth = $this->model->prepare(
			'select '.
				'count(*) '.
			'from "c_game_space" '.
				'left join "space" on ("c_game_space"."space_id" = "space"."space_id") '.
			'where "game_id" = :gid '.
				'and "owner_id" = :uid '.
				'and "space_group" = :gr'
			# (group by clause is unnecessary)
		);

		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);
		$sth->bindParam(':uid', $user_id, PDO::PARAM_INT);
		$sth->bindParam(':gr', $group, PDO::PARAM_STR);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_NUM);

		return @$result[0];
	}
	
	public function totalInGroup ($group) {
		$sth = $this->model->prepare(
			'select '.
				'count(*) '.
			'from "space" '.
			'where "space_group" = :gr'
			# (group by clause is unnecessary)
		);

		$sth->bindParam(':gr', $group, PDO::PARAM_STR);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_NUM);

		return @$result[0];
	}

	private function getRentInfo () {
		$sth = $this->model->prepare(
			'select '.
				'"is_mortgaged", '.
				'"houses", '.
				'"space_group" as "group", '.
				'"foo"."owned_in_group", '.
				'"rent", '.
				'"rent1", '.
				'"rent2", '.
				'"rent3", '.
				'"rent4", '.
				'"rent5" '.
	'from "c_game_space" '.
	'left join "space" on ("c_game_space"."space_id" = "space"."space_id") '.
	'left join ( '.
		'select "space_group", "owner_id", count(*) as "owned_in_group" from "c_game_space" '.
			'left join "space" on ("c_game_space"."space_id" = "space"."space_id") '.
			'where "game_id" = :gid '.
			'group by "space_group" '.
	') as "foo" '.
	'on ("foo"."space_group" = "space"."space_group" '.
		'and "foo"."owner_id" = "c_game_space"."owner_id")'
		);

		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_ASSOC);

		return $result;
	}

	public function setPropertyMortgaged ($space_id, $is_mortgaged) {
		$sth = $this->model->prepare(
			'update "c_game_space" '.
			'set "is_mortgaged" = :mrt '
			'where "space_id" = :sid and '.
			'"game_id" = :gid'
		);

		$sth->bindParam(':mrt', $is_mortgaged, PDO::PARAM_BOOL);
		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);
		$sth->bindParam(':sid', $space_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		# XXX : tell update module about it
		return $this->model->update->pushUpdate([
			'type'	=> 'mortgage',
			'space'	=> $space_id,
		]);
	}

	public function setPropertyOwner ($space_id, $owner_id) {
		$sth = $this->model->prepare(
			'update "c_game_space" '.
			'set "owner_id" = :oid '.
			'where "space_id" = :sid and '.
			'"game_id" = :gid'
		);

		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);
		$sth->bindParam(':oid', $owner_id, PDO::PARAM_INT);
		$sth->bindParam(':sid', $space_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		# XXX : tell update module about it
		return $this->model->update->pushUpdate([
			'type'	=> 'buy',
			'space'	=> $space_id,
			'owner'	=> $owner_id,
		]);
	}
}
