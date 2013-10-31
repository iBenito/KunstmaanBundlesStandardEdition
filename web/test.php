<?php

$matches = array();

$p = 'E(30):50';
$p = 'S:100;E(33):50';

$datePartPattern    = '(S|E)(\((-?\d+)\))*';
$amountPartPattern  = '(\d+)';

$instalmentPattern  = "$datePartPattern:$amountPartPattern";

echo preg_match_all("/$instalmentPattern+/", $p, $matches);

var_dump($matches);

?>