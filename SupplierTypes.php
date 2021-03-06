<?php

include('includes/session.inc');
$Title = _('Supplier Types') . ' / ' . _('Maintenance');
include('includes/header.inc');

if (isset($_POST['SelectedType'])) {
	$SelectedType = mb_strtoupper($_POST['SelectedType']);
} elseif (isset($_GET['SelectedType'])) {
	$SelectedType = mb_strtoupper($_GET['SelectedType']);
}

if (isset($Errors)) {
	unset($Errors);
}

$Errors = array();

echo '<p class="page_title_text noPrint" ><img src="' . $RootPath . '/css/' . $Theme . '/images/maintenance.png" title="' . _('Supplier Types') . '" alt="" />' . _('Supplier Type Setup') . '</p>';
echo '<div class="page_help_text noPrint">' . _('Add/edit/delete Supplier Types') . '</div><br />';

if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;

	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */

	//first off validate inputs sensible
	$i = 1;
	if (mb_strlen($_POST['TypeName']) > 100) {
		$InputError = 1;
		echo prnMsg(_('The supplier type name description must be 100 characters or less long'), 'error');
		$Errors[$i] = 'SupplierType';
		$i++;
	}

	if (mb_strlen(trim($_POST['TypeName'])) == 0) {
		$InputError = 1;
		echo prnMsg(_('The supplier type name description must contain at least one character'), 'error');
		$Errors[$i] = 'SupplierType';
		$i++;
	}

	$checksql = "SELECT count(*)
			 FROM suppliertype
			 WHERE typename = '" . $_POST['TypeName'] . "'";
	$checkresult = DB_query($checksql, $db);
	$checkrow = DB_fetch_row($checkresult);
	if ($checkrow[0] > 0) {
		$InputError = 1;
		echo prnMsg(_('You already have a supplier type called') . ' ' . $_POST['TypeName'], 'error');
		$Errors[$i] = 'SupplierName';
		$i++;
	}

	if (isset($SelectedType) and $InputError != 1) {

		$sql = "UPDATE suppliertype
			SET typename = '" . $_POST['TypeName'] . "'
			WHERE typeid = '" . $SelectedType . "'";

		$msg = _('The supplier type') . ' ' . $SelectedType . ' ' . _('has been updated');
	} elseif ($InputError != 1) {

		// First check the type is not being duplicated

		$checkSql = "SELECT count(*)
				 FROM suppliertype
				 WHERE typeid = '" . $_POST['TypeID'] . "'";

		$checkresult = DB_query($checkSql, $db);
		$checkrow = DB_fetch_row($checkresult);

		if ($checkrow[0] > 0) {
			$InputError = 1;
			prnMsg(_('The supplier type ') . $_POST['TypeID'] . _(' already exist.'), 'error');
		} else {

			// Add new record on submit

			$sql = "INSERT INTO suppliertype
						(typename)
					VALUES ('" . $_POST['TypeName'] . "')";


			$msg = _('Supplier type') . ' ' . $_POST['TypeName'] . ' ' . _('has been created');
			$checkSql = "SELECT count(typeid)
				 FROM suppliertype";
			$result = DB_query($checkSql, $db);
			$row = DB_fetch_row($result);

		}
	}

	if ($InputError != 1) {
		//run the SQL from either of the above possibilites
		$result = DB_query($sql, $db);


		// Fetch the default price list.
		$sql = "SELECT confvalue
					FROM config
					WHERE confname='DefaultSupplierType'";
		$result = DB_query($sql, $db);
		$SupplierTypeRow = DB_fetch_row($result);
		$DefaultSupplierType = $SupplierTypeRow[0];

		// Does it exist
		$checkSql = "SELECT count(*)
				 FROM suppliertype
				 WHERE typeid = '" . $DefaultSupplierType . "'";
		$checkresult = DB_query($checkSql, $db);
		$checkrow = DB_fetch_row($checkresult);

		// If it doesnt then update config with newly created one.
		if ($checkrow[0] == 0) {
			$sql = "UPDATE config
					SET confvalue='" . $_POST['TypeID'] . "'
					WHERE confname='DefaultSupplierType'";
			$result = DB_query($sql, $db);
			$_SESSION['DefaultSupplierType'] = $_POST['TypeID'];
		}

		prnMsg($msg, 'success');

		unset($SelectedType);
		unset($_POST['TypeID']);
		unset($_POST['TypeName']);
	}

} elseif (isset($_GET['delete'])) {

	$sql = "SELECT COUNT(*) FROM suppliers WHERE supptype='" . $SelectedType . "'";

	$ErrMsg = _('The number of suppliers using this Type record could not be retrieved because');
	$result = DB_query($sql, $db, $ErrMsg);
	$myrow = DB_fetch_row($result);
	if ($myrow[0] > 0) {
		prnMsg(_('Cannot delete this type because suppliers are currently set up to use this type') . '<br />' . _('There are') . ' ' . $myrow[0] . ' ' . _('suppliers with this type code'));
	} else {

		$sql = "DELETE FROM suppliertype WHERE typeid='" . $SelectedType . "'";
		$ErrMsg = _('The Type record could not be deleted because');
		$result = DB_query($sql, $db, $ErrMsg);
		prnMsg(_('Supplier type') . $SelectedType . ' ' . _('has been deleted'), 'success');

		unset($SelectedType);
		unset($_GET['delete']);

	}
}

