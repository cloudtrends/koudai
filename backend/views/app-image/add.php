<?php

use yii\helpers\Url;
use backend\components\widgets\ActiveForm;

/**
 * @var backend\components\View $this
 */
$this->shownav('app_config', 'menu_app_config_banner_begin');
$this->showsubmenu('app图片管理', array(
	array('列表', Url::toRoute('app-image/list'), 0),
	array('添加图片', Url::toRoute('app-image/add'), 1),
));
?>

<?php echo $this->render('_form', ['model' => $model]); ?>