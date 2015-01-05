<?php

use yii\helpers\Url;

/**
 * @var backend\components\View $this
 */
$this->shownav('content', 'menu_content_manager');
$this->showsubmenu('文章管理', array(
	array('列表', Url::toRoute('article/list'), 0),
	array('添加文章', Url::toRoute('article/add'), 1),
));

?>

<?php echo $this->render('_form', ['model' => $model, 'articleTypeItems' => $articleTypeItems]); ?>