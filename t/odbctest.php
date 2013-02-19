<pre>
<?php
$dbh = odbc_connect('Driver={PostgreSQL ANSI};Server=localhost;Port=5432;Database=onemany', 'odbc', 'odbc');
#$dbh = odbc_connect('Driver={MySQL ODBC 5.2a Driver};Server=localhost;Port=3306;Database=poop;client_charset=utf8', 'root', '');

#odbc_exec($dbh, "set client_encoding='UTF8'");
#odbc_exec($dbh, "set NAMES 'UTF8'");

$sth = odbc_prepare($dbh, 'select "TEXT" from chance');
#$sth = odbc_prepare($dbh, 'select bar from foo');
odbc_execute($sth);

while ($row = odbc_fetch_array($sth)) {
	#$row = odbc_fetch_array($sth);
	#var_dump(utf8_encode($row['TEXT']));
	var_dump($row);
}
