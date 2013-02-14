<?php

# model of the board (i.e. how to draw)

class Board {
	private static $ICONS = [
		    "./images/thimble.jpg",      // index 0
		    "./images/battleship.jpg",      // index 1
		    "./images/car.jpg",      // index 2
		    "./images/tophat.jpg",      // index 3
		    "./images/dog.jpg",      // index 4
		    "./images/wheelbarrow.jpg",      // index 5
		    "./images/rider.jpg"      // index 6
	];

	# default constructor
	function __construct () {

		self::$props = [ //propertyID(index of array to use), owner, houses, group, mortgaged?
            [-1, 0, "GO", 0],  //GO
            [-1, 0, 1, 0],  //mediterranean
            [-1, 0, "CC", 0],  //COMM CHEST
            [-1, 0, 1, 0],  //baltic
            [-1, 0, "IT", 0],  //INCOME TAX
            [-1, 0, "RR", 0],  //reading RAILROAD index 3
            [-1, 0, 2, 0],  //oriental
            [-1, 0, "C", 0],  //CHANCE
            [-1, 0, 2, 0],  //vermont
            [-1, 0, 2, 0],  //connecticut
            [-1, 0, "JV", 0],  //JUST VISITING JAIL
            [-1, 0, 3, 0],  //st charles
            [-1, 0, "U", 0],  //electric UTILITY index 8
            [-1, 0, 3, 0],  //states
            [-1, 0, 3, 0],  //virginia
            [-1, 0, "RR", 0],  //Pennsyvania RAILROAD index 11
            [-1, 0, 4, 0],  //st james
            [-1, 0, "CC", 0],  //COMM CHEST
            [-1, 0, 4, 0],  //tennessee
            [-1, 0, 4, 0],  //new york
            [-1, 0, "FP", 0],  //FREE PARKING
            [-1, 0, 5, 0],  //kentucky
            [-1, 0, "C", 0],  //CHANCE
            [-1, 0, 5, 0],  //indiana
            [-1, 0, 5, 0],  //illinois
            [-1, 0, "RR", 0],  //B & O RAILROAD index 18
            [-1, 0, 6, 0],  //atlantic
            [-1, 0, 6, 0],  //ventnor
            [-1, 0, "U", 0],  //water works UTILITY  index 21
            [-1, 0, 6, 0],  //marvin gardens
            [-1, 0, "G2", 0],  //GO TO JAIL
            [-1, 0, 7, 0],  //pacific
            [-1, 0, 7, 0],  //north carolina
            [-1, 0, "CC", 0],  //COMM CHEST
            [-1, 0, 7, 0],  //pennsylvania AVENUE
            [-1, 0, "RR", 0],  //short line RAILROAD  index 26
            [-1, 0, "C", 0],  //CHANCE
            [-1, 0, 8, 0],  //Park place
            [-1, 0, "LT", 0],  //Luxury Tax
            [-1, 0, 8, 0]  //boardwalk
];
	}
