<?php

$con = mysql_connect("localhost","s2_zizoo","s2_zizoo");
if (!$con){
  die('Could not connect: ' . mysql_error());
}

mysql_select_db("s2_zizoo", $con);

$result = mysql_query('SELECT * FROM country;');
while($row = mysql_fetch_array($result)){
    echo '$country'.$row['iso'].' = new Country();' . "\n";
	echo '$country'.$row['iso'].'->setIso("'.$row['iso'].'");' . "\n";
	if ($row['iso3']){
		echo '$country'.$row['iso'].'->setIso3("'.$row['iso3'].'");' . "\n";
	}
	echo '$country'.$row['iso'].'->setName("'.$row['name'].'");' . "\n";
	if ($row['numcode']){
		echo '$country'.$row['iso'].'->setNumcode("'.$row['numcode'].'");' . "\n";
	}
	echo '$country'.$row['iso'].'->setPrintableName("'.$row['printable_name'].'");' . "\n";
	echo '$manager->persist($country'.$row['iso'].');' . "\n\n";
}

$result = mysql_query('SELECT * FROM country;');
while($row = mysql_fetch_array($result)){
	echo '$this->addReference("country'.$row['iso'].'", $country'.$row['iso'].');' . "\n";
}

mysql_close($con);

?>
