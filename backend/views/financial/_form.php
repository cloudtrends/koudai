<?php
use yii\helpers\Url;
use common\models\Financial;
use backend\components\widgets\ActiveForm;
?>
<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl ?>/js/My97DatePicker/WdatePicker.js"></script>
<?php $form = ActiveForm::begin(['id' => 'financial-form']); ?>
    <table class="tb tb2 fixpadding">
    <!--录入定期项目-->
    <?php if($type == 'regular'): ?>        
        <tr height="60" style="display:none">
            <td colspan=4>
                <?php echo $form->field($model, 'project_type')->textInput(['value' => Financial::TYPE_REGULAR]); ?>
            </td>
        </tr>
        <tr  height="60">
            <td class="td24">选择项目</td>
            <td width=200><?php echo $form->field($model, 'project_id')->dropDownList($projectnames,['prompt' => '-请选择-']); ?></td>
            <td  class="tips2" colspan=2>根据id或名称选择项目,借款金额,用户（前台）利率和期限对应选择项目</td>
        </tr>
        <tr  height="60">
            <td class="td24">借款金额</td>
            <td><?php echo $form->field($model, 'total_amount_financing',['options' => ['style' => 'float:left;']])->textInput(['style' => 'width:120px;height:20px;background-color:#ccc;','readonly' => "readonly"]); ?>元</td>
            <td width=50>期限</td>
            <td><input id="term" name="term" value="<?php echo $model->term; ?>" style="width:120px;height:20px;background-color:#ccc;" readonly = "readonly"></td>
        </tr>
        <tr  height="60">
            <td class="td24">借款利息率</td>
            <td><?php echo $form->field($model, 'borrower_rate',['options' => ['style' => 'float:left;']])->textInput(['style' => 'width:120px;height:20px;',]); ?>%</td>
            <td>用户利率</td>
            <td><?php echo $form->field($model, 'user_rate',['options' => ['style' => 'float:left;']])->textInput(['style' => 'width:120px;height:20px;background-color:#ccc;','readonly'=>'readonly']); ?>%</td>
        </tr>
        <tr  height="60">
            <td class="td24">放款时间</td>
            <td><?php echo $form->field($model, 'loan_time', ['options' => ['style' => 'float:left;']])->textInput(['style' => 'width:100px;height:20px;','onFocus' => "WdatePicker()"]); ?>开始时间</td>
            <td colspan=2><?php echo $form->field($model, 'borrower_repayment_time', ['options' => ['style' => 'float:left;']])->textInput(['style' => 'width:100px;height:20px;','onFocus' => "WdatePicker()"]); ?>结束时间</td>
        </tr>
        <tr  height="60">
            <td class="td24">借款期限</td>
            <td colspan=2><input id="loan" name="loan" value="<?php echo $model->loan; ?>" style="width:120px;height:20px;background-color:#ccc;" readonly='readonly'></td>
            <td><span id="loan_timeErrors" style="color:red"></span></td>
        </tr>
    <?php endif; ?>


    <!--录入活期项目-->
    <?php if($type == 'current'): ?>
        <tr height="60" style="display:none">
            <td colspan=4>
                <?php echo $form->field($model, 'status')->textInput(['value' => Financial::PENDING_AUDIT]); ?>
                <?php echo $form->field($model, 'project_type')->textInput(['value' => Financial::TYPE_CURRENT]); ?>
            </td>
        </tr>
        <tr  height="60">
            <td class="td24">项目名称</td>
            <td width=200><?php echo $form->field($model, 'project_name')->textInput(['style' => 'width:120px;height:20px;',]); ?></td>
            <td  class="tips2" colspan=2>用户利息率对应口袋宝</td>
        </tr>
        <tr  height="60">
            <td class="td24">借款金额</td>
            <td colspan=3><?php echo $form->field($model, 'total_amount_financing',['options' => ['style' => 'float:left;']])->textInput(['style' => 'width:120px;height:20px;',]); ?>元</td>
        </tr>
        <tr  height="60">
            <td class="td24">借款利息率</td>
            <td><?php echo $form->field($model, 'borrower_rate',['options' => ['style' => 'float:left;']])->textInput(['style' => 'width:120px;height:20px;',]); ?>%</td>
            <td width=50>用户利率</td>
            <td><?php echo $form->field($model, 'user_rate',['options' => ['style' => 'float:left;']])->textInput(['style' => 'width:120px;height:20px;background-color:#ccc;','readonly'=>'readonly']); ?>%</td>
        </tr>
        <tr  height="60">
            <td class="td24">借款时间</td>
            <td><?php echo $form->field($model, 'loan_time', ['options' => ['style' => 'float:left;']])->textInput(['style' => 'width:100px;height:20px;','onFocus' => "WdatePicker()"]); ?>开始时间</td>
            <td colspan=2><?php echo $form->field($model, 'borrower_repayment_time', ['options' => ['style' => 'float:left;']])->textInput(['style' => 'width:100px;height:20px;','onFocus' => "WdatePicker()"]); ?>结束时间</td>
        </tr>        
        <tr  height="60">
            <td class="td24">放款期限</td>
            <td colspan=2><input id="loan" name="loan" value="<?php echo $model->loan; ?>" style="width:120px;height:20px;background-color:#ccc;" readonly='readonly'></td>
            <td><span id="loan_timeErrors" style="color:red"></span></td>
        </tr>
    <?php endif; ?>

    <!--编辑项目-->
    <?php if($type == 'edit'): ?>
    <!--审核活期项目-->
        <?php if($project_type == Financial::TYPE_CURRENT): ?>
            <tr>        
                    <td class="td24 td27"><?php echo $model->getAttributeLabel('project_type'); ?>：</td>
                    <td colspan="3">活期</td>
                </tr>
                <tr>        
                    <td class="td24"><?php echo $model->getAttributeLabel('project_id'); ?>：</td>
                    <td  width="300"><?php echo $model->project_id ? $model->project_id : '无'; ?></td>
                    <td class="td24"><?php echo $model->getAttributeLabel('project_name'); ?>：</td>
                    <td><?php echo $form->field($model, 'project_name')->textInput(); ?></td>
                </tr>
                <tr>
                    <td class="td24"><?php echo $model->getAttributeLabel('total_amount_financing'); ?>：</td>
                    <td colspan="3"><?php echo $form->field($model, 'total_amount_financing')->textInput(); ?></td>
                </tr>
                <tr>
                    <td class="td24"><?php echo $model->getAttributeLabel('borrower_rate'); ?>：</td>
                    <td><?php echo $form->field($model,'borrower_rate')->textInput(); ?></td>
                    <td class="td24"><?php echo $model->getAttributeLabel('user_rate'); ?>：</td>
                    <td><?php echo $model->user_rate; ?></td>
                </tr>
                <tr>
                    <td class="td24"><?php echo $model->getAttributeLabel('platform_revenue'); ?>：</td>
                    <td><?php echo $model->platform_revenue; ?></td>
                    <td class="td24"><?php echo $model->getAttributeLabel('investor_revenue'); ?>：</td>
                    <td><?php echo $model->investor_revenue; ?></td>
                </tr>
                <tr>
                    <td class="td24"><?php echo $model->getAttributeLabel('total_revenue'); ?>：</td>
                    <td colspan="3"><?php echo $model->total_revenue; ?></td>   
                </tr>
                <tr>
                    <td class="td24"><?php echo $model->getAttributeLabel('project_start_time'); ?>：</td>
                    <td><?php echo $model->project_start_time ? date('Y-m-d H:i:s', $model->project_start_time) : '-'; ?></td>
                    <td class="td24"><?php echo $model->getAttributeLabel('project_end_time'); ?>：</td>
                    <td><?php echo $model->project_end_time ? date('Y-m-d H:i:s', $model->project_end_time) : '-'; ?></td>
                </tr>
                <tr>
                    <td class="td24"><?php echo $model->getAttributeLabel('loan_time'); ?>：</td>
                    <td><?php echo $model->loan_time ? $model->loan_time : '-'; ?></td>
                    <td class="td24"><?php echo $model->getAttributeLabel('borrower_repayment_time'); ?>：</td>
                    <td><?php echo $model->borrower_repayment_time ? $model->borrower_repayment_time.$model->loan : '-'; ?></td>
                </tr>
                <tr>
                    <td class="td24"><?php echo $model->getAttributeLabel('created_at'); ?>：</td>
                    <td><?php echo $model->created_at ? date('Y-m-d H:i:s', $model->created_at) : '-'; ?></td>
                    <td class="td24"><?php echo $model->getAttributeLabel('updated_at'); ?>：</td>
                    <td><?php echo $model->updated_at ? date('Y-m-d H:i:s', $model->updated_at) : '-'; ?></td>
                </tr>
                <tr>
                    <td class="td24"><?php echo $model->getAttributeLabel('status'); ?>：</td>
                    <td  colspan="3"><?php echo $form->field($model, 'status')->radioList([$model->status => Financial::$status[$model->status],($model->status-3) => Financial::$status[($model->status-3)],]); ?></td>
                </tr>
                <tr>
                    <td class="td24"><?php echo $model->getAttributeLabel('remarks'); ?>：</td>
                    <td colspan="3"><?php echo $form->field($model, 'remarks')->textarea(); ?></td>
                </tr>
        <?php endif; ?>

    <!--编辑定期项目-->
        <?php if($project_type == Financial::TYPE_REGULAR): ?>           
            <tr>        
                <td class="td24 td27"><?php echo $model->getAttributeLabel('project_type'); ?>：</td>
                <td colspan="3">定期项目</td>
            </tr>
            <tr>        
                <td class="td24"><?php echo $model->getAttributeLabel('project_id'); ?>：</td>
                <td  width="300"><?php echo $model->project_id; ?></td>
                <td class="td24"><?php echo $model->getAttributeLabel('project_name'); ?>：</td>
                <td><?php echo $model->project_name; ?></td>
            </tr>
            <tr>
                <td class="td24"><?php echo $model->getAttributeLabel('total_amount_financing'); ?>：</td>
                <td colspan="3"><?php echo $model->total_amount_financing; ?></td>
            </tr>
            <tr>
                <td class="td24"><?php echo $model->getAttributeLabel('borrower_rate'); ?>：</td>
                <td><?php echo $form->field($model,'borrower_rate')->textInput(); ?></td>
                <td class="td24"><?php echo $model->getAttributeLabel('user_rate'); ?>：</td>
                <td><?php echo $model->user_rate; ?></td>
            </tr>
            <tr>
                <td class="td24"><?php echo $model->getAttributeLabel('platform_revenue'); ?>：</td>
                <td><?php echo $model->platform_revenue; ?></td>
                <td class="td24"><?php echo $model->getAttributeLabel('investor_revenue'); ?>：</td>
                <td><?php echo $model->investor_revenue; ?></td>
            </tr>
            <tr>
                <td class="td24"><?php echo $model->getAttributeLabel('total_revenue'); ?>：</td>
                <td colspan="3"><?php echo $model->total_revenue; ?></td>   
            </tr>
            <tr>
                <td class="td24"><?php echo $model->getAttributeLabel('project_start_time'); ?>：</td>
                <td><?php echo $model->project_start_time ? date('Y-m-d H:i:s', $model->project_start_time) : '-'; ?></td>
                <td class="td24"><?php echo $model->getAttributeLabel('project_end_time'); ?>：</td>
                <td><?php echo $model->project_end_time ? date('Y-m-d H:i:s', $model->project_end_time) : '-'; ?></td>
            </tr>
            <tr>
                <td class="td24"><?php echo $model->getAttributeLabel('loan_time'); ?>：</td>
                <td><?php echo $model->loan_time ? $model->loan_time : '-'; ?></td>
                <td class="td24"><?php echo $model->getAttributeLabel('borrower_repayment_time'); ?>：</td>
                <td><?php echo $model->borrower_repayment_time ? $model->borrower_repayment_time.$model->loan : '-'; ?></td>
            </tr>
            <tr>
                <td class="td24"><?php echo $model->getAttributeLabel('created_at'); ?>：</td>
                <td><?php echo $model->created_at ? date('Y-m-d H:i:s', $model->created_at) : '-'; ?></td>
                <td class="td24"><?php echo $model->getAttributeLabel('updated_at'); ?>：</td>
                <td><?php echo $model->updated_at ? date('Y-m-d H:i:s', $model->updated_at) : '-'; ?></td>
            </tr>
            <tr>
                <td class="td24"><?php echo $model->getAttributeLabel('status'); ?>：</td>
                <td  colspan="3"><?php echo $form->field($model, 'status')->radioList([$model->status => Financial::$status[$model->status],($model->status+1) => Financial::$status[($model->status+1)],]); ?></td>
            </tr>
            <tr>
                <td class="td24"><?php echo $model->getAttributeLabel('remarks'); ?>：</td>
                <td colspan="3"><?php echo $form->field($model, 'remarks')->textarea(); ?></td>
            </tr>
        <?php endif; ?>
    <?php endif; ?>

    <!--操作项目-->
        <?php if($type == 'operation'): ?>           
            <tr>        
                <td class="td24 td27"><?php echo $model->getAttributeLabel('project_type'); ?>：</td>
                <td colspan="3"><?php echo $model->getAttributeLabel('project_type'); ?></td>
            </tr>
            <tr>        
                <td class="td24"><?php echo $model->getAttributeLabel('project_id'); ?>：</td>
                <td  width="300"><?php echo $model->project_id; ?></td>
                <td class="td24"><?php echo $model->getAttributeLabel('project_name'); ?>：</td>
                <td><?php echo $model->project_name; ?></td>
            </tr>
            <tr>
                <td class="td24"><?php echo $model->getAttributeLabel('total_amount_financing'); ?>：</td>
                <td colspan="3"><?php echo sprintf('%.2f',$model->total_amount_financing / 100); ?></td>
            </tr>
            <tr>
                <td class="td24"><?php echo $model->getAttributeLabel('borrower_rate'); ?>：</td>
                <td><?php echo $model->borrower_rate; ?></td>
                <td class="td24"><?php echo $model->getAttributeLabel('user_rate'); ?>：</td>
                <td><?php echo $model->user_rate; ?></td>
            </tr>
            <tr>
                <td class="td24"><?php echo $model->getAttributeLabel('platform_revenue'); ?>：</td>
                <td><?php echo sprintf('%.2f',$model->platform_revenue / 100); ?></td>
                <td class="td24"><?php echo $model->getAttributeLabel('investor_revenue'); ?>：</td>
                <td><?php echo sprintf('%.2f',$model->investor_revenue / 100); ?></td>
            </tr>
            <tr>
                <td class="td24"><?php echo $model->getAttributeLabel('total_revenue'); ?>：</td>
                <td colspan="3"><?php echo sprintf('%.2f',$model->total_revenue / 100); ?></td>   
            </tr>
            <tr>
                <td class="td24"><?php echo $model->getAttributeLabel('project_start_time'); ?>：</td>
                <td><?php echo $model->project_start_time ? date('Y-m-d H:i:s', $model->project_start_time) : '-'; ?></td>
                <td class="td24"><?php echo $model->getAttributeLabel('project_end_time'); ?>：</td>
                <td><?php echo $model->project_end_time ? date('Y-m-d H:i:s', $model->project_end_time) : '-'; ?></td>
            </tr>
            <tr>
                <td class="td24"><?php echo $model->getAttributeLabel('loan_time'); ?>：</td>
                <td><?php echo $model->loan_time ? $model->loan_time : '-'; ?></td>
                <td class="td24"><?php echo $model->getAttributeLabel('borrower_repayment_time'); ?>：</td>
                <td><?php echo $model->borrower_repayment_time ? $model->borrower_repayment_time.$model->loan : '-'; ?></td>
            </tr>
            <tr>
                <td class="td24"><?php echo $model->getAttributeLabel('created_at'); ?>：</td>
                <td><?php echo $model->created_at ? date('Y-m-d H:i:s', $model->created_at) : '-'; ?></td>
                <td class="td24"><?php echo $model->getAttributeLabel('updated_at'); ?>：</td>
                <td><?php echo $model->updated_at ? date('Y-m-d H:i:s', $model->updated_at) : '-'; ?></td>
            </tr>
            <tr>
                <td class="td24"><?php echo $model->getAttributeLabel('status'); ?>：</td>
                <td  colspan="3"><?php echo $form->field($model, 'status')->radioList([$model->status => Financial::$status[$model->status],($model->status+1) => Financial::$status[($model->status+1)],]); ?></td>
            </tr>
            <tr>
                <td class="td24"><?php echo $model->getAttributeLabel('remarks'); ?>：</td>
                <td colspan="3"><?php echo $form->field($model, 'remarks')->textarea(); ?></td>
            </tr>
        <?php endif; ?>
    <tr>
            <td colspan="15">
                <input type="submit" value="提交" name="submit_btn" class="btn">
            </td>
        </tr>
    </table>        