if (!isset($SelectedType)) {

	/* It could still be the second time the page has been run and a record has been selected for modification - SelectedType will
	 *  exist because it was sent with the new call. If its the first time the page has been displayed with no parameters then
	 * none of the above are true and the list of sales types will be displayed with links to delete or edit each. These will call
	 * the same page again and allow update/input or deletion of the records
	 */

	$sql = "SELECT typeid, typename FROM suppliertype";
	$result = DB_query($sql, $db);

	echo '<table class="selection">';
	echo '<tr>
			<th class="SortableColumn">' . _('Type ID') . '</th>
			<th class="SortableColumn">' . _('Type Name') . '</th>
		</tr>';

	$k = 0; //row colour counter

	while ($myrow = DB_fetch_row($result)) {
		if ($k == 1) {
			echo '<tr class="EvenTableRows">';
			$k = 0;
		} else {
			echo '<tr class="OddTableRows">';
			$k = 1;
		}

		printf('<td>%s</td>
				<td>%s</td>
				<td><a href="%sSelectedType=%s">' . _('Edit') . '</a></td>
				<td><a href="%sSelectedType=%s&amp;delete=yes" onclick="return MakeConfirm(\'' . _('Are you sure you wish to delete this Supplier Type?') . '\', \'Confirm Delete\', this);">' . _('Delete') . '</a></td>
			</tr>', $myrow[0], $myrow[1], htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?', $myrow[0], htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?', $myrow[0]);
	}
	//END WHILE LIST LOOP
	echo '</table>';
}

//end of ifs and buts!
if (isset($SelectedType)) {

	echo '<div class="centre">
			<p><a href="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '">' . _('Show All Types Defined') . '</a></p>
		</div>';
}
if (!isset($_GET['delete'])) {

	echo '<form onSubmit="return VerifyForm(this);" method="post" class="noPrint" action="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '">';
	echo '<div>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo '<br />
		<table class="selection">'; //Main table

	// The user wish to EDIT an existing type
	if (isset($SelectedType) and $SelectedType != '') {

		$sql = "SELECT typeid,
				   typename
				FROM suppliertype
				WHERE typeid='" . $SelectedType . "'";

		$result = DB_query($sql, $db);
		$myrow = DB_fetch_array($result);

		$_POST['TypeID'] = $myrow['typeid'];
		$_POST['TypeName'] = $myrow['typename'];

		echo '<input type="hidden" name="SelectedType" value="' . $SelectedType . '" />';
		echo '<input type="hidden" name="TypeID" value="' . $_POST['TypeID'] . '" />';

		// We dont allow the user to change an existing type code

		echo '<tr>
				<td>' . _('Type ID') . ': </td>
				<td>' . $_POST['TypeID'] . '</td>
			</tr>';
	}

	if (!isset($_POST['TypeName'])) {
		$_POST['TypeName'] = '';
	}
	echo '<tr>
			<td>' . _('Type Name') . ':</td>
			<td><input type="text" autofocus="autofocus" required="required" minlength="1" maxlength="100" name="TypeName" value="' . $_POST['TypeName'] . '" /></td>
		</tr>';

	echo '<tr>
			<td colspan="2">
				<div class="centre">
					<input type="submit" name="submit" value="' . _('Accept') . '" />
				</div>
			</td>
		</tr>
		</table>
		</div>
		</form>';

} // end if user wish to delete

include('includes/footer.inc');
?>