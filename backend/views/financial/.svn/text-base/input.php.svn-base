<?php

/**
 * @var backend\components\View $this
 */
$menuKey = $title = '';
switch ($type) {
    case 'current':
        $menuKey = 'menu_financial_current';
        $title = '  活期项目';
        break;
    case 'regular':
        $menuKey = 'menu_financial_regular';
        $title = '  定期项目';
        break;
}
$this->shownav('financial', $menuKey);
$this->showsubmenu($title);

?>

<?php echo $this->render('_form', ['model' => $model, 'projectnames' => $projectnames,'type' => $type,]); ?>