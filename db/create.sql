CREATE DATABASE onemany
  WITH
       ENCODING = 'UTF8'
       TABLESPACE = pg_default
       LC_COLLATE = 'English_United States.1252'
       LC_CTYPE = 'English_United States.1252'
       CONNECTION LIMIT = -1;

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
