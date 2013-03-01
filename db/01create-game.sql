-- Table: game

-- DROP TABLE game;

CREATE TABLE game
(
  game_id serial NOT NULL,
  game_name character varying(1024) NOT NULL,
  last_roll integer[], -- Last roll performed.
  whoseturn integer, -- Whose turn it is.
  CONSTRAINT game_pkey PRIMARY KEY (game_id)
  CONSTRAINT game_whoseturn_fkey FOREIGN KEY (whoseturn)
      REFERENCES "user" (user_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);

COMMENT ON COLUMN game.last_roll IS 'Last roll performed.';
COMMENT ON COLUMN game.whoseturn IS 'Whose turn it is.';
