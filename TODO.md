## OneMany Tasks

1. Use random function for rolling dice

2. Doubles gains user extra turn

3. 3 doubles in a row throws user in jail (no GO money)

4a. If user has enough money, buy un-owned property

4b. ASK user to buy any un-owned property they land on

5. Do the actual buying if they said 'yes' (end turn)

6. [A+] Auction property if they said 'no' (must figure out how to do so)

7. Pay regular rent

8. Pay double rent if they own the whole monopoly

9. Pay houses/hotel amount

10. Store rents/houses/hotel amount info

11. Utility and railroad cost (based on number of props owned by owner of property landed on)

12a. [B] Action based on card (DB to be given to you for this purpose)

12b. [C] +/- $75 for landing on card

13. Implement jailing

13a. Rolling doubles frees you, move that many, NO EXTRA TURN

13b. Pay $50 before roll to exit

13c. [A] use get out of jail free card

14. Pay $75 to bank on Luxury tax

15a. Pay $200 on Income Tax

15b. Ask user to pay $200 or 10% of total worth (DO NOT SHOW THEM THIS 10%, but CALCULATE IT WHEN THEY DECIDE)

16a. Implement payment for passing GO

16b. [A] Implement non-payment of GO money in certain circumstances

17. Implement appropriate bankruptcy procedure (move icon off of board? no more turns?)

18. Implement checking to see if someone has won (last person standing)

19. Implement ability for user to quit

20. [A+] Implement trading

21. Implement logging in system to allow user to only play when it's their turn

22. Implement logging in system addition to allow multiple SEPARATE GAMES and CHOICE OF GAME TO LOOK AT

23. Buy houses/hotels before roll

24. Implement selling (houses)/mortgaging (props) to pay debts instead of straight bankruptcy

25. Store prop cost/mortgage amounts

26. Un-mortgage properties at 110%

27. Transferring property to correct entity on player bankruptcy

28. Mortgage transferring fee (see above)

29a. Number of players set to number of available users in DB if new game exists.

29b. Allow number of players/which players to be selected at game start.

29c. Implement ‘New Game’ function to start new game.

29d. Implement ‘Join Game’ functions to join new (un-started) game.

29e. Implement creating user function to BEGIN game. Disable Join Game function for this game.

***NOTE: this list is NOT comprehensive.  There will be other things you will need to implement in order to fulfill these tasks correctly.***


## Just for fun

***These are not requirements for any level, but are good additions.  These are basically the ‘house rules’ ***

Use a configuration file to set these options site-wide.

* Landing on GO gets you $500

LandOnGoNets500 – Default is OFF

* All money paid by result of a landing on a space (not buying/mortgaging property), goes into a pot.  Landing on Free Parking gets you everything in the pot

FreeParkingFreeMoney – Default is OFF

* Put this amount of money in the pot every time it’s emptied (including to start the game)

FreeParkingSeedMoney – Default is 0

* Set auctioning on/off

PropertyAuctions – Default is OFF

* Set trading on or off

TradingAllowed – Default is OFF

* Allowing building houses/hotels unevenly (rules require that no property can have the equivalent of more than one house on it than any other of the same color group.  IE if New York only has one house, neither Tennessee nor St. James can have more than 2 houses).

UnevenImprovements – Default is OFF