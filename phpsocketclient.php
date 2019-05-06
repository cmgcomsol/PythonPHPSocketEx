<?php
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
    echo "<br/>socket_create() failed. reason: " . socket_strerror($socket) . "\n";
}else {
    echo "<br/>OK created socket.\n";
}

echo "<br/>Try to connect '$ip' Port '$port'...\n";
$result = socket_connect($socket, $ip, $port);
if ($result < 0) {
    echo "<br/>socket_connect() failed.\nReason: ($result) " . socket_strerror($result) . "\n";
}else {
    echo "<br/>Connect OK\n";
}

$in = "Testing\r\n";
$out = '';

if(!socket_write($socket, $in, strlen($in))) {
    echo "<br/>socket_write() failed. reason: " . socket_strerror($socket) . "\n";
    die("<br/>Nothing else to do.\n");
}else {
    echo "<br/>Send Message to Server Successfully!\n";
    echo "<br/>Send Information:<font color='red'>$in</font> <br>";
}

$out = socket_read($socket, 8192);
echo "<br/>Receive Server Return Message Successfully!\n";
echo "<br/>Received Message:",$out;



echo "<br/>Turn Off Socket...\n";
socket_close($socket);



$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket < 0) {
    echo "<br/>socket_create() failed. reason: " . socket_strerror($socket) . "\n";
}else {
    echo "<br/>OK created socket.\n";

}


$result = socket_connect($socket, $ip, $port);
if ($result < 0) {
    echo "<br/><br/>socket_connect() failed.\nReason: ($result) " . socket_strerror($result) . "\n";
}else {
    echo "<br/>Connect OK\n";
}


$in="NAME\r\n";
if(!socket_write($socket, $in, strlen($in))) {
    echo "<br/>socket_write() failed. reason: " . socket_strerror($socket) . "\n";
    die("<br/>Nothing else to do.\n");
}else {
    echo "<br/>Send Message to Server Successfully!\n";
    echo "<br/>Send Information:<font color='red'>$in</font> <br>";
}

$out = socket_read($socket, 8192);
echo "<br/>Receive Server Return Message Successfully!\n";
echo "<br/>Received Message:",$out;


echo "<br/>Turn Off Socket...\n";
socket_close($socket);
echo "<br/>Turn Off OK\n";
?>
