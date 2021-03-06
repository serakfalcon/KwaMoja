<?php

/*Script to change a supplier code wherever it appears*/

include('includes/session.inc');
$Title = _('UTILITY PAGE To Changes A Supplier Code In All Tables');
include('includes/header.inc');

if (isset($_POST['ProcessSupplierChange']))
	ProcessSupplier($_POST['OldSupplierNo'], $_POST['NewSupplierNo']);

echo '<form onSubmit="return VerifyForm(this);" action="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '" method="post" class="noPrint">';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

echo '<div class="centre">
		<table>
			<tr>
				<td>' . _('Existing Supplier Code') . ':</td>
				<td><input type="text" name="OldSupplierNo" size="20" minlength="0" maxlength="20" /></td>
			</tr>
			<tr>
				<td> ' . _('New Supplier Code') . ':</td>
				<td><input type="text" name="NewSupplierNo" size="20" minlength="0" maxlength="20" /></td>
			</tr>
		</table>
	<button type="submit" name="ProcessSupplierChange">' . _('Process') . '</button>
	<div>
	</form>';

include('includes/footer.inc');
exit();


function ProcessSupplier($oldCode, $newCode) {
	global $db;
	$table_key = array(
		'grns' => 'supplierid',
		'offers' => 'supplierid',
		'purchdata' => 'supplierno',
		'purchorders' => 'supplierno',
		'shipments' => 'supplierid',
		'suppliercontacts' => 'supplierid',
		'supptrans' => 'supplierno',
		'www_users' => 'supplierid'
	);

	// First check the Supplier code exists
	if (!checkSupplierExist($oldCode)) {
		prnMsg('<br /><br />' . _('The Supplier code') . ': ' . $oldCode . ' ' . _('does not currently exist as a supplier code in the system'), 'error');
		return;
	}
	$newCode = trim($newCode);
	if (checkNewCode($newCode)) {
		// Now check that the new code doesn't already exist
		if (checkSupplierExist($newCode)) {
			prnMsg(_('The replacement supplier code') . ': ' . $newCode . ' ' . _('already exists as a supplier code in the system') . ' - ' . _('a unique supplier code must be entered for the new code'), 'error');
			return;
		}
	} else {
		return;
	}

	$result = DB_Txn_Begin($db);

	prnMsg(_('Inserting the new supplier record'), 'info');
	$sql = "INSERT INTO suppliers (`supplierid`,
		`suppname`,  `address1`, `address2`, `address3`,
		`address4`,  `address5`,  `address6`, `supptype`, `lat`, `lng`,
		`currcode`,  `suppliersince`, `paymentterms`, `lastpaid`,
		`lastpaiddate`, `bankact`, `bankref`, `bankpartics`,
		`remittance`, `taxgroupid`, `factorcompanyid`, `taxref`,
		`phn`, `port`, `email`, `fax`, `telephone`)
	SELECT '" . $newCode . "',
		`suppname`,  `address1`, `address2`, `address3`,
		`address4`,  `address5`,  `address6`, `supptype`, `lat`, `lng`,
		`currcode`,  `suppliersince`, `paymentterms`, `lastpaid`,
		`lastpaiddate`, `bankact`, `bankref`, `bankpartics`,
		`remittance`, `taxgroupid`, `factorcompanyid`, `taxref`,
		`phn`, `port`, `email`, `fax`, `telephone`
		FROM suppliers WHERE supplierid='" . $oldCode . "'";

	$DbgMsg = _('The SQL that failed was');
	$ErrMsg = _('The SQL to insert the new suppliers master record failed') . ', ' . _('the SQL statement was');
	$result = DB_query($sql, $db, $ErrMsg, $DbgMsg, true);

	foreach ($table_key as $table => $key) {
		prnMsg(_('Changing') . ' ' . $table . ' ' . _('records'), 'info');
		$sql = "UPDATE " . $table . " SET $key='" . $newCode . "' WHERE $key='" . $oldCode . "'";
		$ErrMsg = _('The SQL to update') . ' ' . $table . ' ' . _('records failed');
		$result = DB_query($sql, $db, $ErrMsg, $DbgMsg, true);
	}

	prnMsg(_('Deleting the supplier code from the suppliers master table'), 'info');
	$sql = "DELETE FROM suppliers WHERE supplierid='" . $oldCode . "'";

	$ErrMsg = _('The SQL to delete the old supplier record failed');
	$result = DB_query($sql, $db, $ErrMsg, $DbgMsg, true);

	$result = DB_Txn_Commit($db);
}

function checkSupplierExist($codeSupplier) {
	global $db;
	$result = DB_query("SELECT supplierid FROM suppliers WHERE supplierid='" . $codeSupplier . "'", $db);
	if (DB_num_rows($result) == 0) {
		return false;
	}
	return true;
}

function checkNewCode($code) {
	$tmp = str_replace(' ', '', $code);
	if ($tmp != $code) {
		prnMsg('<br /><br />' . _('The New supplier code') . ': ' . $code . ' ' . _('must be not empty nor with spaces'), 'error');
		return false;
	}
	return true;
}
?>