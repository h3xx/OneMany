-- Table: c_game_chance

-- DROP TABLE c_game_chance;

CREATE TABLE c_game_chance
(
  game_id integer NOT NULL,
  chance_recordid integer NOT NULL,
  sequence integer NOT NULL,
  is_drawn boolean NOT NULL DEFAULT false,
  CONSTRAINT c_game_chance_pkey PRIMARY KEY (game_id, chance_recordid),
  CONSTRAINT c_game_chance_chance_recordid_fkey FOREIGN KEY (chance_recordid)
      REFERENCES chance ("RECORDID") MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT c_game_chance_game_id_fkey FOREIGN KEY (game_id)
      REFERENCES game (game_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);
