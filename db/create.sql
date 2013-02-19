CREATE DATABASE onemany
  WITH
       ENCODING = 'UTF8'
       TABLESPACE = pg_default
       LC_COLLATE = 'C'
       LC_CTYPE = 'C'
       CONNECTION LIMIT = -1;

COMMENT ON DATABASE onemany
  IS 'OneMany game database';

CREATE EXTENSION pgcrypto; -- needed for cryptographic functions

-- -- -- TABLES -- -- --

-- Table: game

-- DROP TABLE game;

CREATE TABLE game
(
  game_id serial NOT NULL,
  game_state integer NOT NULL DEFAULT 0,
  game_name character varying(1024) NOT NULL,
  CONSTRAINT game_pkey PRIMARY KEY (game_id)
)
WITH (
  OIDS=FALSE
);

-- Table: user

-- DROP TABLE user;

CREATE TABLE user
(
  user_id serial NOT NULL,
  user_name character varying(128) NOT NULL,
  login_hash character(40) NOT NULL, -- Hexadecimal SHA1 hash.
  login_salt character varying(40) NOT NULL,
  CONSTRAINT user_pkey PRIMARY KEY (user_id)
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
  game_id integer NOT NULL,
  chat_id serial NOT NULL,
  user_id integer NOT NULL,
  chat_text character varying(1024) NOT NULL,
  chat_time timestamp without time zone NOT NULL DEFAULT now(),
  CONSTRAINT chat_pkey PRIMARY KEY (chat_id),
  CONSTRAINT chat_game_id_fkey FOREIGN KEY (game_id)
      REFERENCES game (game_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);

-- Table: game_update

-- DROP TABLE game_update;

CREATE TABLE game_update
(
  game_id integer NOT NULL,
  game_newstate integer NOT NULL,
  game_change character varying(4096) NOT NULL,
  CONSTRAINT game_update_pkey PRIMARY KEY (game_id, game_newstate),
  CONSTRAINT game_update_game_id_fkey FOREIGN KEY (game_id)
      REFERENCES game (game_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);
