<?

$ICONS = array (
            "./images/thimble.jpg",      // index 0
            "./images/battleship.jpg",      // index 1
            "./images/car.jpg",      // index 2
            "./images/tophat.jpg",      // index 3
            "./images/dog.jpg",      // index 4
            "./images/wheelbarrow.jpg",      // index 5
            "./images/rider.jpg"      // index 6
);

$PLAYERS = array (  //playerid(is index of array to use), name?, tokenid, cash, getouttajailcards, locationOnBoard, isInJail?
            array("Zoe", 1, 1500, 0, 0, 0),
            array("Mal", 2, 1400, 0, 0, 0),
            array("River", 3, 1300, 0, 0, 0),
            array("Kaylee", 4, 1450, 0, 0, 0),
            array("Wash", 5, 1350, 0, 0, 0),
            array("Jayne", 6, 1425, 0, 0, 0),
            array("Book", 0, 1375, 0, 0, 0)
);

DEFINE (BANK_START, 15140);

$PROPERTIES = array( //propertyID(index of array to use), owner, houses, group, mortgaged?
            array(-1, 0, "GO", 0),  //GO
            array(-1, 0, 1, 0),  //mediterranean
            array(-1, 0, "CC", 0),  //COMM CHEST
            array(-1, 0, 1, 0),  //baltic
            array(-1, 0, "IT", 0),  //INCOME TAX
            array(-1, 0, "RR", 0),  //reading RAILROAD index 3
            array(-1, 0, 2, 0),  //oriental
            array(-1, 0, "C", 0),  //CHANCE
            array(-1, 0, 2, 0),  //vermont
            array(-1, 0, 2, 0),  //connecticut
            array(-1, 0, "JV", 0),  //JUST VISITING JAIL
            array(-1, 0, 3, 0),  //st charles
            array(-1, 0, "U", 0),  //electric UTILITY index 8
            array(-1, 0, 3, 0),  //states
            array(-1, 0, 3, 0),  //virginia
            array(-1, 0, "RR", 0),  //Pennsyvania RAILROAD index 11
            array(-1, 0, 4, 0),  //st james
            array(-1, 0, "CC", 0),  //COMM CHEST
            array(-1, 0, 4, 0),  //tennessee
            array(-1, 0, 4, 0),  //new york
            array(-1, 0, "FP", 0),  //FREE PARKING
            array(-1, 0, 5, 0),  //kentucky
            array(-1, 0, "C", 0),  //CHANCE
            array(-1, 0, 5, 0),  //indiana
            array(-1, 0, 5, 0),  //illinois
            array(-1, 0, "RR", 0),  //B & O RAILROAD index 18
            array(-1, 0, 6, 0),  //atlantic
            array(-1, 0, 6, 0),  //ventnor
            array(-1, 0, "U", 0),  //water works UTILITY  index 21
            array(-1, 0, 6, 0),  //marvin gardens
            array(-1, 0, "G2", 0),  //GO TO JAIL
            array(-1, 0, 7, 0),  //pacific
            array(-1, 0, 7, 0),  //north carolina
            array(-1, 0, "CC", 0),  //COMM CHEST
            array(-1, 0, 7, 0),  //pennsylvania AVENUE
            array(-1, 0, "RR", 0),  //short line RAILROAD  index 26
            array(-1, 0, "C", 0),  //CHANCE
            array(-1, 0, 8, 0),  //Park place
            array(-1, 0, "LT", 0),  //Luxury Tax
            array(-1, 0, 8, 0)  //boardwalk
);


//$myPlayers = PLAYERS;
//$myProps = PROPERTIES;
$myPlayers = $PLAYERS;
$myProps = $PROPERTIES;
$myIcons = $ICONS;
$whoseTurn = 0;

readOneManyFile();

doTurn($whoseTurn);


writeOneManyFile();


?>

<HTML>

<HEAD>

    <TITLE>MONOPOLY BOARD TEST</TITLE>
    
    <LINK REL="stylesheet" type="text/css" href="./styles/main.css" />
    
