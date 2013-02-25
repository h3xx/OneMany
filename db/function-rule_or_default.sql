-- Function: rule_or_default(integer, character varying)
--
-- Database engine: PostgreSQL 9.2
--
-- Figures out either the set rule for a game or the default.
--
-- @author: Dan Church <h3xx@gmx.com>
-- @license: GPL v3.0

-- DROP FUNCTION rule_or_default(integer, character varying);

CREATE OR REPLACE FUNCTION rule_or_default(_game_id integer, _rule_name character varying)
  RETURNS character varying AS
$BODY$

declare
	_rule_val	character varying;

begin
	select
		into _rule_val
		"rule_value"
		from "c_game_rules"
		where "game_id" = _game_id and "rule_name" = _rule_name;

	if not FOUND then
		select
			into _rule_val
			"rule_default"
			from "rules"
			where "rule_name" = _rule_name;

	end if;

	return _rule_val;

end;

$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
