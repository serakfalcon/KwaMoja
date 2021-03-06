<?php

include('includes/session.inc');

$Title = _('Tax Groups');
include('includes/header.inc');

if (isset($_GET['SelectedGroup'])) {
	$SelectedGroup = $_GET['SelectedGroup'];
} elseif (isset($_POST['SelectedGroup'])) {
	$SelectedGroup = $_POST['SelectedGroup'];
}

echo '<p class="page_title_text noPrint" ><img src="' . $RootPath . '/css/' . $Theme . '/images/maintenance.png" title="' . _('Search') . '" alt="" />' . ' ' . $Title . '</p><br />';

if (isset($_POST['submit']) or isset($_GET['remove']) or isset($_GET['add'])) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;

	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */
	//first off validate inputs sensible
	if (isset($_POST['GroupName']) and mb_strlen($_POST['GroupName']) < 4) {
		$InputError = 1;
		prnMsg(_('The Group description entered must be at least 4 characters long'), 'error');
	}

	// if $_POST['GroupName'] then it is a modification of a tax group name
	// else it is either an add or remove of taxgroup
	unset($sql);
	if (isset($_POST['GroupName'])) { // Update or Add a tax group
		if (isset($SelectedGroup)) { // Update a tax group
			$sql = "UPDATE taxgroups SET taxgroupdescription = '" . $_POST['GroupName'] . "'
					WHERE taxgroupid = '" . $SelectedGroup . "'";
			$ErrMsg = _('The update of the tax group description failed because');
			$result = DB_query($sql, $db, $ErrMsg);
			if ($result) {
				prnMsg(_('The tax group description was updated to') . ' ' . $_POST['GroupName'], 'success');
			}
			unset($SelectedGroup);
		} else { // Add new tax group

			$GroupResult = DB_query("SELECT taxgroupid
								FROM taxgroups
								WHERE taxgroupdescription='" . $_POST['GroupName'] . "'", $db);
			if (DB_num_rows($GroupResult) == 1) {
				prnMsg(_('A new tax group could not be added because a tax group already exists for') . ' ' . $_POST['GroupName'], 'warn');
				unset($sql);
			} else {
				$sql = "INSERT INTO taxgroups (taxgroupdescription)
						VALUES ('" . $_POST['GroupName'] . "')";
				$ErrMsg = _('The addition of the group failed because');
				$result = DB_query($sql, $db, $ErrMsg);
				if ($result) {
					prnMsg(_('Added the new tax group') . ' ' . $_POST['GroupName'], 'success');
				}
				$GroupResult = DB_query("SELECT taxgroupid
									FROM taxgroups
									WHERE taxgroupdescription='" . $_POST['GroupName'] . "'", $db);
				$GroupRow = DB_fetch_array($GroupResult);
				$SelectedGroup = $GroupRow['taxgroupid'];
			}
		}
		unset($_POST['GroupName']);
	} elseif (isset($SelectedGroup) and isset($_GET['TaxAuthority'])) {
		$TaxAuthority = $_GET['TaxAuthority'];
		if (isset($_GET['add'])) { // adding a tax authority to a tax group
			$sql = "INSERT INTO taxgrouptaxes ( taxgroupid,
												taxauthid,
												calculationorder)
					VALUES ('" . $SelectedGroup . "',
							'" . $TaxAuthority . "',
							0)";

			$ErrMsg = _('The addition of the tax failed because');
			$result = DB_query($sql, $db, $ErrMsg);
			if ($result) {
				prnMsg(_('The tax was added.'), 'success');
			}
		} elseif (isset($_GET['remove'])) { // remove a taxauthority from a tax group
			$sql = "DELETE FROM taxgrouptaxes
					WHERE taxgroupid = '" . $SelectedGroup . "'
					AND taxauthid = '" . $TaxAuthority . "'";
			$ErrMsg = _('The removal of this tax failed because');
			$result = DB_query($sql, $db, $ErrMsg);
			if ($result) {
				prnMsg(_('This tax was removed.'), 'success');
			}
		}
		unset($_GET['add']);
		unset($_GET['remove']);
		unset($_GET['TaxAuthority']);
	}
} elseif (isset($_POST['UpdateOrder'])) {
	//A calculation order update
	$sql = "SELECT taxauthid FROM taxgrouptaxes WHERE taxgroupid='" . $SelectedGroup . "'";
	$Result = DB_query($sql, $db, _('Could not get tax authorities in the selected tax group'));

	while ($myrow = DB_fetch_row($Result)) {

		if (is_numeric($_POST['CalcOrder_' . $myrow[0]]) and $_POST['CalcOrder_' . $myrow[0]] < 5) {

			$sql = "UPDATE taxgrouptaxes
				SET calculationorder='" . $_POST['CalcOrder_' . $myrow[0]] . "',
					taxontax='" . $_POST['TaxOnTax_' . $myrow[0]] . "'
				WHERE taxgroupid='" . $SelectedGroup . "'
				AND taxauthid='" . $myrow[0] . "'";

			$result = DB_query($sql, $db);
		}
	}

	//need to do a reality check to ensure that taxontax is relevant only for taxes after the first tax
	$sql = "SELECT taxauthid,
					taxontax
			FROM taxgrouptaxes
			WHERE taxgroupid='" . $SelectedGroup . "'
			ORDER BY calculationorder";

	$Result = DB_query($sql, $db, _('Could not get tax authorities in the selected tax group'));

	if (DB_num_rows($Result) > 0) {
		$myrow = DB_fetch_array($Result);
		if ($myrow['taxontax'] == 1) {
			prnMsg(_('It is inappropriate to set tax on tax where the tax is the first in the calculation order. The system has changed it back to no tax on tax for this tax authority'), 'warning');
			$Result = DB_query("UPDATE taxgrouptaxes SET taxontax=0
								WHERE taxgroupid='" . $SelectedGroup . "'
								AND taxauthid='" . $myrow['taxauthid'] . "'", $db);
		}
	}
} elseif (isset($_GET['Delete'])) {

	/* PREVENT DELETES IF DEPENDENT RECORDS IN 'custbranch, suppliers */

	$sql = "SELECT COUNT(*) FROM custbranch WHERE taxgroupid='" . $_GET['SelectedGroup'] . "'";
	$result = DB_query($sql, $db);
	$myrow = DB_fetch_row($result);
	if ($myrow[0] > 0) {
		prnMsg(_('Cannot delete this tax group because some customer branches are setup using it'), 'warn');
		echo '<br />' . _('There are') . ' ' . $myrow[0] . ' ' . _('customer branches referring to this tax group');
	} else {
		$sql = "SELECT COUNT(*) FROM suppliers
				WHERE taxgroupid='" . $_GET['SelectedGroup'] . "'";
		$result = DB_query($sql, $db);
		$myrow = DB_fetch_row($result);
		if ($myrow[0] > 0) {
			prnMsg(_('Cannot delete this tax group because some suppliers are setup using it'), 'warn');
			echo '<br />' . _('There are') . ' ' . $myrow[0] . ' ' . _('suppliers referring to this tax group');
		} else {

			$sql = "DELETE FROM taxgrouptaxes
					WHERE taxgroupid='" . $_GET['SelectedGroup'] . "'";
			$result = DB_query($sql, $db);
			$sql = "DELETE FROM taxgroups
					WHERE taxgroupid='" . $_GET['SelectedGroup'] . "'";
			$result = DB_query($sql, $db);
			prnMsg($_GET['GroupID'] . ' ' . _('tax group has been deleted') . '!', 'success');
		}
	} //end if taxgroup used in other tables
	unset($SelectedGroup);
	unset($_GET['GroupName']);
}

if (!isset($SelectedGroup)) {

	/* If its the first time the page has been displayed with no parameters then none of the above are true and the list of tax groups will be displayed with links to delete or edit each. These will call the same page again and allow update/input or deletion of tax group taxes*/

	$sql = "SELECT taxgroupid,
					taxgroupdescription
			FROM taxgroups";
	$result = DB_query($sql, $db);

	if (DB_num_rows($result) == 0) {
		echo '<div class="page_help_text">' . _('As this is the first time that the system has been used, you must first create a tax group.') .
				'<br />' . _('For help, click on the help icon in the top right') .
				'<br />' . _('Once you have filled in all the details, click on the button at the bottom of the screen') . '</div>';
	}

	if (DB_num_rows($result) == 0) {
		echo '<div class="centre">';
		prnMsg(_('There are no tax groups configured.'), 'info');
		echo '</div>';
	} else {
		echo '<table class="selection">
				<tr>
					<th class="SortableColumn">' . _('Group No') . '</th>
					<th class="SortableColumn">' . _('Tax Group') . '</th>
				</tr>';

		$k = 0; //row colour counter
		while ($myrow = DB_fetch_array($result)) {
			if ($k == 1) {
				echo '<tr class="EvenTableRows">';
				$k = 0;
			} else {
				echo '<tr class="OddTableRows">';
				$k = 1;
			}

			printf('<td>%s</td>
					<td>%s</td>
					<td><a href="%s&amp;SelectedGroup=%s">' . _('Edit') . '</a></td>
					<td><a href="%s&amp;SelectedGroup=%s&amp;Delete=1&amp;GroupID=%s" onclick="return MakeConfirm(\'' . _('Are you sure you wish to delete this tax group?') . '\', \'Confirm Delete\', this);">' . _('Delete') . '</a></td>
					</tr>', $myrow['taxgroupid'], $myrow['taxgroupdescription'], htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?', $myrow['taxgroupid'], htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?', $myrow['taxgroupid'], urlencode($myrow['taxgroupdescription']));

		} //END WHILE LIST LOOP
		echo '</table>';
	}
} //end of ifs and buts!


if (isset($SelectedGroup)) {
	echo '<div class="centre">
			<a href="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '">' . _('Review Existing Groups') . '</a>
		</div>';
}

if (isset($SelectedGroup)) {
	//editing an existing role

	$sql = "SELECT taxgroupid,
					taxgroupdescription
			FROM taxgroups
			WHERE taxgroupid='" . $SelectedGroup . "'";
	$result = DB_query($sql, $db);
	if (DB_num_rows($result) == 0) {
		prnMsg(_('The selected tax group is no longer available.'), 'warn');
	} else {
		$myrow = DB_fetch_array($result);
		$_POST['SelectedGroup'] = $myrow['taxgroupid'];
		$_POST['GroupName'] = $myrow['taxgroupdescription'];
	}
}
echo '<br />';
echo '<form onSubmit="return VerifyForm(this);" method="post" class="noPrint" action="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '">';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
if (isset($_POST['SelectedGroup'])) {
	echo '<input type="hidden" name="SelectedGroup" value="' . $_POST['SelectedGroup'] . '" />';
}
echo '<table class="selection">';

if (!isset($_POST['GroupName'])) {
	$_POST['GroupName'] = '';
}
echo '<tr>
		<td>' . _('Tax Group') . ':</td>
		<td><input type="text" name="GroupName" size="40" required="required" minlength="1" maxlength="40" value="' . $_POST['GroupName'] . '" /></td>';
echo '<td><input type="submit" name="submit" value="' . _('Enter Group') . '" /></td>
	</tr>
	</table>
	<br />
	</form>';


if (isset($SelectedGroup)) {

	$sql = "SELECT taxid,
			description as taxname
			FROM taxauthorities
			ORDER BY taxid";

	$sqlUsed = "SELECT taxauthid,
				description AS taxname,
				calculationorder,
				taxontax
			FROM taxgrouptaxes INNER JOIN taxauthorities
				ON taxgrouptaxes.taxauthid=taxauthorities.taxid
			WHERE taxgroupid='" . $SelectedGroup . "'
			ORDER BY calculationorder";

	$Result = DB_query($sql, $db);

	/*Make an array of the used tax authorities in calculation order */
	$UsedResult = DB_query($sqlUsed, $db);
	$TaxAuthsUsed = array(); //this array just holds the taxauthid of all authorities in the group
	$TaxAuthRow = array(); //this array holds all the details of the tax authorities in the group
	$i = 1;
	while ($myrow = DB_fetch_array($UsedResult)) {
		$TaxAuthsUsed[$i] = $myrow['taxauthid'];
		$TaxAuthRow[$i] = $myrow;
		$i++;
	}

	/* the order and tax on tax will only be an issue if more than one tax authority in the group */
	if (count($TaxAuthsUsed) > 0) {
		echo '<form onSubmit="return VerifyForm(this);" method="post" class="noPrint" action="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '">';
		echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />
				<input type="hidden" name="SelectedGroup" value="' . $SelectedGroup . '" />';
		echo '<table class="selection">
				<tr>
					<th colspan="3"><h3>' . _('Calculation Order') . '</h3></th>
				</tr>
				<tr>
					<th class="SortableColumn">' . _('Tax Authority') . '</th>
					<th>' . _('Order') . '</th>
					<th>' . _('Tax on Prior Taxes') . '</th>
				</tr>';
		$k = 0; //row colour counter
		for ($i = 1; $i < count($TaxAuthRow) + 1; $i++) {
			if ($k == 1) {
				echo '<tr class="OddTableRows">';
				$k = 0;
			} else {
				echo '<tr class="EvenTableRows">';
				$k = 1;
			}

			if ($TaxAuthRow[$i]['calculationorder'] == 0) {
				$TaxAuthRow[$i]['calculationorder'] = $i;
			}

			echo '<td>' . $TaxAuthRow[$i]['taxname'] . '</td>
				<td><input type="text" class="integer" name="CalcOrder_' . $TaxAuthRow[$i]['taxauthid'] . '" value="' . $TaxAuthRow[$i]['calculationorder'] . '" size="2" required="required" minlength="1" maxlength="2" style="width: 100%" /></td>
				<td><select required="required" minlength="1" name="TaxOnTax_' . $TaxAuthRow[$i]['taxauthid'] . '" style="width: 100%">';
			if ($TaxAuthRow[$i]['taxontax'] == 1) {
				echo '<option selected="selected" value="1">' . _('Yes') . '</option>';
				echo '<option value="0">' . _('No') . '</option>';
			} else {
				echo '<option value="1">' . _('Yes') . '</option>';
				echo '<option selected="selected" value="0">' . _('No') . '</option>';
			}
			echo '</select></td>
				</tr>';

		}
		echo '</table>';
		echo '<br />
			<div class="centre">
				<input type="submit" name="UpdateOrder" value="' . _('Update Order') . '" />
			</div>';
	}

	echo '</form>';

	if (DB_num_rows($UsedResult) == 0) {
		echo '<div class="page_help_text">' . _('As this is the first time that the system has been used, you must first create a tax group.') .
				'<br />' . _('For help, click on the help icon in the top right') .
				'<br />' . _('Once you have filled in all the details, click on the button at the bottom of the screen') . '</div>';
	} elseif (DB_num_rows($UsedResult) == 1 and isset($_SESSION['FirstStart'])) {
		echo '<meta http-equiv="refresh" content="0; url=' . $RootPath . '/TaxProvinces.php">';
		exit;
	}

	if (DB_num_rows($Result) > 0) {
		echo '<br />';
		echo '<table class="selection">
				<tr>
					<th colspan="4">' . _('Assigned Taxes') . '</th>
					<th></th>
					<th colspan="2">' . _('Available Taxes') . '</th>
				</tr>
				<tr>
					<th class="SortableColumn">' . _('Tax Auth ID') . '</th>
					<th class="SortableColumn">' . _('Tax Authority Name') . '</th>
					<th>' . _('Calculation Order') . '</th>
					<th>' . _('Tax on Prior Tax(es)') . '</th>
					<th></th>
					<th>' . _('Tax Auth ID') . '</th>
					<th>' . _('Tax Authority Name') . '</th>
				</tr>';

	} else {
		echo '<br />
				<div class="centre">' . _('There are no tax authorities defined to allocate to this tax group') . '
				</div>';
	}

	$k = 0; //row colour counter
	while ($AvailRow = DB_fetch_array($Result)) {

		if ($k == 1) {
			echo '<tr class="EvenTableRows">';
			$k = 0;
		} else {
			echo '<tr class="OddTableRows">';
			$k = 1;
		}
		$TaxAuthUsedPointer = array_search($AvailRow['taxid'], $TaxAuthsUsed);

		if ($TaxAuthUsedPointer) {

			if ($TaxAuthRow[$TaxAuthUsedPointer]['taxontax'] == 1) {
				$TaxOnTax = _('Yes');
			} else {
				$TaxOnTax = _('No');
			}

			printf('<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td><a href="%sSelectedGroup=%s&amp;remove=1&amp;TaxAuthority=%s" onclick="return MakeConfirm(\'' . _('Are you sure you wish to remove this tax authority from the group?') . '\', \'Confirm Delete\', this);">' . _('Remove') . '</a></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>', $AvailRow['taxid'], $AvailRow['taxname'], $TaxAuthRow[$TaxAuthUsedPointer]['calculationorder'], $TaxOnTax, htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?', $SelectedGroup, $AvailRow['taxid']);

		} else {
			printf('<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>%s</td>
				<td>%s</td>
				<td><a href="%sSelectedGroup=%s&amp;add=1&amp;TaxAuthority=%s">' . _('Add') . '</a></td>', $AvailRow['taxid'], $AvailRow['taxname'], htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?', $SelectedGroup, $AvailRow['taxid']);
		}
		echo '</tr>';
	}
	echo '</table>';

}

include('includes/footer.inc');

?>