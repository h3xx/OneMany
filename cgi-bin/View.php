<?php

require_once('Tools.php');
require_once('View/Chat.php');
require_once('View/List.php');
require_once('View/Board.php');
require_once('View/Game.php');
require_once('View/PropertyCard.php');
require_once('View/Auction.php');

class View {
	private $model, $user_id;

	function __construct ($model, $user_id) {
		$this->model = $model;
		$this->user_id = $user_id;
	}

	public function processInstruction ($instr) {
		# the user must be logged in, or logging in, or requesting public info
		if (!isset($this->user_id) && !preg_match('/^(listGames)$/', @$instr['func'])) {
			$jsonresponse = [
				'result'=> false,
				'msg'	=> 'You are not logged in.',
				'redirect'=> Tools::loginUrl(),
			];
		} else {
			switch (@$instr['func']) {
				case 'myUserInfo':
					$jsonresponse = $this->model->user->getUserInfo($this->user_id);
					if (!$jsonresponse) {
						$jsonresponse = [
							'result'=> false,
							'msg'	=> 'Is your account verified?',
						];
					}
					break;
					;;
				case 'pollChat':
					$jsonresponse = $this->chat->getChatUpdate(@$instr['args']);
					break;
					;;
				case 'pollGame':
					$jsonresponse = $this->game->getBoardUpdateInstructions(@$instr['args']);
					break;
					;;
				case 'propcardInfo':
					$jsonresponse = $this->propcard->getPropertyCardData(@$instr['args']);
					break;
					;;
				case 'init':
					$jsonresponse = $this->game->getInitialGameData();
					break;
					;;
				case 'listGames':
					$jsonresponse = $this->list->listGames();
					break;
					;;
				case 'auction':
					$jsonresponse = $this->auction->getAuctionInfo(@$instr['args']);
					break;
					;;
				case 'playerCount':
					$jsonresponse = [
						'players'	=> $this->model->game->numPlayers(),
						'min'		=> $this->model->rules->getRuleValue('min_players', 2),
						'max'		=> $this->model->rules->getRuleValue('max_players', 5),
					];
					break;
					;;
				default:
					$jsonresponse = [
						'result'=> false,
						'msg'	=> 'Invalid function.',
					];
					break;
					;;
			}
		}

		return Tools::encodeJson($jsonresponse);
	}

# member getters {{{

	private function getChat () {
		if (!isset($this->chat)) {
			$this->chat = new ViewChat($this->model, $this->user_id);
		}
		return $this->chat;
	}

	private function getGame () {
		if (!isset($this->game)) {
			$this->game = new ViewGame($this->model, $this->user_id);
		}
		return $this->game;
	}

	private function getPropCard () {
		if (!isset($this->propcard)) {
			$this->propcard = new ViewPropertyCard($this->model);
		}
		return $this->propcard;
	}

	private function getList () {
		if (!isset($this->list)) {
			$this->list = new ViewList($this->model, $this->user_id);
		}
		return $this->list;
	}

	private function getAuction () {
		if (!isset($this->auction)) {
			$this->auction = new ViewAuction($this->model, $this->user_id);
		}
		return $this->auction;
	}

	function __get ($name) {
		switch ($name) {
			case 'chat':
				return $this->getChat();
				break;
				;;
			case 'game':
				return $this->getGame();
				break;
				;;
			case 'propcard':
				return $this->getPropCard();
				break;
				;;
			case 'list':
				return $this->getList();
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
