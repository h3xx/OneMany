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
