-- Table: game

-- DROP TABLE game;

CREATE TABLE game
(
  game_id serial NOT NULL,
  game_name character varying(1024) NOT NULL,
  last_roll integer[], -- Last roll performed.
  CONSTRAINT game_pkey PRIMARY KEY (game_id)
)
WITH (
  OIDS=FALSE
);

COMMENT ON COLUMN game.last_roll IS 'Last roll performed.';
