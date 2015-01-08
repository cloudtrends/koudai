<?php

use yii\widgets\LinkPager;
use yii\helpers\Html;
use common\models\GuestSuggest;

/**
 * @var backend\components\View $this
 */
$this->shownav('system', 'menu_guest_suggest_list');
$this->showsubmenu('反馈列表');

?>

<table class="tb tb2 fixpadding">
	<tr class="header">
		<th>ID</th>
		<th>用户ID</th>
		<th>反馈类型</th>
		<th>内容</th>
		<th>联系方式</th>
		<th>创建时间</th>
		<th>来源平台</th>
		<th>版本号</th>
		<th>设备名称</th>
		<th>设备系统版本</th>
	</tr>
	<?php foreach ($suggestList as $value): ?>
	<tr class="hover">
		<td class="td25"><?php echo $value->id; ?></td>
		<td><?php echo $value->user_id; ?></td>
		<td><?php echo isset(GuestSuggest::$types[$value->type]) ? GuestSuggest::$types[$value->type] : ''; ?></td>
		<td><?php echo Html::encode($value->content); ?></td>
		<td><?php echo Html::encode($value->user_info); ?></td>
		<td><?php echo date('Y-m-d H:i:s', $value->created_at); ?></td>
		<td><?php echo $value->client_type; ?></td>
		<td><?php echo $value->app_version; ?></td>
		<td><?php echo $value->device_name; ?></td>
		<td><?php echo $value->device_system_version; ?></td>
	</tr>
	<?php endforeach; ?>
</table>

<?php echo LinkPager::widget(['pagination' => $pages]); ?>
