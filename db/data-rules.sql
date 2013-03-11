-- Table: rules

insert  into "rules"("rule_name","rule_default","rule_desc") values
	('starting_cash',	'1500',		'How much cash each player starts with.'),
	('go_salary',		'200',		'How much cash the player gets for passing GO.'),
	('min_players',		'2',		'Minimum players needed for a game.'),
	('max_players',		'5',		'Maximul players allowed for a game.'),
	('free_parking',	'0',		'Whether "Free Parking" is a thing.'),
	('auctions',		'1',		'Whether unbought properties may be auctioned by the bank upon landing on the space.'),
	('auction_startbid_perc','50',		'What percentage of the face value auctions start at.'),
	('incometax_flat',	'200',		'How much Income Tax charges (flat rate option).'),
	('incometax_perc',	'10',		'How much Income Tax charges (percentage option).'),
	('luxurytax',		'75',		'How much Luxury Tax charges (flat rate).'),
	('buy_gojf_card',	'1',		'Whether players may purchase a "Get out of Jail Free" card from another player.'),
	('jail_bail',		'50',		'How much a player pays to get out of jail.'),
	('jail_doubles',	'3',		'How many times in a row the user can roll doubles before being thrown in jail (0 to disable).'),
	('parallel_improvement','1',		'Whether improvements must be bought in parallel.'),
	('monopoly_rentfactor',	'2',		'The rent multiplier on regular properties in the event of a monopoly.'),
	('auction_timeout',	'30 seconds',	'How long until auctions expire.');
