<?php
use yii\helpers\Url;
use yii\helpers\Html;
use common\models\Project;
use backend\components\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\ProjectInvest;
use common\models\CreditBaseInfo;
use common\models\ProjectProfits;

/**
 * @var backend\components\View $this
 */
$this->shownav('project', 'menu_invest_invests');
$this->showsubmenu('投资记录');

?>

<table class="tb tb2 fixpadding">
    <tr><th class="partition" colspan="15">项目信息</th></tr>
    <tr>  
        <td class="td24">项目名称：</td>
        <td width="300"><?php echo $investItem['project_name']; ?></td>
        <td class="td24">类型：</td>
        <td><?php echo $investItem['jtype'] ?></td>       
    </tr>
    <tr>
        <td class="td24">项目金额：</td>
        <td><?php echo sprintf('%.2f', $investItem['total_money'] / 100); ?> 元</td>
        <td class="td24">年利率：</td>
        <td><?php echo $investItem['apr']; ?> %</td>
    </tr>
    <tr>
        <td class="td24">借款期限：</td>
        <td><?php echo $investItem['period'] . ' ' . ( $investItem['is_day'] ? '天' : '月'); ?></td>
        <td class="td24">状态：</td>
        <td><?php echo $investItem['project_status']; ?></td> 
    </tr>
    <tr><th class="partition" colspan="15">投资信息</th></tr>
    <tr>
        <td class="td24">状态：</td>
        <td><?php echo ProjectInvest::$status[$investItem['status']]; ?></td>
        <td class="td24">投资人姓名：</td>
        <td><?php echo $investItem['username']; ?></td>     
    </tr>           
    <?php if ($investItem['status'] != 3): ?>
        <tr>
            <td class="td24">投资金额：</td>
            <td><?php echo sprintf('%.2f', $investItem['invest_money'] / 100); ?> 元</td>
            <td class="td24">待收总额：</td>
            <td><?php echo sprintf('%.2f', $investItem['duein_money'] / 100); ?></td>       
        </tr>
        <tr>
            <td class="td24">预计收益率：</td>
            <td><?php echo $investItem['project_apr']; ?> %</td>
            <td class="td24">待收收益：</td>
            <td><?php echo sprintf('%.2f', $investItem['duein_profits'] / 100); ?></td>
        </tr>
        <tr>
            <td class="td24">起息日：</td>
            <td><?php echo $investItem['interest_start_date']; ?></td>
            <td class="td24">还款日：</td>
            <td><?php echo $investItem['last_repay_date']; ?></td>
        </tr>

        <tr>
            <td class="td24">是否是债权转让：</td>
            <td><?php echo $investItem['is_transfer'] ? '是' : '否'; ?></td>
            <td class="td24">备注：</td>
            <td><?php echo $investItem['remark']; ?></td>
        </tr>
    <?php endif; ?>  
        <?php if ($investItem['is_transfer']==1): ?>
            <tr><th class="partition" colspan="15">转让信息</th></tr>
            <tr>
                 <td class="td24">状态：</td>
                <td><?php echo  $formItem['status']; ?> </td>         
                <td class="td24">转让人(上家)：</td>
                <td><?php echo  $creditList['user_name']; ?></td>              
            </tr>
            <tr>
                 <td class="td24">投资金额：</td>
                <td><?php echo  sprintf('%.2f', $formItem['invest_money'] / 100); ?> </td>
                <td class="td24">预计收益率：</td>
                <td><?php echo  $formItem['project_apr']; ?> %</td>                 
            </tr>
             <tr>
                <td class="td24">平台手续费：</td>
                <td><?php echo $creditList['commission_rate']; ?></td>
                <td class="td24">转让时间：</td>
                <td><?php echo date('Y-m-d',  $creditList['assign_start_date']); ?></td>
            </tr>       
     <?php endif;   ?>   
      <?php if ($investItem['status'] == 6): ?>
                <tr><th class="partition" colspan="15">转让信息</th></tr>
                <tr>
                    <td class="td24">状态：</td>
                    
                    <td><?php echo $latterItem['status']; ?> </td>    
                    <td class="td24">接受人(下家)：</td>
                    <td><?php echo $latterItem['usernmae']; ?></td>               
                </tr>
                <tr>
                    <td class="td24">投资金额：</td>
                    <td><?php echo sprintf('%.2f', $latterItem['invest_money'] / 100); ?> </td>
                    <td class="td24">预计收益率：</td>
                    <td><?php echo $latterItem['project_apr']; ?> %</td>                           
                </tr>
                <tr>
                    <td class="td24">起息日：</td>
                    <td><?php echo $latterItem['start_date']; ?></td>
                    <td class="td24">还款日：</td>
                    <td><?php echo $latterItem['repay_date']; ?></td>
                </tr>       
            <?php endif; ?>   
                <?php if ($investItem['status'] == 4): ?>
                    <tr><th class="partition" colspan="15">转让信息</th></tr>
                    <tr>
                        <td class="td24">状态：</td>
                        <td><?php echo $creditList['status']; ?> </td> 
                        <td class="td24">预计收益率：</td>
                        <td><?php echo $creditList['assign_rate']; ?> %</td>    
                    </tr>
                    <tr>
                        <td class="td24">转让价格：</td>
                        <td><?php echo sprintf('%.2f',$creditList['assign_fee'] / 100);?></td>
                        <td class="td24">转让时间：</td>
                        <td><?php echo date('Y-m-d', $creditList['assign_start_date']); ?></td>
                    </tr> 
                    <tr>
                      
                        <td class="td24">平台手续费：</td>
                        <td><?php echo $creditList['commission_rate']; ?></td>
                    </tr>
                <?php endif; ?>   
   
</table>
