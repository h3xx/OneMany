-- Table: user

-- DROP TABLE "user";

CREATE TABLE "user"
(
  user_id serial NOT NULL,
  user_name character varying(128) NOT NULL,
  login_hash character(40) NOT NULL, -- Hexadecimal SHA1 hash.
  login_salt character varying(40) NOT NULL,
  CONSTRAINT user_pkey PRIMARY KEY (user_id)
)
WITH (
  OIDS=FALSE
);
COMMENT ON TABLE "user"
  IS 'Login/password table';
COMMENT ON COLUMN "user".login_hash IS 'Hexadecimal SHA1 hash.';
