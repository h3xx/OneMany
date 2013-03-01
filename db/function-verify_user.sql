-- Function: verify_user(text)
--
-- Database engine: PostgreSQL 9.2
--
-- Set a user as verified.
--
-- @author: Dan Church <h3xx@gmx.com>
-- @license: GPL v3.0

-- DROP FUNCTION verify_user(text);

CREATE OR REPLACE FUNCTION verify_user(_login_name text)
  RETURNS boolean AS
$BODY$

begin

	update
		"user"
		set "verified" = true
		where
			"user_name" = _login_name;

	if not found then
		return false;
	end if;

	return false;
end;

$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;

