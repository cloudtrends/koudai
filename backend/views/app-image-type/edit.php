<?php

use yii\helpers\Url;

/**
 * @var backend\components\View $this
 * @var backend\components\widgets\ActiveFrom $form
 */
$this->shownav('app_config', 'menu_app_config_banner_begin');
$this->showsubmenu('图片类型管理', array(
	array('列表', Url::toRoute('app-image-type/list'), 1),
	array('添加类型', Url::toRoute('app-image-type/add'), 0),
));

?>

<?php echo $this->render('_form', ['model' => $model]); ?>