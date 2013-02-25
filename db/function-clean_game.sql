-- Function: clean_game(integer)
--
-- Database engine: PostgreSQL 9.2
--
-- Clean up the database after a game.
--
-- @author: Dan Church <h3xx@gmx.com>
-- @license: GPL v3.0

-- DROP FUNCTION clean_game(integer);

CREATE OR REPLACE FUNCTION clean_game(_game_id integer)
  RETURNS boolean AS
$BODY$

begin

	delete from "chat" where "game_id" = _game_id;
	delete from "c_user_game" where "game_id" = _game_id;
	delete from "c_game_chance" where "game_id" = _game_id;
	delete from "c_game_commchest" where "game_id" = _game_id;
	delete from "c_game_rules" where "game_id" = _game_id;
	delete from "c_game_space" where "game_id" = _game_id;
	delete from "game_update" where "game_id" = _game_id;

	return true;
end;

$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
