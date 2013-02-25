-- Table: c_game_chance

-- DROP TABLE c_game_chance;

CREATE TABLE c_game_chance
(
  game_id integer NOT NULL,
  chance_recordid integer NOT NULL,
  sequence integer NOT NULL,
  drawn_by integer, -- What user has drawn the card (if any).
  CONSTRAINT c_game_chance_pkey PRIMARY KEY (game_id, chance_recordid),
  CONSTRAINT c_game_chance_chance_recordid_fkey FOREIGN KEY (chance_recordid)
      REFERENCES chance ("RECORDID") MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT c_game_chance_drawn_by_fkey FOREIGN KEY (drawn_by)
      REFERENCES "user" (user_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT c_game_chance_game_id_fkey FOREIGN KEY (game_id)
      REFERENCES game (game_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);

COMMENT ON COLUMN c_game_chance.drawn_by IS 'What user has drawn the card (if any).';
