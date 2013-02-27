-- Function: take_user_reset(integer, character varying, text)
--
-- Database engine: PostgreSQL 9.2
-- Dependencies: `pgcrypto' extension
--
-- Reset a password.
--
-- @author: Dan Church <h3xx@gmx.com>
-- @license: GPL v3.0

-- DROP FUNCTION take_user_reset(integer, character varying, text);

CREATE OR REPLACE FUNCTION take_user_reset(_user_id integer, _reset_string character varying, password_plain text)
  RETURNS boolean AS
$BODY$

declare
	hashy_hash	text;
	salty_salt	text;

begin

	select
		count(*)
		from "user"
		where
		"user_id" = _user_id and
		"reset_string" = _reset_string and
		"reset_expire" > now();

	if not found then
		-- no such user - can't continue
		return false;
	end if;

	-- blowfish salt = 128 bits = 16 characters
	select into salty_salt gen_salt('bf');
	select into hashy_hash encode(digest(crypt(password_plain, salty_salt), 'sha1'), 'hex');

	update
		"user"
		set
		"login_hash" = hashy_hash,
		"login_salt" = salty_salt,
		"reset_string" = null,
		"reset_expire" = null

		where
		"user_id" = _user_id;

	return true;
end;

$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
