<?php

/**
 * @var backend\components\View $this
 */
$this->shownav('financial', 'menu_financial_list');
if($type == 'edit'){    
$this->showsubmenuanchors('项目列表', array(
    array('编辑项目', 'form', 1),
));
}
if($type == 'operation'){    
$this->showsubmenuanchors('项目列表', array(
    array('操作项目', 'form', 1),
));
}

?>

<?php echo $this->render('_form', ['model' => $model,'projectnames' => $projectnames,'type' => $type,'project_type' => $project_type]); ?>