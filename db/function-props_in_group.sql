-- Function: props_in_group(character varying)
--
-- Database engine: PostgreSQL 9.2
--
-- Returns the number of properties in the given group.
--
-- @author: Dan Church <h3xx@gmx.com>
-- @license: GPL v3.0

-- DROP FUNCTION props_in_group(character varying);

CREATE OR REPLACE FUNCTION props_in_group(_space_group character varying)
  RETURNS integer AS
$BODY$

declare
	grp_ct		integer;

begin

	select
		into grp_ct 
		count(*)
		from	"space"
		where
			"space_group" = _space_group;

	return grp_ct;
end;

$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
