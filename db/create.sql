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
  "RECORDID" serial NOT NULL,
  "TEXT" character varying(100),
  "RESULT" character varying(10),
  CONSTRAINT chance_pkey PRIMARY KEY ("RECORDID")
)
WITH (
  OIDS=FALSE
);
ALTER TABLE chance
  OWNER TO odbc;
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
  OWNER TO odbc;
COMMENT ON TABLE commchest
  IS 'Data for community chest cards';

-- Table: chat

-- DROP TABLE chat;

CREATE TABLE chat
(
  chat_id serial NOT NULL,
  room_id integer NOT NULL,
  user_id integer NOT NULL,
  chat_text character varying(1024) NOT NULL,
  chat_time timestamp without time zone NOT NULL,
  CONSTRAINT chat_pkey PRIMARY KEY (chat_id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE chat
  OWNER TO h3xx;

-- -- -- FUNCTIONS -- -- --


-- -- -- DATA -- -- --

insert  into "chance"("RECORDID","TEXT","RESULT") values (1,'Advance to Go (Collect $200)','G0'),(2,'Advance to Illinois Ave.','G17'),(3,'Advance to St. Charles Place – if you pass Go, collect $200','G11'),(4,'Bank pays you dividend of $50','50'),(5,'Go back 3 spaces','G3'),(6,'Go directly to Jail – do not pass Go, do not collect $200','G2'),(7,'Pay poor tax of $15','-15'),(8,'Take a ride on the Reading – if you pass Go collect $200','G5'),(9,'Take a walk on the Boardwalk – advance token to Boardwalk','G39'),(10,'You have been elected chairman of the board – pay each player $50','PA50'),(11,'Your building loan matures – collect $150','150');

insert  into "commchest"("RECORDID","TEXT","RESULT") values (1,'Advance to Go (Collect $200)','G0'),(2,'Bank error in your favor – collect $200','200'),(3,'Doctor''s fees {fee} – Pay $50','-50'),(4,'Go to jail – go directly to jail – Do not pass Go, do not collect $200','G2'),(5,'Grand opera Night – collect $50 from every player for opening night seats','CA50'),(6,'Tax refund – collect $20','20'),(7,'Pay Hospital Fees of $100','-100'),(8,'Pay School tax of $150','-150'),(9,'Receive for services $25','25'),(10,'You have won second prize in a beauty contest– collect $10','10'),(11,'You inherit $100','100'),(12,'From sale of stock you get $45','45'),(13,'Xmas fund matures - collect $100','100');
