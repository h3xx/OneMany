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

-- Table: chance

-- DROP TABLE chance;

CREATE TABLE chance
(
  "TEXT" character varying(100),
  "RESULT" character varying(10),
  "RECORDID" serial NOT NULL,
  CONSTRAINT chance_pkey PRIMARY KEY ("RECORDID")
)
WITH (
  OIDS=FALSE
);
ALTER TABLE chance
  OWNER TO odbc_group;
COMMENT ON TABLE chance
  IS 'Data for chance cards';

-- Table: commchest

-- DROP TABLE commchest;

CREATE TABLE commchest
(
  "RECORDID" serial NOT NULL,
  "TEXT" character varying(100),
  "RESULT" character varying(10),
  CONSTRAINT commchest_pkey PRIMARY KEY ("RECORDID")
)
WITH (
  OIDS=FALSE
);
ALTER TABLE commchest
  OWNER TO odbc_group;
COMMENT ON TABLE commchest
  IS 'Data for community chest cards';

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

-- -- -- DATA -- -- --

insert  into "chance"("RECORDID","TEXT","RESULT") values (1,'Advance to Go (Collect $200)','G0'),(2,'Advance to Illinois Ave.','G17'),(3,'Advance to St. Charles Place – if you pass Go, collect $200','G11'),(4,'Bank pays you dividend of $50','50'),(5,'Go back 3 spaces','G3'),(6,'Go directly to Jail – do not pass Go, do not collect $200','G2'),(7,'Pay poor tax of $15','-15'),(8,'Take a ride on the Reading – if you pass Go collect $200','G5'),(9,'Take a walk on the Boardwalk – advance token to Boardwalk','G39'),(10,'You have been elected chairman of the board – pay each player $50','PA50'),(11,'Your building loan matures – collect $150','150');

insert  into "commchest"("RECORDID","TEXT","RESULT") values (1,'Advance to Go (Collect $200)','G0'),(2,'Bank error in your favor – collect $200','200'),(3,'Doctor''s fees {fee} – Pay $50','-50'),(4,'Go to jail – go directly to jail – Do not pass Go, do not collect $200','G2'),(5,'Grand opera Night – collect $50 from every player for opening night seats','CA50'),(6,'Tax refund – collect $20','20'),(7,'Pay Hospital Fees of $100','-100'),(8,'Pay School tax of $150','-150'),(9,'Receive for services $25','25'),(10,'You have won second prize in a beauty contest– collect $10','10'),(11,'You inherit $100','100'),(12,'From sale of stock you get $45','45'),(13,'Xmas fund matures - collect $100','100');