</HEAD>


<BODY>

<? printTable(); ?>

</BODY>

</HTML>




<?

function readOneManyFile() {
    
    global $myPlayers, $myProps, $myIcons, $whoseTurn;
    
    if ($FILE_IN = fopen("onemany.txt", "r+")) {//file exists
        
        $whoseTurn = rtrim(fgets($FILE_IN));
        
        $numPlayers = rtrim(fgets($FILE_IN));

        for($i = 0; $i < $numPlayers; $i++) {
            $myPlayersTemp[] = explode(",", rtrim(fgets($FILE_IN)));
        }
        $myPlayers = $myPlayersTemp;
        for($j = 0; $j < 40; $j++) {
            $myPropsTemp[] = explode(",", rtrim(fgets($FILE_IN)));
        }
        $myProps = $myPropsTemp;
    }
    
    fclose($FILE_IN);
}

function writeOneManyFile() {
    
    global $myPlayers, $myProps, $myIcons, $whoseTurn;
    
    $FILE_IN = fopen("onemany.txt", "w+");
    
    fwrite($FILE_IN, $whoseTurn . "\n");

    fwrite($FILE_IN, count($myPlayers) . "\n");
    //echo "WRITING OUT: MYPLAYERS\n<BR>";
    for ($i = 0; $i < count($myPlayers); $i++) {
        fwrite($FILE_IN, implode(",", $myPlayers[$i]) . "\n");
    }
    
    //echo "WRITING OUT: myProps\n<BR>";
    for ($j = 0; $j < 40; $j++) {
        fwrite($FILE_IN, implode(",", $myProps[$j]) . "\n");
    }
    //echo "WRITING OUT: MYICONS\n<BR>";
    //fwrite($FILE_IN, implode(",", $myIcons) . "\n");
    //echo "*** DONE WRITING OUT\n<BR>";
    
    fclose($FILE_IN);
    
}

function movePlayer($playerID, $spaces) {
    
    global $myPlayers;
    
    $curLocation = $myPlayers[$playerID][4];
    
    $total = $curLocation + $spaces;
    
    if ($total > 39) {
        $total -= 40;
    }
    
    $myPlayers[$playerID][4] = $total;
    
}



function doRoll($playerID) {
    
    global $myPlayers;
    
    //REPLACE THIS WITH FUNCTIONALITY TO ROLL TWO DICE and MOVE THE PLAYER THAT MANY SPACES
    movePlayer($playerID, 7);
    
    //OTHER POSSIBLE FUNCTIONALITY
    //KEEP TRACK OF DOUBLES - ON THIRD DOUBLE IN A ROW, MOVE TO JAIL W/O GO MONEY
    
}



function landedHere($playerID) {
    
    global $myPlayers, $myProps;
    
    $loc = $myPlayers[$playerID][4]; //location ID of property we landed on
    
    $landed_on_prop_array = $myProps[$loc]; //gets location array for property we landed on
                                            //array structure => owner, houses, group, mortgaged?

    //are we on a regular (colored) property or on a odd one?
    if (preg_match('#^\d$#',$landed_on_prop_array[2])) {  //if we are on a grouped property, do these things
        
        //is it owned or not?
        if ($landed_on_prop_array[0] == -1) { //not owned
            ask_user_to_buy($playerID, $loc);
        }
        else {  //it is owned...pay rent
            pay_rent($playerID, $loc);
        }
        
    }
    else {  //if it's a special prop, do that instead
        //determine what kind
        switch ($landed_on_prop_array[2]) {
            case "U":
            case "RR":
                        pay_special($playerID, $loc);
                        break;
            case "C":
            case "CC":
                        doCardAction($landed_on_prop_array[2], $playerID);
                        break;
            case "G2":
                        sendToJail($playerID);
                        break;
            case "IT":
                        incomeTax($playerID);
                        break;
            case "LT":
                        luxuryTax($playerID);
                        break;
            default:
                        //DO NOTHING for corners other than go to jail
                        break;
        }
       
    }

    
}





