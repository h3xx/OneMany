-- Table: space

-- DROP TABLE space;

CREATE TABLE space
(
  space_id serial NOT NULL,
  space_name character varying(255) NOT NULL,
  space_group character varying(255) NOT NULL, -- Group or action to be performed.
  cost integer NOT NULL, -- Cost to buy from bank.
  rent integer NOT NULL, -- Rent with 0 houses.
  rent1 integer NOT NULL, -- Rent with 1 house.
  rent2 integer NOT NULL, -- Rent with 2 houses.
  rent3 integer NOT NULL, -- Rent with 3 houses.
  rent4 integer NOT NULL, -- Rent with 4 houses.
  rent5 integer NOT NULL, -- Rent with a hotel.
  mortgage integer NOT NULL, -- Mortgage value.
  housecost integer NOT NULL, -- How much one house costs.
  CONSTRAINT space_pkey PRIMARY KEY (space_id)
)
WITH (
  OIDS=FALSE
);
COMMENT ON TABLE space
  IS 'Information about board spaces.';
COMMENT ON COLUMN space.space_group IS 'Group or action to be performed.';
COMMENT ON COLUMN space.cost IS 'Cost to buy from bank.';
COMMENT ON COLUMN space.rent IS 'Rent with 0 houses.';
COMMENT ON COLUMN space.rent1 IS 'Rent with 1 house.';
COMMENT ON COLUMN space.rent2 IS 'Rent with 2 houses.';
COMMENT ON COLUMN space.rent3 IS 'Rent with 3 houses.';
COMMENT ON COLUMN space.rent4 IS 'Rent with 4 houses.';
COMMENT ON COLUMN space.rent5 IS 'Rent with a hotel.';
COMMENT ON COLUMN space.mortgage IS 'Mortgage value.';
COMMENT ON COLUMN space.housecost IS 'How much one house costs.';
