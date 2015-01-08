<?php

use common\models\Financial;

/**
 * @var backend\components\View $this
 */
$this->shownav('financial', 'menu_financial_list');
$this->showsubmenuanchors('查看项目', array(
    array('基本信息', 'baseinfo', 1),
));

?>

<div id="baseinfo">
	<?php echo $this->render('_baseinfo', ['model' => $model]); ?>
</div>