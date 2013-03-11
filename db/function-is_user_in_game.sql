-- Function: is_user_in_game(integer, integer);
--
-- Database engine: PostgreSQL 9.2
--
-- Test whether a user is in a given game.
--
-- @author: Dan Church <h3xx@gmx.com>
-- @license: GPL v3.0

-- DROP FUNCTION is_user_in_game(integer, integer);

CREATE OR REPLACE FUNCTION is_user_in_game(_game_id integer, _user_id integer)
  RETURNS boolean AS
$BODY$

begin

	perform
		count(*)
		from "c_user_game"
		where "game_id" = _game_id and "user_id" = _user_id;

	if not found then
		return false;
	end if;

	return true;
end;

$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
