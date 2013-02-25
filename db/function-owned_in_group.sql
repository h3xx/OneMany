-- Function: owned_in_group(integer, integer, character varying)
--
-- Database engine: PostgreSQL 9.2
--
-- Figure out how many properties a player owns in a related group.
--
-- @author: Dan Church <h3xx@gmx.com>
-- @license: GPL v3.0

-- DROP FUNCTION owned_in_group(integer,integer,character varying);

CREATE OR REPLACE FUNCTION owned_in_group(_game_id integer,_owner_id integer,_space_group character varying)
  RETURNS integer AS
$BODY$

declare
	_owned		integer;

begin

	select
		into _owned
		count(*)
		from "c_game_space"
			left join "space" on ("space"."space_id" = "c_game_space"."space_id")
		where "game_id" = _game_id and "owner_id" = _owner_id and "space_group" = _space_group;

	return _owned;
end;

$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
