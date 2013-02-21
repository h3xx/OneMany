-- Table: c_game_space

-- DROP TABLE c_game_space;

CREATE TABLE c_game_space
(
  game_id integer NOT NULL,
  space_id integer NOT NULL,
  owner_id integer, -- User id of owner (if any).
  houses integer NOT NULL DEFAULT 0, -- How many houses on the property.
  is_mortgaged boolean NOT NULL DEFAULT false, -- Whether the property is mortgaged.
  CONSTRAINT c_game_space_pkey PRIMARY KEY (game_id, space_id),
  CONSTRAINT c_game_space_owner_id_fkey FOREIGN KEY (owner_id)
      REFERENCES "user" (user_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
  CONSTRAINT c_game_space_houses_check CHECK (houses < 6)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE c_game_space
  OWNER TO chudanj;
COMMENT ON COLUMN c_game_space.owner_id IS 'User id of owner (if any).';
COMMENT ON COLUMN c_game_space.houses IS 'How many houses on the property.';
COMMENT ON COLUMN c_game_space.is_mortgaged IS 'Whether the property is mortgaged.';
