<?php
/*
send name
send date of birth
send gender
{
  control:test,
  name:name,
  dob:dob

}

expected from server
{
  control:testresult
  name:salutation + name
  age:age
}
*/

if(isset($_GET['name'])){
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

      $values=array(
        'control'=>"test",
        'name'=> $_GET['name'],//"Geo C Mathew",
        'dob'=>$_GET['dob'],//"30 April 1983",
        'gender'=>$_GET['gender']//"male"
      );

      $in = json_encode($values);
      $out = '';

      if(!socket_write($socket, $in, strlen($in))) {
          echo "<br/>socket_write() failed. reason: " . socket_strerror($socket) . "\n";
          echo "<br/>probably server down: " . socket_strerror($socket) . "\n";
          die(-1);
      }else {
          echo "<br/>Send Message to Server Successfully!\n";
          echo "<br/>Send Information:<font color='red'>$in</font> <br>";
      }

      $out = socket_read($socket, 8192);
      echo "<br/>Receive Server Return Message Successfully!\n";
      echo "<br/>Received Message:",$out;

      echo "<br/Testing result output...<br/>\n";
      $out=json_decode($out,true);
      var_dump($out);
      if($out['control']=='testresult'){
        echo "<br/>".$out['name']." is ".$out['age']." years old.";
      }


      echo "<br/>Turn Off Socket...\n";
      socket_close($socket);
      echo "<br/>Turn Off OK\n";
}
?>
<br/>
<hr/>
<form>
  <input type="text" name="name" placeholder="Enter name" /> <br/>
  <input type="text" name="dob" placeholder="Date of birth in format dd mmm yyyy" /> <br/>
  <input type="text" name="gender" placeholder="male/female" /> <br/>
  <input type='submit' />
</form>
