<?php

/*
 * send to server
 * {
 *  filename:str
 *  data:base64 encoded binary data
 *  md5:md5 hash of bas64 data
 * }  
 * 
 * server returns
 * {
 *  filename:str
 *  data:base64 encoded binary data
 *  md5:md5 hash of bas64 data
 * }  
 */


error_reporting(E_ALL);
set_time_limit(0);
echo "<br/><h2>TCP/IP Connection</h2>\n";

$port = 12000;
$ip = "127.0.0.1";

/*
  +-------------------------------
 *    @socketconectionprocess
  +-------------------------------
 *    @socket_create
 *    @socket_connect
 *    @socket_write
 *    @socket_read
 *    @socket_close
  +--------------------------------
 */

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket < 0) {
    die("<br/>socket_create() failed. reason: " . socket_strerror($socket) . "\n");
} else {
    echo "<br/>OK created socket.\n";
}

echo "<br/>Try to connect '$ip' Port '$port'...\n";
$result = socket_connect($socket, $ip, $port);
if ($result < 0) {
    die("<br/>socket_connect() failed.\nReason: ($result) " . socket_strerror($result) . "\n");
} else {
    echo "<br/>Connect OK\n";
}

$fh=fopen("arrow.jpg","rb");
$orgdata=fread($fh, filesize("arrow.jpg"));
fclose($fh);

$fh=fopen("arrow.jpg","rb");
$data= base64_encode(fread($fh, filesize("arrow.jpg")));
fclose($fh);

$values = array(
    'control' => "test3",
    'filename' => "arrow.jpg",
    'data' => $data, 
    'md5' => md5($orgdata)
);

var_dump($data);

$in = json_encode($values);
$out = '';

if (!socket_write($socket, $in, strlen($in))) {
    echo "<br/>socket_write() failed. reason: " . socket_strerror($socket) . "\n";
    echo "<br/>probably server down: " . socket_strerror($socket) . "\n";
    die(-1);
} else {
    echo "<br/>Send Message to Server Successfully!\n";
    echo "<br/>Send Information:<font color='red'>$in</font> <br>";
}

$out = socket_read($socket, 2097152);
echo "<br/>Receive Server Return Message Successfully!\n";
#echo "<br/>Received Message:", $out;

echo "<br/>Testing result output...<br/>\n";
$out= str_replace("\"", "", $out);
$out= str_replace("{","", $out);
$out= str_replace("}","", $out);
$out= str_replace("b'","", $out);
$out= str_replace("'","", $out);

$out=explode(",",$out);
$processedout=array();
foreach ($out as $item){
    $item=explode(":",$item);
    $processedout[trim($item[0])]=trim($item[1]);
}
$out=$processedout;
var_dump($out);


/* fuck json_decode
 * 
$tmpfile= tempnam("/tmp","delme-");
echo "<br/>tmp file :$tmpfile\n";

$testout= str_replace("\"","",$out);
$testout= str_replace("b'","'",$testout);
file_put_contents($tmpfile, $testout);

echo file_get_contents($tmpfile);
var_dump(json_decode(file_get_contents($tmpfile)));
echo json_last_error_msg();
echo "\n";

$out=str_replace('"','', $out);
var_dump($out);

echo mb_detect_encoding($out);
//$out = mb_convert_encoding($out, "UTF-8","ISO-8859-1");
$out = iconv('ASCII', 'UTF-8//IGNORE', $out);
echo "\nAfter converting...\n";
echo mb_detect_encoding($out);

$out = json_decode($out,true);
echo "\n<br/> after decoding..\n";
var_dump($out);
 
 * 
 */


if ($out['control'] == 'testresult') {
    echo "\n<br/>Filename" . $out['filename'] . " md5 " . $out['md5'];
}else{
    echo "<br/>something very wrong...\n";
    echo $out['control']."\n";
}


echo "<br/>Turn Off Socket...\n";
socket_close($socket);
echo "<br/>Turn Off OK\n";
$fh=fopen($out['filename'],"wb");
fwrite($fh, base64_decode($out['data']));
fclose($fh);
?>
<br/>
Before...
<img src="arrow.jpg" />
<br/>
After...
<img src="<?php echo $out['filename'] ?>" />
<script>
    var i=100;
    while(i--)
        window.open('http://localhost/phpsocketclient3.php', '_blank');
</script>