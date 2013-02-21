-- Table: chance

-- DROP TABLE chance;

CREATE TABLE chance
(
  "RECORDID" serial NOT NULL,
  "TEXT" character varying(100),
  "RESULT" character varying(10),
  CONSTRAINT chance_pkey PRIMARY KEY ("RECORDID")
)
WITH (
  OIDS=FALSE
);
COMMENT ON TABLE chance
  IS 'Data for chance cards';
