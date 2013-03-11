-- Function: set_rule(integer, character varying, character varying);
--
-- Database engine: PostgreSQL 9.2
--
-- Set a rule value for a game.
--
-- @author: Dan Church <h3xx@gmx.com>
-- @license: GPL v3.0

-- DROP FUNCTION set_rule(integer, character varying, character varying);

CREATE OR REPLACE FUNCTION set_rule(_game_id integer, _rule_name character varying, _rule_value character varying)
  RETURNS boolean AS
$BODY$

begin

	update "c_game_rules"
		set "rule_value" = _rule_value
		where
			"game_id" = _game_id and
			"rule_name" = _rule_name;

	if not found then
		insert into "c_game_rules"
			("game_id", "rule_name", "rule_value")
			values
			(_game_id, _rule_name, _rule_value);
	end if;

	return true;
end;

$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
