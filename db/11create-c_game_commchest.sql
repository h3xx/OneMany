-- Table: c_game_commchest

-- DROP TABLE c_game_commchest;

CREATE TABLE c_game_commchest
(
  game_id integer NOT NULL,
  commchest_recordid integer NOT NULL,
  sequence integer NOT NULL,
  is_drawn boolean NOT NULL DEFAULT false,
  CONSTRAINT c_game_commchest_pkey PRIMARY KEY (game_id, commchest_recordid),
  CONSTRAINT c_game_commchest_commchest_recordid_fkey FOREIGN KEY (commchest_recordid)
      REFERENCES commchest ("RECORDID") MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT c_game_commchest_game_id_fkey FOREIGN KEY (game_id)
      REFERENCES game (game_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);
