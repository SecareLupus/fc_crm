<?php
// config.inc
// contains specific info for either ops or fc files

// FC VERSION

// takes a database name and a SQL query, returns raw SQL object
function open_stream()
{
  $dbname="databasename";
  $host="hostaddress";
  $user="username";
  $pwd="password";

  $access = new mysqli($host, $user, $pwd, $dbname);

  if($err = mysqli_connect_errno())
     die ("Couldn't connect to server." . $err);
  else
     return $access;
}

$cxn = open_stream();

$topinfo = "</HEAD>
<BODY LINK='333366' ALINK='333366' VLINK='333366'>
<TABLE>
	<TR>
		<TD WIDTH=150 rowspan=3 VALIGN=TOP>
			<IMG SRC='./data/logo.png' ALT='MRRU Logo' width='150'>
			<BR>";

?>
