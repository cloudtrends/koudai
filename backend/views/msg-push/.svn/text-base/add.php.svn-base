<?php
/**
 * Created by PhpStorm.
 * User: changhaoyu
 * Date: 14-10-21
 * Time: 下午11:00
 */

use yii\helpers\Url;


$this->shownav('content', 'menu_content_manager');
$this->showsubmenu('消息推送', array(
	array('推送任务列表', Url::toRoute('msg-push/list'), 0),
	array('添加任务', Url::toRoute('msg-push/add'), 1),
));

?>

<?php echo $this->render('_form',[
	"model" => $model,
	"taskType" => $taskType,
        "action" => $action,

]);
?>