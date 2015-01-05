<?php

use yii\helpers\Url;

/**
 * @var backend\components\View $this
 */
$this->shownav('content', 'menu_contenttype_manager');
$this->showsubmenu('栏目管理', array(
	array('列表', Url::toRoute('article-type/list'), 0),
	array('添加栏目', Url::toRoute('article-type/add'), 1),
));

?>

<?php echo $this->render('_form', ['model' => $model]); ?>