		<tr class="hover">
			<td class="conf_td_label"><font color="red">*</font>安卓最新版本号:</td>
			<td class="conf_td_input"><?php echo $form->field($model, 'android_newest_version')->textInput(array("disabled"=>"true")); ?></td>
			<td class="vtop tips2"></td>
		</tr>
		<tr class="hover">
			<td class="conf_td_label"><font color="red">*</font>ios最新版本号:</td>
			<td class="conf_td_input"><?php echo $form->field($model, 'ios_newest_version')->textInput(array("disabled"=>"true")); ?></td>
			<td class="vtop tips2"></td>
		</tr>
		<tr class="hover">
			<td class="conf_td_label"><font color="red">*</font>安卓版下载地址:</td>
			<td class="conf_td_input"><?php echo $form->field($model, 'android_download_url')->textInput(array("style"=>"width:300px;","disabled"=>"true")); ?></td>
			<td class="vtop tips2"></td>
		</tr>
		<tr class="hover">
			<td class="conf_td_label"><font color="red">*</font>ios版下载地址:</td>
			<td class="conf_td_input"><?php echo $form->field($model, 'ios_download_url')->textInput(array("style"=>"width:300px;","disabled"=>"true")); ?></td>
			<td class="vtop tips2"></td>
		</tr>