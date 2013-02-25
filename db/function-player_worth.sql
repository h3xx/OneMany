-- Function: player_worth(integer, integer)
--
-- Database engine: PostgreSQL 9.2
--
-- Calculate a player's worth.
--
-- @author: Dan Church <h3xx@gmx.com>
-- @license: GPL v3.0

-- DROP FUNCTION player_worth(integer,integer);

CREATE OR REPLACE FUNCTION player_worth(_game_id integer,_user_id integer)
  RETURNS integer AS
$BODY$

declare
	uworth		integer;

begin

	select
		into uworth
		sum("cash") from (
			select "cash" from "c_user_game" where "user_id" = 2 and "game_id" = 4
			union
			select
				"cost" + ("houses" * "housecost") as "cash"
				from "c_game_space"
				left join "space" on ("space"."space_id" = "c_game_space"."space_id")
				where "game_id" = 4 and "owner_id" = 2
		) as "foo";

	return uworth;
end;

$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
