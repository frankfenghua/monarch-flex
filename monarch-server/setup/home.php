<?php

// title:  home.php
// author: Ryan Lin
// date:   10/27/08
// about:  Introduces the visitor to what our program is all about. Allows 
//         them to sign up for an account.  An account is necessary in case the
//         user wants to edit his community's settings and whatnot. Allows user
//         to sign in if he already has an account.
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

?>

<h1>Welcome</h1>

<div class="half">
	<div class="in">
		<h3>intro</h3>
		Do you want statistics about your forum, blog, or news site?
		If yes, then join our community analysis program. 
		It can make you money and learn about human behavior.
	</div>
</div>
<div class="half">
	<div class="in">
		<h3>login</h3>
		<form method="post" action="?section=login">
			<b>username:</b>
			<input type="text" name="loginUsername" />
			<b>password:</b>
			<input type="password" name="loginPassword" />
			<br />
			<div class="formButton">
				<input type="submit" value="login" />
			</div>
		</form>
		<h3>register</h3>
		<form method="post" action="?section=register">
			<b>username:</b>
			<input type="text" name="username" />
			<b>real full name:</b>
			<input type="text" name="realName" />
			<b>password:</b>
			<input type="password" name="password" />
			<b>password again:</b>
			<input type="password" name="passwordAgain" />
			<br />
			<div class="formButton">
				<input type="submit" value="register" />
			</div>
		</form>
	</div>
</div>