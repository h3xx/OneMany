-- Table: c_game_rules

-- DROP TABLE c_game_rules;

CREATE TABLE c_game_rules
(
  game_id integer NOT NULL,
  rule_name character varying(255) NOT NULL,
  rule_value character varying(255),
  CONSTRAINT c_game_rules_pkey PRIMARY KEY (game_id, rule_name),
  CONSTRAINT c_game_rules_game_id_fkey FOREIGN KEY (game_id)
      REFERENCES game (game_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT c_game_rules_rule_name_fkey FOREIGN KEY (rule_name)
      REFERENCES rules (rule_name) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);
COMMENT ON TABLE c_game_rules
  IS 'Rule overrides.';
