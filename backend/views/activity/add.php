<?php

use yii\helpers\Url;

/**
 * @var backend\components\View $this
 */
$this->shownav('content', 'menu_activity_manager');
$this->showsubmenu('活动管理', array(
    array('列表', Url::toRoute('activity/list'), 0),
    array('添加活动', Url::toRoute('activity/add'), 1),
));

?>

<?php echo $this->render('_form', ['model' => $model]); ?>