function ask_user_to_buy($whoToAsk, $loc) {
    
    //ask the user to buy the property
    echo "<BR><P>Would you like to buy this property?</P><BR>";
    
}

function do_user_buy($whoBuys, $loc) {
    
}
            
            
function pay_rent($whoPays, $loc) {
    
    global $myProps, $myPlayers;
    
    $this_owner = $myProps[$loc][0];
    $this_owner_name = $myPlayers[$this_owner][0];
    
    echo "<BR><P>You owe $this_owner_name some money.</P><BR>";
    
    //houses?  pay based on rate
    
    //else, does owner own the monopoly (all in the group)?, if so double regular rent
    
    //else, pay normal rent
    
    
}
            
            
function pay_special($whoLanded, $loc) {
    
    global $myProps, $myPlayers;
    
    $this_owner = $myProps[$loc][0];
    $this_owner_name = $myPlayers[$this_owner][0];
    
    echo "<BR><P>You owe $this_owner_name some money.</P><BR>";
    
    //figure out if its utility/railroad
    
    //if utilty, figure out if owner owns both or just one, roll, pay 4/10 times the amount
    
    //if railroad, figure out how many RRs owner owns, then multiply owned by double $25 that many times (25, 50, 100, 200)
    
}
            
            
function doCardAction($wherePlayerIs, $playerID) {
    
    //based on which card it is, grab that card and do what it says
    
    echo "<BR><P>You would normally draw a card and do that.</P><BR>";
    
}
            
            
function sendToJail($whoToSend) {
    
    //place user in jail WITHOUT GIVING THEM 200 FOR PASSING GO
    global $myPlayers;
    
    $myPlayers[$whoToSend][4] = 10;
    //$myPlayers[$whoToSend][5] = 1;  //UN-COMMENT THIS LINE ONCE YOU IMPLEMENT JAIL-HOUSE ROLLING!!!
    
}
            
            
function incomeTax($whoPays) {
    
    //ask user to pay $200 or 10%
    echo "<BR><P>Would you like to pay $200 or 10% of you holdings?</P><BR>";
    
}

function doPayIncomeTax($whoPays, $option) {
    
}
            
            
function luxuryTax($whoPays) {
    
    //pay bank $75
    echo "<BR><P>You would normally pay $75 to the bank.</P><BR>";
    
}






