-- Function: check_login(text, text)
--
-- Database engine: PostgreSQL 9.2
--
-- Create a new game.
--
-- @author: Dan Church <h3xx@gmx.com>
-- @license: GPL v3.0

-- DROP FUNCTION create_game(text);

CREATE OR REPLACE FUNCTION create_game(_game_name text)
  RETURNS integer AS
$BODY$

declare
	gid		integer;
	salty_salt	text;

begin

	insert
		into hashy_hash 
		"login_hash"
		from	"user"
		where
			"user_name" = _login_name;

	if hashy_hash is null then
		return false;
	end if;

	select
		into salty_salt 
		"login_salt"
		from	"user"
		where
			"user_name" = _login_name;

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
