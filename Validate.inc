<?php
// Validate.inc
// PHP Library for building and verifying safe strings.

//buildEmail(user, host)
//Builds an email from the input given. If the email is legal,
//returns the email address. Otherwise returns null.
function buildEmail($user, $host)
{
	$tmpEmail = $user . "@" . $host;
	if (isLegalEmail($tmpEmail))
	{
		return $tmpEmail;
	}
	return null;
}

//buildPhoneNum(string)
//Builds an phone number from the input given. Strips out all
//non-number characters, and builds a string from the remaining
//charactelrs. If the phone number is legal, returns the phone
//number. Otherwise returns null.
function buildPhoneNum($string)
{
	$numbers = preg_replace("/[^0-9]/", '', $string);
	$tmpPhone = "(" . substr($numbers, 0, 3) . ") " . substr($numbers, 3, 3) . "-" . substr($numbers, 6, 4);
	if (isLegalPhoneNum($tmpPhone))
	{
		return $tmpPhone;
	}
	return null;
}

//isLegalEmail(test)
//Checks to see if the provided string is a legal email, based
//on the filter_var definition of a valid email.
function isLegalEmail($test)
{
	if (filter_var($test, FILTER_VALIDATE_EMAIL))
	{
		return true;
	}
	return false;
}

//isLegalPhoneNum(test)
//Checks to see if the provided string is a legal phone number, based
//on the pattern "(ABC) DEF-GHIJ".
function isLegalPhoneNum($test)
{
	if(preg_match("/^\([0-9]{3}\) [0-9]{4}-[0-9]{4}$/", $test))
	{
		return true;
	}
	return false;
}

?>