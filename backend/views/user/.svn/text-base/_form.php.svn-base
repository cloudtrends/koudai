<?php
use common\models\User;
use backend\components\widgets\ActiveForm;
?>
<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl ?>/js/My97DatePicker/WdatePicker.js"></script>

<?php $form = ActiveForm::begin(['id' => 'user-form']); ?>
    <table class="tb tb2">
       <tr><td class="td27" colspan="2">用户名:</td></tr>
       <tr class="noborder">
           <td class="vtop rowform"><?php echo $form->field($model, 'username')->textInput(); ?></td>
           <td class="vtop tips2"></td>
       </tr>
       <tr><td class="td27" colspan="2">手机号:</td></tr>
       <tr class="noborder">
           <td class="vtop rowform"><?php echo $form->field($model, 'phone')->textInput(['maxlength' => '11']); ?></td>
           <td class="vtop tips2"></td>
       </tr>
       <tr><td class="td27" colspan="2">真实姓名:</td></tr>
       <tr class="noborder">
           <td class="vtop rowform"><?php echo $form->field($model, 'realname')->textInput(); ?></td>
           <td class="vtop tips2"></td>
       </tr>
       <tr><td class="td27" colspan="2">身份证:</td></tr>
       <tr class="noborder">
           <td class="vtop rowform"><?php echo $form->field($model, 'id_card')->textInput(['maxlength' => '18']) ?></td>
           <td class="vtop tips2"></td>
       </tr>
       <tr><td class="td27" colspan="2">性别:</td></tr>
       <tr class="noborder">
           <td class="vtop rowform"><?php echo $form->field($model, 'sex')->radioList(User::$sexes); ?></td>
           <td class="vtop tips2"></td>
       </tr>
       <tr><td class="td27" colspan="2">生日:</td></tr>
       <tr class="noborder">
           <td class="vtop rowform"><?php echo  $form->field($model, 'birthday')->textInput(['onFocus' => "WdatePicker()"]);; ?></td>
           <td class="vtop tips2"></td>
       </tr>
       <tr><td class="td27" colspan="2">实名认证状态:</td></tr>
       <tr class="noborder">
           <td class="vtop rowform"><?php echo $form->field($model, 'real_verify_status')->radioList(['1' => '是', '0' => '否']); ?></td>
           <td class="vtop tips2"></td>
       </tr>
       <tr><td class="td27" colspan="2">绑定银行卡状态:</td></tr>
       <tr class="noborder">
           <td class="vtop rowform"><?php echo $form->field($model, 'card_bind_status')->radioList(['1' => '是', '0' => '否']); ?></td>
           <td class="vtop tips2"></td>
       </tr>
       <tr><td class="td27" colspan="2">是否新手:</td></tr>
       <tr class="noborder">
           <td class="vtop rowform"><?php echo $form->field($model, 'is_novice')->radioList(['1' => '是', '0' => '否']); ?></td>
           <td class="vtop tips2"></td>
       </tr>
       <tr>
           <td colspan="15">
       <input type="submit" value="提交" name="submit_btn" class="btn">
           </td>
       </tr>
    </table>
<?php ActiveForm::end(); ?>
