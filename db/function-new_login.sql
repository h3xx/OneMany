-- Function: new_login(text, text, text)
--
-- Database engine: PostgreSQL 9.2
-- Dependencies: `pgcrypto' extension
--
-- Insert a new hashed login.
--
-- @author: Dan Church <h3xx@gmx.com>
-- @license: GPL v3.0

-- DROP FUNCTION new_login(text, text, text);

CREATE OR REPLACE FUNCTION new_login(_login_name text, _email text, password_plain text)
  RETURNS character varying AS
$BODY$

declare
	logn		text;
	hashy_hash	text;
	salty_salt	text;
	_vfy_str	character varying;

begin
	perform
		"user_id"
		from	"user"
		where
			"user_name" = _login_name;

	if found then
		-- already exists; can't create
		return null;
	end if;

	-- blowfish salt = 128 bits = 16 characters
	select into salty_salt gen_salt('bf');
	select into hashy_hash encode(digest(crypt(password_plain, salty_salt), 'sha1'), 'hex');

	insert
		into "user"
		into _vfy_str
		("user_name", "user_email", "login_hash", "login_salt")
		values(_login_name, _email, hashy_hash, salty_salt)
		returning "verify_string";

	return _vfy_str;
end;

$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;

