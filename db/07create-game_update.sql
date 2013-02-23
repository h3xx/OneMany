-- Table: game_update

-- DROP TABLE game_update;

CREATE TABLE game_update
(
  game_id integer NOT NULL,
  game_newstate serial NOT NULL,
  game_change character varying(4096) NOT NULL,
  CONSTRAINT game_update_pkey PRIMARY KEY (game_id, game_newstate),
  CONSTRAINT game_update_game_id_fkey FOREIGN KEY (game_id)
      REFERENCES game (game_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);
