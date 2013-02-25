-- Function: has_monopoly(integer, integer, integer)
--
-- Database engine: PostgreSQL 9.2
--
-- Returns whether the given user has a monopoly that contains the given space.
--
-- @author: Dan Church <h3xx@gmx.com>
-- @license: GPL v3.0

-- DROP FUNCTION has_monopoly(integer, integer, integer);

CREATE OR REPLACE FUNCTION has_monopoly(_game_id integer, _user_id integer, _space_id integer)
  RETURNS boolean AS
$BODY$

declare
	grp		character varying;
	grp_ct		integer;
	owned_ct	integer;

begin

	select
		into grp
		"space_group"
		from "space"
		where "space_id" = _space_id;

	select
		into grp_ct
		count(*) from "space"
		where "space_group" = grp;

	select
		into owned_ct
		count(*)
		from "c_game_space"
			left join "space" on ("space"."space_id" = "c_game_space"."space_id")
		where
			"game_id" = _game_id and
			"owner_id" = _user_id and
			"space_group" = grp;

	if grp_ct = owned_ct then
		return true;
	end if;

	return false;
end;

$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
