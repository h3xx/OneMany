-- Function: user_leave_game(integer, integer)
--
-- Database engine: PostgreSQL 9.2
--
-- Remove a user from a game, disowning all their properties and such.
--
-- @author: Dan Church <h3xx@gmx.com>
-- @license: GPL v3.0

-- DROP FUNCTION user_leave_game(integer, integer);

CREATE OR REPLACE FUNCTION user_leave_game(_game_id integer, _user_id integer)
  RETURNS boolean AS
$BODY$


begin

	-- disown all property
	update "c_game_space"
		set "owner_id" = null,
		    "houses" = 0,
		    "is_mortgaged" = false
		where "game_id" = _game_id and "user_id" = _user_id;

	-- disown any "get out of jail free" cards
	update "c_game_chance"
		set "drawn_by" = null
		where "game_id" = _game_id and "user_id" = _user_id;
	update "c_game_commchest"
		set "drawn_by" = null
		where "game_id" = _game_id and "user_id" = _user_id;

	
end;

$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
