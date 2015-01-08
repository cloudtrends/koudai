<?php
use common\models\Activity;
use backend\components\widgets\ActiveForm;
?>
<!-- 富文本编辑器 注：360浏览器无法显示编辑器时，尝试切换模式（如兼容模式）-->
<script type="text/javascript">
var UEDITOR_HOME_URL = '<?php echo $this->baseUrl; ?>/js/ueditor/'; //一定要用这句话，否则你需要去ueditor.config.js修改路径的配置信息 
</script>
<script type="text/javascript" src="<?php echo $this->baseUrl; ?>/js/ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="<?php echo $this->baseUrl; ?>/js/ueditor/ueditor.all.js"></script>
<script type="text/javascript">
var ue = UE.getEditor('activity-content');
</script>
<?php $form = ActiveForm::begin(['id' => 'activity-form', 'options' => ['enctype' => 'multipart/form-data']]); ?>
    <table class="tb tb2">
        <tr><td class="td27" colspan="2">标题:</td></tr>
        <tr class="noborder">
            <td class="vtop rowform"><?php echo $form->field($model, 'title')->textInput(); ?></td>
            <td class="vtop tips2"></td>
        </tr>
         <tr><td class="td27" colspan="2">摘要:</td></tr>
        <tr class="noborder">
            <td class="vtop rowform">
                <?php echo $form->field($model, 'abstract')->textarea(); ?>
                <div class="help-block"><?php echo $model->getFirstError('abstract'); ?></div>
            </td>
            <td class="vtop tips2"></td>
        </tr>
        <tr>
            <td class="td27" colspan="2">主题图片:
            </td>
        </tr>
        <tr class="noborder">
            <td class="vtop rowform">
                <?php if($model->thumbnail != ''):?>  
                    <img src="<?php echo Activity::getThumbnailAbsUrl($model->thumbnail); ?>" height=200px /><br/><br/>
                <?php endif;?>
                <?php echo $form->field($model, 'thumbnail')->fileInput(); ?>
            </td>
            <td class="vtop tips2"></td>
        </tr>
        <tr><td class="td27" colspan="2">内容:</td></tr>
        <tr class="noborder">
            <td class="vtop rowform">
                <div style="width:780px;height:400px;margin:5px auto 40px 0;">
                    <?php echo $form->field($model, 'content')->textarea(['style' => 'width:780px;height:295px;']); ?>
                </div>
                <div class="help-block"><?php echo $model->getFirstError('content'); ?></div>
            </td>
            <td class="vtop tips2"></td>
        </tr>
        <tr><td class="td27" colspan="2">状态:</td></tr>
        <tr class="noborder">
            <td class="vtop rowform"><?php echo $form->field($model, 'status')->radioList(Activity::$status);?></td>
            <td class="vtop tips2"></td>
        </tr>
        <tr>
            <td colspan="15">
                <input type="submit" value="提交" name="submit_btn" class="btn">
            </td>
        </tr>
    </table>
<?php ActiveForm::end(); ?>
