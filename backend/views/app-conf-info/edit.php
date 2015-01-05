<?php

use yii\helpers\Url;

/**
 * @var backend\components\View $this
 * @var backend\components\widgets\ActiveFrom $form
 */
$this->shownav('app_config', 'menu_app_config_common_begin');
$this->showsubmenu('app管理', array(
	array('列表', Url::toRoute('app-conf-info/list'), 1),
	array('添加', Url::toRoute('app-conf-info/add'), 0),
));

?>

<?php echo $this->render('_form', ['model' => $model]); ?>