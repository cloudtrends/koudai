<?php

use yii\helpers\Url;
use backend\components\widgets\ActiveForm;

/**
 * @var backend\components\View $this
 */
$this->shownav('app_config', 'menu_app_config_banner_begin');
$this->showsubmenu('图片类型管理', array(
	array('列表', Url::toRoute('app-image-type/list'), 0),
	array('添加类型', Url::toRoute('app-image-type/add'), 1),
));
?>

<?php echo $this->render('_form', ['model' => $model]); ?>