<div class="wrap">
	<div id='message' class='updated'>Le fichier doit comporter<strong>3 colonnes: username, password email first_name last_name</strong>.</div>
	<div style="clear:both; width:100%;">

	<h2>Import Utilisateurs</h2>


	<form method="POST" enctype="multipart/form-data" action="" accept-charset="utf-8" onsubmit="return check();">
	<table class="form-table" style="width:50%">
		<tbody>
		<tr class="form-field">
			<th scope="row"><label for="role">Role</label></th>
			<td>
			<!--select name="role" id="role">
				<?php /*
					$list_roles = VF2_Import_Users::get_editable_roles(); 
					foreach ($list_roles as $key => $value) {
						if($key == "subscriber")
							echo "<option selected='selected' value='$key'>$value</option>";
						else
							echo "<option value='$key'>$value</option>";
					}
					print_r($list_roles);
			*/	?>
			</select-->
			<input name="role" type="hidden" value="subscriber" />
			<select name="group" id="group">
				<?php 
					$list_groups = VF_Tools::get_groups(); 
					foreach ($list_groups as $key => $value) {
					//	if($key == "subscriber")
					//		echo "<option selected='selected' value='$key'>$value</option>";
					//	else
							echo "<option value='$key'>$value</option>";
					}
				?>
			</select>
			</td>
		</tr>
		<tr class="form-field form-required">
			<th scope="row"><label for="user_login">CSV file <span class="description">(required)</span></label></th>
			<td><input type="file" name="uploadfiles[]" id="uploadfiles" size="35" class="uploadfiles" /></td>
		</tr>
		</tbody>
	</table>
	<input class="button-primary" type="submit" name="uploadfile" id="uploadfile_btn" value="Lancer l'importation"/>
	</form>

</div>