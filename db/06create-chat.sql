-- Table: chat

-- DROP TABLE chat;

CREATE TABLE chat
(
  game_id integer NOT NULL,
  chat_id serial NOT NULL,
  user_id integer NOT NULL,
  chat_text character varying(1024) NOT NULL,
  chat_time timestamp without time zone NOT NULL DEFAULT now(),
  CONSTRAINT chat_pkey PRIMARY KEY (chat_id),
  CONSTRAINT chat_game_id_fkey FOREIGN KEY (game_id)
      REFERENCES game (game_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);
