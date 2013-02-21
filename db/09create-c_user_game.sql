-- Table: c_user_game

-- DROP TABLE c_user_game;

CREATE TABLE c_user_game
(
  user_id integer NOT NULL,
  game_id integer NOT NULL,
  CONSTRAINT c_user_game_pkey PRIMARY KEY (user_id, game_id),
  CONSTRAINT c_user_game_game_id_fkey FOREIGN KEY (game_id)
      REFERENCES game (game_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT c_user_game_user_id_fkey FOREIGN KEY (user_id)
      REFERENCES "user" (user_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);
