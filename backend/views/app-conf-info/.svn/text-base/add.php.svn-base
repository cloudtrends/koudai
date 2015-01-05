<?php

use yii\helpers\Url;
use backend\components\widgets\ActiveForm;

/**
 * @var backend\components\View $this
 */
$this->shownav('app_config', 'menu_app_config_common_begin');
$this->showsubmenu('app管理', array(
	array('列表', Url::toRoute('app-conf-info/list'), 0),
	array('添加', Url::toRoute('app-conf-info/add'), 1),
));
?>

<?php echo $this->render('_form', ['model' => $model]); ?>