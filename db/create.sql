CREATE DATABASE onemany
  WITH OWNER = odbc
       ENCODING = 'UTF8'
       TABLESPACE = pg_default
       LC_COLLATE = 'English_United States.1252'
       LC_CTYPE = 'English_United States.1252'
       CONNECTION LIMIT = -1;
GRANT ALL ON DATABASE onemany TO odbc;

COMMENT ON DATABASE onemany
  IS 'OneMany game database';

CREATE EXTENSION pgcrypto; -- needed for cryptographic functions

-- -- -- TABLES -- -- --

-- Table: login

-- DROP TABLE login;

CREATE TABLE login
(
  login_name character varying(128) NOT NULL,
  login_hash character(40) NOT NULL, -- Hexadecimal SHA1 hash.
  login_salt character varying(40) NOT NULL,
  CONSTRAINT login_pkey PRIMARY KEY (login_name)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE login
  OWNER TO odbc;
GRANT ALL ON TABLE login TO odbc;
COMMENT ON TABLE login
  IS 'Login/password table';
COMMENT ON COLUMN login.login_hash IS 'Hexadecimal SHA1 hash.';


-- -- -- FUNCTIONS -- -- --

-- Function: new_login(text, text)

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
		login_name
		from	login
		where
			login_name = _login_name;
	if logn is not null then
		-- already exists; can't create
		return false;
	end if;

	-- blowfish salt = 128 bits = 16 characters
	select into salty_salt gen_salt('bf');
	select into hashy_hash encode(digest(crypt(password_plain, salty_salt), 'sha1'), 'hex');

	insert into login(login_name, login_hash, login_salt)
		values(_login_name, hashy_hash, salty_salt);

	return true;
end;

$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION new_login(text, text)
  OWNER TO odbc;

-- Function: check_login(text, text)

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
ALTER FUNCTION check_login(text, text)
  OWNER TO odbc;
