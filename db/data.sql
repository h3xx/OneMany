-- -- -- DATA -- -- --

-- Table: chance

insert  into "chance"("RECORDID","TEXT","RESULT") values (1,'Advance to Go (Collect $200)','G0'),
	(2,'Advance to Illinois Ave.','G17'),
	(3,'Advance to St. Charles Place – if you pass Go, collect $200','G11'),
	(4,'Bank pays you dividend of $50','50'),
	(5,'Go back 3 spaces','G3'),
	(6,'Go directly to Jail – do not pass Go, do not collect $200','G2'),
	(7,'Pay poor tax of $15','-15'),
	(8,'Take a ride on the Reading – if you pass Go collect $200','G5'),
	(9,'Take a walk on the Boardwalk – advance token to Boardwalk','G39'),
	(10,'You have been elected chairman of the board – pay each player $50','PA50'),
	(11,'Your building loan matures – collect $150','150');

-- Table: commchest

insert  into "commchest"("RECORDID","TEXT","RESULT") values
	(1,'Advance to Go (Collect $200)','G0'),
	(2,'Bank error in your favor – collect $200','200'),
	(3,'Doctor''s fees {fee} – Pay $50','-50'),
	(4,'Go to jail – go directly to jail – Do not pass Go, do not collect $200','G2'),
	(5,'Grand opera Night – collect $50 from every player for opening night seats','CA50'),
	(6,'Tax refund – collect $20','20'),
	(7,'Pay Hospital Fees of $100','-100'),
	(8,'Pay School tax of $150','-150'),
	(9,'Receive for services $25','25'),
	(10,'You have won second prize in a beauty contest– collect $10','10'),
	(11,'You inherit $100','100'),
	(12,'From sale of stock you get $45','45'),
	(13,'Xmas fund matures - collect $100','100');