<?php ActiveForm::end(); ?>

<script>
//jquery 入口
$(function(){
    $("#financial-form #financial-project_id").change(function(){
        //获取选择的值
        var pid = $(this).val();
        //得到当前的select对象
        var $_this = $(this);
        //ajax操作
        $.ajax({
            url:'<?php echo Url::to(['financial/load-project']); ?>',
            async:false,//同步刷新
            data:{ajaxpid:pid},
            type:'get',
            success:function(data){
                $("#financial-total_amount_financing").val(data.total_amount_financing);
                $("#financial-form #financial-user_rate").val(data.user_rate);
                $("#financial-form #term").val(data.term);
            }
        });
    });

    //计算时间差
    $('#financial-form #financial-loan_time,#financial-form #financial-borrower_repayment_time').blur(function(){
        var begintime = $("#financial-form #financial-loan_time").val();
        var endtime = $("#financial-form #financial-borrower_repayment_time").val();
        if(begintime!="" && endtime!=""){
            var y1 = begintime.substring(0, 4);
            var m1 = begintime.substring(5,7);
            var d1 = begintime.substring(8,10);
            var y2 = endtime.substring(0, 4);
            var m2 = endtime.substring(5,7);
            var d2 = endtime.substring(8,10);
            var date1 = new Date(y1,m1,d1);  //开始时间
            var date2 = new Date(y2,m2,d2);     //结束时间
            var date3 = date2.getTime() - date1.getTime();   //时间差的毫秒数
            //计算相差的年数
            var years = Math.floor(date3 / (12 * 30 * 24 * 3600 * 1000));
            //计算相差的月数
            var leave = date3 % (12 * 30 * 24 * 3600 * 1000);
            var months = Math.floor(leave / (30 * 24 * 3600 * 1000));
            //计算出相差天数
            var leave0 = leave % (30 * 24 * 3600 * 1000);
            var days = Math.floor(leave0 / (24 * 3600 * 1000));
            if(years<0 || months<0 || days<0){
                $("#financial-form #financial-borrower_repayment_time").val('');
                $("#financial-form #loan_timeErrors").html('*结束时间必须大于开始时间');
                $("#financial-form #loan").val('');
                return false;
            }
            $.ajax({
                url:'<?php echo Url::to(['financial/load-time']); ?>',
                async:false,//同步刷新
                data:{years: years,months:months,days:days},
                type:'get',
                success:function(data){
                    $("#financial-form #loan_timeErrors").html('');
                    $("#financial-form #loan").val(data.loan);
                }
            });
        }
    });

})
</script>