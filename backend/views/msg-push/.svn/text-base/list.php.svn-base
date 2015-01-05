<?php
/**
 * Created by PhpStorm.
 * User: haoyu
 * Date: 14-10-21
 * Time: 下午11:00
 */

use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->shownav('content', 'menu_content_manager');
$this->showsubmenu('消息推送', array(
	array('推送任务列表', Url::toRoute('msg-push/list'), 1),
	array('添加任务', Url::toRoute('msg-push/add'), 0),
));

?>
<title>消息推送任务列表</title>
<table>
    <tr class="msg_push_list_table_header">
        <th>ID</th>
        <th>推送类型</th>
        <th>接受者列表</th>
        <th>推送内容</th>
        <th>期望推送时间</th>
        <th>更新时间</th>
        <th>状态</th>
        <th>操作</th>
    </tr>
    <?php foreach ($taskList as $task): ?>
    <tr>
        <td>
            <?php echo $task->task_id; ?>
        </td>
        <td>
            <?php echo $taskType[$task->task_type]; ?>
        </td>
        <td>
            <?php echo $task->receiver_list; ?>
        </td>
        <td>
            <?php echo $task->msg_content; ?>
        </td>
        <td>
            <?php echo $task->expect_time; ?>
        </td>
        <td>
            <?php echo date("Y-m-d H:i:s",$task->updated_at); ?>
        </td>
        <td>
            <?php echo $statusDesc[$task->status]; ?>
        </td>
        <td>
            <a href="<?php echo $this->baseUrl."/index.php?r=msg-push/edit&task_id=".$task->task_id ?>">编辑</a>
            <a href="<?php echo $this->baseUrl."/index.php?r=msg-push/send&task_id=".$task->task_id ?>">立即发送</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php echo LinkPager::widget(['pagination' => $pages]); ?>