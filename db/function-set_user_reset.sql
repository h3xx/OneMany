-- Function: set_user_reset(integer)
--
-- Database engine: PostgreSQL 9.2
-- Dependencies: `pgcrypto' extension
--
-- Put a password reset request into the database.
--
-- Returns the reset string.
--
-- @author: Dan Church <h3xx@gmx.com>
-- @license: GPL v3.0

-- DROP FUNCTION set_user_reset(integer);

CREATE OR REPLACE FUNCTION set_user_reset(_user_id integer)
  RETURNS character varying AS
$BODY$

declare
	rst	character varying;

begin

	update
		into rst
		"user" set

		"reset_expire" = now() + interval '7 days',
		"reset_string" = encode(gen_random_bytes(40), 'base64')

		where "user_id" = _user_id
		returning "reset_string";

	return rst;
end;

$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
