-- Table: user

-- DROP TABLE "user";

CREATE TABLE "user"
(
  user_id serial NOT NULL,
  user_name character varying(128) NOT NULL,
  user_email character varying(255) NOT NULL, -- The user's email address.
  reset_string character varying(128), -- String needed to reset the user's password via email.
  reset_expire timestamp without time zone,
  login_hash character(40) NOT NULL, -- Hexadecimal SHA1 hash.
  verify_string character varying(128) DEFAULT encode(gen_random_bytes(40), 'base64'::text), -- String needed to verify the user's email address. login_salt character varying(40) NOT NULL,
  verified boolean NOT NULL DEFAULT false, -- Whether the user has verified their email address.
  CONSTRAINT user_pkey PRIMARY KEY (user_id),
  CONSTRAINT user_user_email_key UNIQUE (user_email)
)
WITH (
  OIDS=FALSE
);
COMMENT ON TABLE "user"
  IS 'Login/password table';
COMMENT ON COLUMN "user".login_hash IS 'Hexadecimal SHA1 hash.';
COMMENT ON COLUMN "user".user_email IS 'The user''s email address.';
COMMENT ON COLUMN "user".reset_string IS 'String needed to reset the user''s password via email.';
COMMENT ON COLUMN "user".verify_string IS 'String needed to verify the user''s email address.';
COMMENT ON COLUMN "user".verified IS 'Whether the user has verified their email address.';
