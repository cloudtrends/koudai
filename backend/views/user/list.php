<?php

use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\helpers\Html;
use common\models\User;
use backend\components\widgets\ActiveForm;

/**
 * @var backend\components\View $this
 */
$this->shownav('user', 'menu_user_list');
$this->showsubmenu('用户基本信息');

?>

<?php $form = ActiveForm::begin(['id' => 'searchform','method'=>'get', 'options' => ['style' => 'margin-bottom:5px;']]); ?>
            ID：<input type="text" value="<?php echo Yii::$app->getRequest()->get('id', ''); ?>" name="id" class="txt" style="width:60px;">&nbsp;
	用户名关键词：<input type="text" value="<?php echo Yii::$app->getRequest()->get('username', ''); ?>" name="username" class="txt" style="width:120px;">&nbsp;
	姓名：<input type="text" value="<?php echo Yii::$app->getRequest()->get('realname', ''); ?>" name="realname" class="txt" style="width:120px;">&nbsp;
	<input type="submit" name="search_submit" value="过滤" class="btn">
<?php ActiveForm::end(); ?>

<form name="listform" method="post">
	<table class="tb tb2 fixpadding">
		<tr class="header">
			<th>ID</th>
			<th>用户名</th>
			<th>手机号</th>
			<th>实名</th>
			<th>性别</th>
			<th>生日</th>
			<th>来源</th>
			<th>注册时间</th>
			<th>注册ip</th>
			<th>实名认证</th>
			<th>绑定银行卡</th>
			<th>新手</th>
			<th>状态</th>
			<th>操作</th>
		</tr>
		<?php foreach ($users as $value): ?>
		<tr class="hover">
			<td class="td25"><?php echo $value->id; ?></td>
			<td><?php echo $value->username; ?></td>
			<td><?php echo $value->phone; ?></td>
			<td><?php echo $value->realname; ?></td>
			<td><?php echo User::$sexes[$value->sex]; ?></td>
			<td><?php echo $value->birthday; ?></td>
			<td><?php echo $value->source ==1 ? '普通注册' : $value->source; ?></td>
			<td><?php echo date('Y-m-d H:i:s', $value->created_at); ?></td>
			<td><?php echo $value->created_ip; ?></td>
			<td><?php echo $value->real_verify_status ? '是' : '否'; ?></td>
			<td><?php echo $value->card_bind_status ? '是' : '否'; ?></td>
			<td><?php echo $value->is_novice ? '是' : '否'; ?></td>
			<td><?php echo $value->status ? '用户可用' : '已禁用'; ?></td>
			<td>
				<a href="<?php echo Url::to(['user/edit', 'id' => $value->id]); ?>">编辑</a>
				<a onclick="return confirmMsg('确定要<?php echo $value->status ? '禁用' : '解封'; ?>吗？');" href="<?php echo Url::to(['user/delete', 'id' => $value->id,'status'=>$value->status]); ?>"><?php echo $value->status ? '禁用' : '解封'; ?></a>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
            <?php if (empty($users)): ?>
                <div class="no-result">暂无记录</div>   
            <?php endif; ?>
</form>

<?php echo LinkPager::widget(['pagination' => $pages]); ?>