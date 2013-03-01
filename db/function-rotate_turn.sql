-- Function: rotate_turn(integer)
--
-- Database engine: PostgreSQL 9.2
--
-- Rotate the turn to the next player.
--
-- @author: Dan Church <h3xx@gmx.com>
-- @license: GPL v3.0

-- DROP FUNCTION rotate_turn(integer);

CREATE OR REPLACE FUNCTION rotate_turn(_game_id integer)
  RETURNS integer AS
$BODY$

declare
	new_turn	integer;

begin

	-- increment the turn
	update "c_user_game"
		set "sequence" = (
			select max("sequence") + 1
			from "c_user_game"
			where "game_id" = _game_id
		)
		where "user_id" = (
			select "user_id"
			from "c_user_game"
			where "game_id" = 4
			order by "sequence"
			limit 1
	);

	-- figure out whose turn it is now 
	select
		into new_turn
		"user_id"
		from	"c_user_game"
		where
			"game_id" = _game_id
		order by "sequence"
		limit 1;

	return new_turn;
end;

$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
