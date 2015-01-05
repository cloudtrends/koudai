<?php

/**
 * @var backend\components\View $this
 */
$this->shownav('project', 'menu_project_create');
$this->showsubmenu('项目创建');

?>

<?php echo $this->render('_form', ['model' => $model]); ?>