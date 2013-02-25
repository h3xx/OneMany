-- Function: populate_game(integer)
--
-- Database engine: PostgreSQL 9.2
--
-- Create a new game.
--
-- @author: Dan Church <h3xx@gmx.com>
-- @license: GPL v3.0

-- DROP FUNCTION populate_game(integer);

CREATE OR REPLACE FUNCTION populate_game(_game_id integer)
  RETURNS boolean AS
$BODY$

begin

	delete from "c_game_chance" where "game_id" = _game_id;

	create temporary sequence chseq;
	insert
		into "c_game_chance" ("game_id", "chance_recordid", "sequence")
		select
			_game_id as "game_id",
			"randomdeck"."RECORDID" as "chance_recordid",
			nextval('chseq') as "sequence" from (
				select "RECORDID" from "chance" order by random()
			) as "randomdeck";
	drop sequence if exists chseq;

	delete from "c_game_commchest" where "game_id" = _game_id;

	create temporary sequence ccseq;
	insert
		into "c_game_commchest" ("game_id", "commchest_recordid", "sequence")
		select
			_game_id as "game_id",
			"randomdeck"."RECORDID" as "commchest_recordid",
			nextval('ccseq') as "sequence" from (
				select "RECORDID" from "commchest" order by random()
			) as "randomdeck";
	drop sequence if exists ccseq;

	delete from "c_game_space" where "game_id" = _game_id;
	insert
		into "c_game_space" ("game_id", "space_id")
		select
			_game_id as "game_id",
			"space"."space_id"
			from "space";

	-- set up default rules
	delete from "c_game_rules" where "game_id" = _game_id;
	insert
		into "c_game_rules" ("game_id", "rule_name", "rule_value")
		select
			_game_id as "game_id",
			"rules"."rule_name",
			"rules"."rule_default" as "rule_value"
			from "rules";

	return true;
end;

$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
