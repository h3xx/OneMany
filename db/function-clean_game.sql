-- Function: populate_game(text, text)
--
-- Database engine: PostgreSQL 9.2
--
-- Clean up the database after a game.
--
-- @author: Dan Church <h3xx@gmx.com>
-- @license: GPL v3.0

-- DROP FUNCTION clean_game(text);

CREATE OR REPLACE FUNCTION clean_game(_game_id integer)
  RETURNS boolean AS
$BODY$

begin

	delete from "c_game_chance" where "game_id" = _game_id;
	delete from "c_game_commchest" where "game_id" = _game_id;
	delete from "c_game_space" where "game_id" = _game_id;
	delete from "c_game_rules" where "game_id" = _game_id;

	return true;
end;

$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
