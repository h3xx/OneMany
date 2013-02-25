-- Table: rules

-- DROP TABLE rules;

CREATE TABLE rules
(
  rule_name character varying(255) NOT NULL,
  rule_default character varying(255) NOT NULL,
  rule_desc character varying(255),
  CONSTRAINT rules_pkey PRIMARY KEY (rule_name)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE rules
  OWNER TO odbc_group;
COMMENT ON TABLE rules
  IS 'Rule definitions.';
