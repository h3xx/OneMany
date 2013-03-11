-- Table: game

-- DROP TABLE game;

CREATE TABLE game
(
  game_id serial NOT NULL,
  game_name character varying(1024) NOT NULL,
  last_roll integer[] NOT NULL DEFAULT '{1,1}'::integer[], -- Last roll performed.
  auction_user integer, -- Who has made the highest bid in the auction.
  auction_space integer, -- What space is being currently auctioned.
  auction_bid integer, -- How much the highest bid in the current auction was.
  auction_expire timestamp without time zone, -- When the current auction expires.
  auction_reportedclosed boolean NOT NULL DEFAULT false, -- Whether the auction, when closed, has been reported to the game_update table.
  free_parking integer NOT NULL DEFAULT 0, -- The total for free parking.
  CONSTRAINT game_pkey PRIMARY KEY (game_id)
)
WITH (
  OIDS=FALSE
);

COMMENT ON COLUMN game.last_roll IS 'Last roll performed.';
COMMENT ON COLUMN game.auction_user IS 'Who has made the highest bid in the auction.';
COMMENT ON COLUMN game.auction_space IS 'What space is being currently auctioned.';
COMMENT ON COLUMN game.auction_bid IS 'How much the highest bid in the current auction was.';
COMMENT ON COLUMN game.auction_expire IS 'When the current auction expires.';
COMMENT ON COLUMN game.auction_reportedclosed IS 'Whether the auction, when closed, has been reported to the game_update table.';
COMMENT ON COLUMN game.free_parking IS 'The total for free parking.';
