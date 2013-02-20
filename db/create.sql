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

-- Table: space

-- DROP TABLE space;

CREATE TABLE space
(
  space_id serial NOT NULL,
  space_name character varying(255) NOT NULL,
  space_group character varying(255) NOT NULL, -- Group or action to be performed.
  cost integer NOT NULL, -- Cost to buy from bank.
  rent integer NOT NULL, -- Rent with 0 houses.
  rent1 integer NOT NULL, -- Rent with 1 house.
  rent2 integer NOT NULL, -- Rent with 2 houses.
  rent3 integer NOT NULL, -- Rent with 3 houses.
  rent4 integer NOT NULL, -- Rent with 4 houses.
  rent5 integer NOT NULL, -- Rent with a hotel.
  mortgage integer NOT NULL, -- Mortgage value.
  CONSTRAINT space_pkey PRIMARY KEY (space_id)
)
WITH (
  OIDS=FALSE
);
COMMENT ON TABLE space
  IS 'Information about board spaces.';
COMMENT ON COLUMN space.space_group IS 'Group or action to be performed.';
COMMENT ON COLUMN space.cost IS 'Cost to buy from bank.';
COMMENT ON COLUMN space.rent IS 'Rent with 0 houses.';
COMMENT ON COLUMN space.rent1 IS 'Rent with 1 house.';
COMMENT ON COLUMN space.rent2 IS 'Rent with 2 houses.';
COMMENT ON COLUMN space.rent3 IS 'Rent with 3 houses.';
COMMENT ON COLUMN space.rent4 IS 'Rent with 4 houses.';
COMMENT ON COLUMN space.rent5 IS 'Rent with a hotel.';
COMMENT ON COLUMN space.mortgage IS 'Mortgage value.';

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

-- Table: c_game_space

-- DROP TABLE c_game_space;

CREATE TABLE c_game_space
(
  game_id integer NOT NULL,
  space_id integer NOT NULL,
  owner_id integer, -- User id of owner (if any).
  houses integer NOT NULL DEFAULT 0, -- How many houses on the property.
  is_mortgaged boolean NOT NULL DEFAULT false, -- Whether the property is mortgaged.
  CONSTRAINT c_game_space_pkey PRIMARY KEY (game_id, space_id),
  CONSTRAINT c_game_space_owner_id_fkey FOREIGN KEY (owner_id)
      REFERENCES "user" (user_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);
ALTER TABLE c_game_space
  OWNER TO chudanj;
COMMENT ON COLUMN c_game_space.owner_id IS 'User id of owner (if any).';
COMMENT ON COLUMN c_game_space.houses IS 'How many houses on the property.';
COMMENT ON COLUMN c_game_space.is_mortgaged IS 'Whether the property is mortgaged.';

