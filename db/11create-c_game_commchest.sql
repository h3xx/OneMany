-- Table: c_game_commchest

-- DROP TABLE c_game_commchest;

CREATE TABLE c_game_commchest
(
  game_id integer NOT NULL,
  commchest_recordid integer NOT NULL,
  sequence integer NOT NULL,
  drawn_by integer, -- What user has drawn the card (if any).
  CONSTRAINT c_game_commchest_pkey PRIMARY KEY (game_id, commchest_recordid),
  CONSTRAINT c_game_commchest_commchest_recordid_fkey FOREIGN KEY (commchest_recordid)
      REFERENCES commchest ("RECORDID") MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT c_game_commchest_drawn_by_fkey FOREIGN KEY (drawn_by)
      REFERENCES "user" (user_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT c_game_commchest_game_id_fkey FOREIGN KEY (game_id)
      REFERENCES game (game_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);

COMMENT ON COLUMN c_game_commchest.drawn_by IS 'What user has drawn the card (if any).';
