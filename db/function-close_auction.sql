-- Function: close_auction(integer)
--
-- Database engine: PostgreSQL 9.2
--
-- Closes an auction and reports it to the update queue.
--
-- Returns whether the update was performed.
--
-- @author: Dan Church <h3xx@gmx.com>
-- @license: GPL v3.0

-- DROP FUNCTION close_auction(integer)

CREATE OR REPLACE FUNCTION close_auction(_game_id integer)
  RETURNS boolean AS
$BODY$

begin

	update "game"
		set
			"auction_reportedclosed" = true
		where
			"game_id" = _game_id and
			"auction_reportedclosed" = false;

	if not found then
		return false;
	end if;

	return true;
end;

$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
