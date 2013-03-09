-- Table: c_user_game

-- DROP TABLE c_user_game;

CREATE TABLE c_user_game
(
  user_id integer NOT NULL,
  game_id integer NOT NULL,
  on_space integer NOT NULL DEFAULT 0,
  in_jail boolean NOT NULL DEFAULT false, -- Whether the user is in jail.
  cash integer NOT NULL,
  has_gojf boolean NOT NULL DEFAULT false,
  doubles integer NOT NULL DEFAULT 0, -- How many times in a row the user has rolled doubles.
  sequence serial NOT NULL, -- What order the players take turns.
  extra_turn boolean NOT NULL DEFAULT false, -- Whether the user can go again after the current turn has completed.
  ask_buy integer, -- What space (if any) the user is being asked if they want to buy.
  token integer NOT NULL DEFAULT 0, -- What token the player is using for this game.
  CONSTRAINT c_user_game_pkey PRIMARY KEY (user_id, game_id),
  CONSTRAINT c_user_game_ask_buy_fkey FOREIGN KEY (ask_buy)
      REFERENCES space (space_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT c_user_game_game_id_fkey FOREIGN KEY (game_id)
      REFERENCES game (game_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT c_user_game_on_space_fkey FOREIGN KEY (on_space)
      REFERENCES space (space_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT c_user_game_user_id_fkey FOREIGN KEY (user_id)
      REFERENCES "user" (user_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);

COMMENT ON COLUMN c_user_game.has_gojf IS 'Whether the user has a Get out of Jail Free card.';
COMMENT ON COLUMN c_user_game.doubles IS 'How many times in a row the user has rolled doubles.';
COMMENT ON COLUMN c_user_game.sequence IS 'What order the players take turns.';
COMMENT ON COLUMN c_user_game.extra_turn IS 'Whether the user can go again after the current turn has completed.';
COMMENT ON COLUMN c_user_game.ask_buy IS 'What space (if any) the user is being asked if they want to buy.';
COMMENT ON COLUMN c_user_game.in_jail IS 'Whether the user is in jail.';
COMMENT ON COLUMN c_user_game.token IS 'What token the player is using for this game.';
