-- Function: new_login(text, text)
--
-- Database engine: PostgreSQL 9.2
-- Dependencies: `pgcrypto' extension
--
-- Insert a new hashed login.
--
-- @author: Dan Church <h3xx@gmx.com>
-- @license: GPL v3.0

-- DROP FUNCTION new_login(text, text);

CREATE OR REPLACE FUNCTION new_login(_login_name text, password_plain text)
  RETURNS boolean AS
$BODY$

declare
	logn		text;
	hashy_hash	text;
	salty_salt	text;

begin
	select
		into logn 
		"user_name"
		from	"user"
		where
			"user_name" = _login_name;

	if logn is not null then
		-- already exists; can't create
		return false;
	end if;

	-- blowfish salt = 128 bits = 16 characters
	select into salty_salt gen_salt('bf');
	select into hashy_hash encode(digest(crypt(password_plain, salty_salt), 'sha1'), 'hex');

	insert into "user"("user_name", "login_hash", "login_salt")
		values(_login_name, hashy_hash, salty_salt);

	return true;
end;

$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;

