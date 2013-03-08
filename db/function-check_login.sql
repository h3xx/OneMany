-- Function: check_login(text, text)
--
-- Database engine: PostgreSQL 9.2
-- Dependencies: `pgcrypto' extension
--
-- Check a hashed login entirely on the database side.
--
-- Returns the integer user_id, or null if the login is invalid.
--
-- @author: Dan Church <h3xx@gmx.com>
-- @license: GPL v3.0

-- DROP FUNCTION check_login(text, text);

CREATE OR REPLACE FUNCTION check_login(_login_name text, password_plain text)
  RETURNS integer AS
$BODY$

declare
	_user_id	integer;
	_hash	text;
	_salt	text;

begin

	select
		into _user_id, _hash, _salt
		"user_id", "login_hash", "login_salt"
		from	"user"
		where
			"user_name" = _login_name and
			"verified";

	if not found then
		return null;
	end if;

	if encode(digest(crypt(password_plain, _salt), 'sha1'), 'hex') = _hash then
		return _user_id;
	end if;

	return null;
end;

$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