function printTable() {
    
    global $myProps, $myIcons;
    
    global $myPlayers;
    
    print "<TABLE>";
    
    //PRINT THE TOP ROW
    
    print "<TR>\n";
    
    for($i = 0; $i < 11; $i++) {
        
        if($i == 0 || $i == 10) {
            print "<TD class='propCrnr propMain";
            if ($i == 0) {
                print " go ";
            }
            else {
                print " jail ";
            }
            print "'>\n";
            
            print "<div class='iconsCorner'>";
            foreach ($myPlayers as $player) {
                if ($player[4] == $i) { //if the player is in this spot, show their icon
                    $icon_path = $player[1];
                    $icon_path = $myIcons[$icon_path];
                    $name = $player[0];
                    print "<img src='" . $icon_path . "' class='token' alt='" . $name . "' title='" . $name . "'>";
                }
            }
            print "</TD>";
        }
        else {
            print "<TD class='propVert propMain'>";
            print "<div class='iconsVert'>&nbsp;</div>";
            print "<div class='vert'>";
            foreach ($myPlayers as $player) {
                if ($player[4] == $i) { //if the player is in this spot, show their icon
                    $icon_path = $player[1];
                    $icon_path = $myIcons[$icon_path];
                    $name = $player[0];
                    print "<img src='" . $icon_path . "' class='token' alt='" . $name . "' title='" . $name . "'>";
                }
            }
            print "&nbsp;";
            print "</div>";
            print "<div class='iconsVert'>";
            
            if ($myProps[$i][1] == 5) {
                print "<img src='./images/Monopoly_Hotel.jpg' width='19'>";
            }
            else {
                for ($j = 0; $j < $myProps[$i][1]; $j++) {
                    print "<img src='./images/s_house.jpg' class='casa'>";
                }    
            }
            
            
            print "&nbsp;";
            print "</div>";
            print "</TD>";
        }

    }
    
    print "</TR>\n";
    
    
    
    //PRINT THE MIDDLE ROWS
    for ($k = 1; $k < 10; $k++) {
        //left side is 40 - $i (ie, 39-31)
        //right side is 40 - $i - (30 - (2 * $i)) (ie, 11-19, respectively)
        $ls = 40 - $k;
        $rs = $ls - (30 - (2 * $k));

    print " <TR>\n";
    print "        <TD class='propHorz propMain'>";
    print "            <div class='iconsHorz'>&nbsp;</div>";
    print "            <div class='horz'>";
    foreach ($myPlayers as $player) {
                if ($player[4] == $ls) { //if the player is in this spot, show their icon
                    $icon_path = $player[1];
                    $icon_path = $myIcons[$icon_path];
                    $name = $player[0];
                    print "<img src='" . $icon_path . "' class='token' alt='" . $name . "' title='" . $name . "'>";
                }
            }
    print "&nbsp;</div>";
    print "            <div class='iconsHorz'>";
    if ($myProps[$ls][1] == 5) {
                print "<img src='./images/Monopoly_Hotel.jpg' width='19'>";
    }
    else {
        for ($j = 0; $j < $myProps[$ls][1]; $j++) {
                print "<img src='./images/s_house.jpg' class='casa'>";
        }
    }
    print "</div>";
    print "        </td>";
    print "        <TD colspan=9>";
    print "            &nbsp;";
    print "        </td>";
    print "        <TD class='propHorz propMain'>";
    print "            <div class='iconsHorz'>&nbsp;</div>";
    print "            <div class='horz'>";
    foreach ($myPlayers as $player) {
                if ($player[4] == $rs) { //if the player is in this spot, show their icon
                    $icon_path = $player[1];
                    $icon_path = $myIcons[$icon_path];
                    $name = $player[0];
                    print "<img src='" . $icon_path . "' class='token' alt='" . $name . "' title='" . $name . "'>";
                }
            }
    print "&nbsp;</div>";
    print "            <div class='iconsHorz'>&nbsp;</div>";
    print "        </td>";
    print "    </TR>\n";
        

    }
    
    
    
    
    //PRINT THE BOTTOM ROW
    
    
    for($i = 30; $i > 19; $i--) {
        
        if($i == 30 || $i == 20) {
            print "<TD class='propCrnr propMain'>\n";
            
            print "<div class='iconsCorner'>";
            foreach ($myPlayers as $player) {
                if ($player[4] == $i) { //if the player is in this spot, show their icon
                    $icon_path = $player[1];
                    $icon_path = $myIcons[$icon_path];
                    $name = $player[0];
                    print "<img src='" . $icon_path . "' class='token' alt='" . $name . "' title='" . $name . "'>";
                }
            }
            print "</TD>";
        }
        else {
            print "<TD class='propVert propMain'>";
            print "<div class='iconsVert'>";
            
            if ($myProps[$i][1] == 5) {
                print "<img src='./images/Monopoly_Hotel.jpg' width='19'>";
            }
            else {
                for ($j = 0; $j < $myProps[$i][1]; $j++) {
                    print "<img src='./images/s_house.jpg' class='casa'>";
                }
            }
            
            
            print "&nbsp;";
            print "</div>";
            print "<div class='vert'>";
            foreach ($myPlayers as $player) {
                if ($player[4] == $i) { //if the player is in this spot, show their icon
                    $icon_path = $player[1];
                    $icon_path = $myIcons[$icon_path];
                    $name = $player[0];
                    print "<img src='" . $icon_path . "' class='token' alt='" . $name . "' title='" . $name . "'>";
                }
            }
            print "&nbsp;";
            print "</div>";
            print "<div class='iconsVert'>&nbsp;</div>";
            print "</TD>";
        }

    }
    
    print "</TR>\n";
    
    //END OF BOTTOM ROW
    
    
    
    //PRINT ENDING TABLE TAG
    
    print "</TABLE>\n";

}








