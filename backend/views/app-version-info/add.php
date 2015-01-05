<?php

use yii\helpers\Url;
use backend\components\widgets\ActiveForm;

/**
 * @var backend\components\View $this
 */
$this->shownav('app_config', 'menu_app_config_version_begin');
$this->showsubmenu('app版本管理', array(
	array('列表', Url::toRoute('app-version-info/list'), 0),
	array('添加', Url::toRoute('app-version-info/add'), 1),
));
?>

<?php echo $this->render('_form', ['model' => $model, 'apps' => $apps]); ?>