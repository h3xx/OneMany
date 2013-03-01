-- Function: set_turn(integer, integer)
--
-- Database engine: PostgreSQL 9.2
--
-- Sets whose turn it is.
--
-- @author: Dan Church <h3xx@gmx.com>
-- @license: GPL v3.0

-- DROP FUNCTION set_turn(integer, integer);

CREATE OR REPLACE FUNCTION set_turn(_game_id integer, _user_id integer)
  RETURNS boolean AS
$BODY$

begin

	-- increment the turn
	update "c_user_game"
		set "sequence" = (
			select min("sequence") - 1
			from "c_user_game"
			where "game_id" = _game_id and "user_id" != _user_id
		)
		where "user_id" = _user_id;

	return true;
end;

$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
