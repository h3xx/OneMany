-- Table: rules

insert  into "rules"("rule_name","rule_default","rule_desc") values
	('starting_cash',	'1500',		'How much cash each player starts with.'),
	('go_salary',		'200',		'How much cash the player gets for passing GO.'),
	('free_parking',	'0',		'Whether "Free Parking" is a thing.'),
	('auctions',		'1',		'Whether unbought properties may be auctioned by the bank upon landing on the space.'),
	('incometax_flat',	'200',		'How much Income Tax charges (flat rate option).'),
	('incometax_perc',	'10',		'How much Income Tax charges (percentage option).'),
	('buy_gojf_card',	'1',		'Whether players may purchase a "Get out of Jail Free" card from another player.'),
	('jail_bail',		'50',		'How much a player pays to get out of jail.');