function doTurn($playerID) {
    
    global $whoseTurn, $myPlayers;
    
    doRoll($playerID);
    landedHere($playerID);
    
    $numPlayers = count($myPlayers);
    $whoseTurn++;
    
    if ($whoseTurn >= $numPlayers) {
        $whoseTurn = 0;
    }
    
}




//array_deep_copy USED, ALL CREDIT TO THE AUTHOR
//POSTED BY
//elkabong at samsalisbury dot co dot uk
//POSTED AT
//http://php.net/manual/en/ref.array.php

function array_deep_copy (&$array, &$copy, $maxdepth=50, $depth=0) {
    if($depth > $maxdepth) { $copy = $array; return; }
    if(!is_array($copy)) $copy = array();
    foreach($array as $k => &$v) {
        if(is_array($v)) {        array_deep_copy($v,$copy[$k],$maxdepth,++$depth);
        } else {
            $copy[$k] = $v;
        }
    }
}


//
//
//
//  OLD BOARD LAYOUT
//
//
//
/*
<!--
<TABLE>
    <TR>
        <TD class="propCrnr propMain">
            <img src="./images/go200.png" class="propCrnr">
        </TD>
        <TD class="propVert propMain">
            <div class="iconsVert">&nbsp;</div>
            <div class="vert">Mediterranean</div>
            <div class="dp">&nbsp;</div>
        </TD>
        <TD class="propVert propMain">
            Community Chest
        </TD>
        <TD class="propVert propMain">
            <div class="iconsVert">&nbsp;</div>
            <div class="vert">Baltic</div>
            <div class="dp">&nbsp;</div>
        </TD>
        <TD class="propVert propMain">
            Income Tax
        </TD>
        <TD class="propVert propMain">
            Reading Railroad
        </TD>
        <TD class="propVert propMain">
            <div class="iconsVert">&nbsp;</div>
            <div class="vert">Oriental</div>
            <div class="lb">&nbsp;</div>
        </TD>
        <TD class="propVert propMain">
            Chance<BR>
            <img src="./images/Question_Mark_BIG.JPG" width="50">
        </TD>
        <TD class="propVert propMain">
            <div class="iconsVert">&nbsp;</div>
            <div class="vert">Vermont</div>
            <div class="lb">&nbsp;</div>
        </TD>
        <TD class="propVert propMain">
            <div class="iconsVert">&nbsp;</div>
            <div class="vert">Connecticut</div>
            <div class="lb">&nbsp;</div>
        </TD>
        <TD class="propCrnr propMain">
            <img src="./images/JustVisiting.jpg" class="propCrnr">
        </TD>
    </TR>
    <TR>
        <TD class="propHorz propMain">
            <div class="iconsHorz">&nbsp;</div>
            <div class="horz">Boardwalk</div>
            <div class="b">&nbsp;</div>
        </td>
        <TD colspan=9>
            &nbsp;
        </TD>
        <TD class="propHorz propMain">
            <div class="p">&nbsp;</div>
            <div class="horz">St. Charles Place</DIV>
            <div class="iconsHorz">&nbsp;</div>
        </TD>
    </TR>
    <TR>
        <TD class="propHorz propMain">
            Luxury Tax
        </td>
        <TD colspan=9>
            &nbsp;
        </TD>
        <TD class="propHorz propMain">
            Electric Company
        </TD>
    </TR>
    <TR>
        <TD class="propHorz propMain">
            <div class="iconsHorz">&nbsp;</div>
            <div class="horz">Park Place</div>
            <div class="b">&nbsp;</div>
        </td>
        <TD colspan=9>
            &nbsp;
        </TD>
        <TD class="propHorz propMain">
            <div class="p">&nbsp;</div>
            <div class="horz">States Avenue</div>
            <div class="iconsHorz">&nbsp;</div>
        </TD>
    </TR>
    <TR>
        <TD class="propHorz propMain">
            Chance<BR>
            <img src="./images/Question_Mark_BIG.JPG" height="50">
        </td>
        <TD colspan=9>
            &nbsp;
        </TD>
        <TD class="propHorz propMain">
            <div class="p">&nbsp;</div>
            <div class="horz">Virginia</div>
            <div class="iconsHorz">&nbsp;</div>
        </TD>
    </TR>
    <TR>
        <TD class="propHorz propMain">
            Short Line
        </td>
        <TD colspan=9>
            &nbsp;
        </TD>
        <TD class="propHorz propMain">
            Pennsylvania Railroad
        </TD>
    </TR>
    <TR>
        <TD class="propHorz propMain">
            <div class="iconsHorz">&nbsp;</div>
            <div class="horz">Pennsylvania</div>
            <div class="g">&nbsp;</div>
        </td>
        <TD colspan=9>
            &nbsp;
        </TD>
        <TD class="propHorz propMain">
            <div class="o">&nbsp;</div>
            <div class="horz">St. James</div>
            <div class="iconsHorz">&nbsp;</div>
        </TD>
    </TR>
    <TR>
        <TD class="propHorz propMain">
            Community Chest
        </td>
        <TD colspan=9>
            &nbsp;
        </TD>
        <TD class="propHorz propMain">
            Chance<BR>
            <img src="./images/Question_Mark_BIG.JPG" height="50">
        </TD>
    </TR>
    <TR>
        <TD class="propHorz propMain">
            <div class="iconsHorz">&nbsp;</div>
            <div class="horz">North Carolina</div>
            <div class="g">&nbsp;</div>
        </td>
        <TD colspan=9>
            &nbsp;
        </TD>
        <TD class="propHorz propMain">
            <div class="o">&nbsp;</div>
            <div class="horz">Tennessee</div>
            <div class="iconsHorz">&nbsp;</div>
        </TD>
    </TR>
    <TR>
        <TD class="propHorz propMain">
            <div class="iconsHorz">&nbsp;</div>
            <div class="horz">Pacific</div>
            <div class="g">&nbsp;</div>
        </td>
        <TD colspan=9>
            &nbsp;
        </TD>
        <TD class="propHorz propMain">
            <div class="o">&nbsp;</div>
            <div class="horz">New York</div>
            <div class="iconsHorz">&nbsp;</div>
        </TD>
    </TR>
    <TR>
        <TD class="propCrnr propMain">
            <img src="./images/go-to-jail.jpg" class="propCrnr">
        </TD>
        <TD class="propVert propMain">
            <div class="y">&nbsp;</div>
            <div class="vert">Marvin Gardens</div>
            <div class="iconsVert">&nbsp;</div>
        </TD>
        <TD class="propVert propMain">
            Water Works
        </TD>
        <TD class="propVert propMain">
            <div class="y">&nbsp;</div>
            <div class="vert">Ventnor</div>
            <div class="iconsVert">&nbsp;</div>
        </TD>
        <TD class="propVert propMain">
            <div class="y">&nbsp;</div>
            <div class="vert">Atlantic</div>
            <div class="iconsVert">&nbsp;</div>
        </TD>
        <TD class="propVert propMain">
            B &amp; O Railroad
        </TD>
        <TD class="propVert propMain">
            <div class="r">&nbsp;</div>
            <div class="vert">Illinois</div>
            <div class="iconsVert">&nbsp;</div>
        </TD>
        <TD class="propVert propMain">
            <div class="r">&nbsp;</div>
            <div class="vert">Indiana</div>
            <div class="iconsVert">&nbsp;</div>
        </TD>
        <TD class="propVert propMain">
            Chance<BR>
            <img src="./images/Question_Mark_BIG.JPG" width="50">
        </TD>
        <TD class="propVert propMain">
            <div class="r">&nbsp;</div>
            <div class="vert">Kentucky</div>
            <div class="iconsVert">&nbsp;</div>
        </TD>
        <TD class="propCrnr propMain">
            <img src="./images/freeparking.jpg" class="propCrnr">
        </TD>
    </TR>
</TABLE>
-->*/