<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Project;
/**
 * @var backend\components\View $this
 */
$this->shownav('project', 'menu_project_review_new');
$this->showsubmenu('审核新项目');

?>

<?php echo $this->render('_baseinfo', ['model' => $model]); ?>

<?php $form = ActiveForm::begin(['id' => 'review-form']); ?>
<table class="tb tb2 fixpadding">
	<tr><th class="partition" colspan="15">审核此项目</th></tr>
	<tr>
		<td class="td24">操作</td>
		<td><?php echo Html::radioList('operation', 1, [
                '1' => Project::$action_desc[Project::ACTION_PUBLISH],
                '2' => Project::$action_desc[Project::ACTION_CANCEL]
            ]); ?></td>
	</tr>
	<tr>
		<td class="td24">审核备注：</td>
		<td><?php echo Html::textarea('remark', '', ['style' => 'width:300px;']); ?></td>
	</tr>
	<tr>
		<td colspan="15">
			<input type="submit" value="提交" name="submit_btn" class="btn">
		</td>
	</tr>
</table>
<?php ActiveForm::end(); ?>