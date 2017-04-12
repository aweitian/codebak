<?PHP
$c = ' >|taw [] taw ad';
preg_match('/^>\|([^\s]+)/',trim($c),$m);
var_dump($m);
echo substr(trim($c), strlen($m[1])+3);
