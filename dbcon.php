<?php
// Enter your Host, username, password, database below.
$con = mysqli_connect("localhost","root","","paypal");
if(!$con){
	die("Connection Error !!".mysqli_connect_error());
}
 ?>