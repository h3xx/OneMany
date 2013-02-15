-- Function: check_login(text, text)
--
-- Database engine: PostgreSQL 9.2
-- Dependencies: `pgcrypto' extension
--
-- Check a hashed login entirely on the database side.
--
-- @author: Dan Church <h3xx@gmx.com>
-- @license: GPL v3.0

-- DROP FUNCTION check_login(text, text);

CREATE OR REPLACE FUNCTION check_login(_login_name text, password_plain text)
  RETURNS boolean AS
$BODY$

declare
	hashy_hash	text;
	salty_salt	text;

begin

	select
		into hashy_hash 
		login_hash
		from	login
		where
			login_name = _login_name;

	if hashy_hash is null then
		return false;
	end if;

	select
		into salty_salt 
		login_salt
		from	login
		where
			login_name = _login_name;

	-- (must necessarily exist)
	--if salty_salt is null then
	--	return false;
	--end if;

	if encode(digest(crypt(password_plain, salty_salt), 'sha1'), 'hex') = hashy_hash then
		return true;
	end if;

	return false;
end;

$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
