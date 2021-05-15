<?php Style::begin(Lang::T("THEME_ADD")); ?>
        <form action='<?php echo URLROOT; ?>/admintheme/add' method='post'>
		<input type='hidden' name='action' value='style' />
        <input type='hidden' name='do' value='add' />
        <table align='center' width='400' cellspacing='0' class='table_table'>
		<tr>
		<td class='table_col1'><?php echo Lang::T("THEME_NAME_OF_NEW") ?>:</td>
		<td class='table_col2' align='right'><input type='text' name='name' size='30' maxlength='30' value='<?php echo $name; ?>' /></td>
		</tr>
		<tr>
		<td class='table_col1'><?php echo Lang::T("THEME_FOLDER_NAME_CASE_SENSITIVE") ?>:</td>
		<td class='table_col2' align='right'><input type='text' name='uri' size='30' maxlength='30' value='<?php echo $uri; ?>' /></td>
		</tr>
		<tr>
		<td colspan='2' align='center' class='table_head'>
		<input type='submit' value='Add new theme' />
		<input type='reset' value='<?php echo Lang::T("RESET") ?>' />
		</td>
		</tr>
		</table>
        </form>
		<br />
		<center>You must upload at least 5 files, to add a theme other files can be added for example themenavbar.php<br>
		        You may add a new navbar or just leave the link in header pointing to the original.<br>
				Needed files.<br>
				In app/views/inc/themename? - you need header.php and footer.php<br>
				In public_html/assets/themename? - you need custom.css and the two bootstrap files<br>
		</center>
<?php Style::end();