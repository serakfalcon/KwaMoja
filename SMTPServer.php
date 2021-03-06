<?php

include('includes/session.inc');

$Title = _('SMTP Server details');

include('includes/header.inc');

echo '<p class="page_title_text noPrint" ><img src="' . $RootPath . '/css/' . $Theme . '/images/email.gif" title="' . _('SMTP Server') . '" alt="" />' . ' ' . _('SMTP Server Settings') . '</p>';
// First check if there are smtp server data or not


if ((isset($_POST['submit']) or isset($_POST['reload'])) and $_POST['MailServerSetting'] == 1) { //if there are already data setup, Update the table
	$sql = "UPDATE emailsettings SET
				host='" . $_POST['Host'] . "',
				port='" . $_POST['Port'] . "',
				heloaddress='" . $_POST['HeloAddress'] . "',
				username='" . $_POST['UserName'] . "',
				password='" . $_POST['Password'] . "',
				timeout='" . $_POST['Timeout'] . "',
				auth='" . $_POST['Auth'] . "'";
	$ErrMsg = _('The email setting information failed to update');
	$DbgMsg = _('The SQL failed to update is ');
	$result1 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
	unset($_POST['MailServerSetting']);
	if (isset($_POST['submit'])) {
		prnMsg(_('The settings for the SMTP server have been successfully updated'), 'success');
	}
	echo '<br />';

} elseif ((isset($_POST['submit']) or isset($_POST['reload'])) and $_POST['MailServerSetting'] == 0) { //There is no data setup yet
	$sql = "INSERT INTO emailsettings(host,
		 				port,
						heloaddress,
						username,
						password,
						timeout,
						auth)
				VALUES (
					'" . $_POST['Host'] . "',
					'" . $_POST['Port'] . "',
					'" . $_POST['HeloAddress'] . "',
					'" . $_POST['UserName'] . "',
					'" . $_POST['Password'] . "',
					'" . $_POST['Timeout'] . "',
					'" . $_POST['Auth'] . "')";
	$ErrMsg = _('The email settings failed to be inserted');
	$DbgMsg = _('The SQL failed to insert the email information is');
	$result2 = DB_query($sql, $db);
	unset($_POST['MailServerSetting']);
	if (isset($_POST['submit'])) {
		prnMsg(_('The settings for the SMTP server have been sucessfully inserted'), 'success');
	}
	echo '<br/>';
}

// Check the mail server setting status

$sql = "SELECT id,
				host,
				port,
				heloaddress,
				username,
				password,
				timeout,
				auth
			FROM emailsettings";
$ErrMsg = _('The email settings information cannot be retrieved');
$DbgMsg = _('The SQL that failed was');

$result = DB_query($sql, $db, $ErrMsg, $DbgMsg);
if (DB_num_rows($result) != 0) {
	$MailServerSetting = 1;
	$myrow = DB_fetch_array($result);
} else {
	DB_free_result($result);
	$MailServerSetting = 0;
	$myrow['host'] = '';
	$myrow['port'] = '';
	$myrow['heloaddress'] = '';
	$myrow['username'] = '';
	$myrow['password'] = '';
	$myrow['timeout'] = 5;
	$myrow['auth'] = 1;
}


echo '<form onSubmit="return VerifyForm(this);" method="post" class="noPrint" action="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '">';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
echo '<input type="hidden" name="MailServerSetting" value="' . $MailServerSetting . '" />';
echo '<table class="selection">';
echo '<tr>
		<td>' . _('Server Host Name') . '</td>
		<td><input type="text" name="Host" required="required" minlength="1" maxlength="50" value="' . $myrow['host'] . '" /></td>
	</tr>
	<tr>
		<td>' . _('SMTP port') . '</td>
		<td><input type="text" name="Port" required="required" minlength="1" maxlength="4" size="4" class="number" value="' . $myrow['port'] . '" /></td>
	</tr>
	<tr>
		<td>' . _('Helo Command') . '</td>
		<td><input type="text" name="HeloAddress" required="required" minlength="1" maxlength="10" value="' . $myrow['heloaddress'] . '" /></td>
	</tr>
	<tr>
		<td>' . _('Authorisation Required') . '</td>
		<td>
			<select required="required" minlength="1" name="Auth"  onchange="ReloadForm(reload);">';
if ($myrow['auth'] == 1) {
	echo '<option selected="selected" value="1">' . _('True') . '</option>';
	echo '<option value="0">' . _('False') . '</option>';
} else {
	echo '<option value="1">' . _('True') . '</option>';
	echo '<option selected="selected" value="0">' . _('False') . '</option>';
}
echo '</select>
		</td>
	</tr>';
if ($myrow['auth'] == 1) {
	echo '<tr>
			<td>' . _('User Name') . '</td>
			<td><input type="text" name="UserName" required="required" minlength="1" maxlength="50" value="' . $myrow['username'] . '" /></td>
		</tr>
		<tr>
			<td>' . _('Password') . '</td>
			<td><input type="password" name="Password" required="required" minlength="1" maxlength="50" value="' . $myrow['password'] . '" /></td>
		</tr>';
} else {
	echo '<input type="hidden" name="UserName" value="' . $myrow['username'] . '" />
		<input type="hidden" name="Password" value="' . $myrow['password'] . '" />';
}
echo '<tr>
		<td>' . _('Timeout (seconds)') . '</td>
		<td><input type="text" size="5" name="Timeout" required="required" minlength="1" maxlength="4" class="integer" value="' . $myrow['timeout'] . '" /></td>
	</tr>
	<tr>
		<td colspan="2"><div class="centre"><input type="submit" name="submit" value="' . _('Update') . '" /></div></td>
	</tr>';
echo '<input type="submit" name="reload" value="Reload" hidden="hidden" />';
echo '</table>
	  </form>';

include('includes/footer.inc');

?>