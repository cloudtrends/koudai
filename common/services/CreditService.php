<?php
/**
 * Created by PhpStorm.
 * User: haoyu
 * Date: 2014/11/10
 * Time: 16:06
 */

namespace common\services;

use common\helpers\StringHelper;
use common\helpers\TimeHelper;
use common\models\Project;
use common\models\CreditBaseInfo;
use common\models\ProjectInvest;
use common\models\ProjectProfits;
use common\models\User;
use common\models\UserAccount;
use common\models\UserBankCard;
use common\models\UserDailyProfits;
use yii;
use yii\base\Object;
use yii\base\Exception;
use common\exceptions\InvestException;
use common\models\NoticeSms;

class CreditService extends Object
{
    // 转让
    public function assign( $invest_id, $assign_fee, $investInfo )
    {
        // 开启事务
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        $condition = [
            'project_id' => $investInfo['project_id'],
            'invest_id' => $investInfo['invest_id'],
            'user_id' => $investInfo['invest_uid'],
        ];
        // 计算过程中间值，可以输出做参考
        $params = [];
        try
        {
            // 单位转成分
            $assign_fee = StringHelper::safeConvertCentToInt($assign_fee);

            // ------- 1. 更新原来的记录 -------
            // 1.1 项目投资记录表，找到原来的记录，更新状态 ( 必须从 “成功” 到 “转让中” )
            $affected_rows = $db->createCommand()->update(ProjectInvest::tableName(),[
                "status" => ProjectInvest::STATUS_ASSIGNING,
            ],[
                "status" => ProjectInvest::STATUS_SUCCESS,
                "id" => $invest_id,
            ])->execute();

            if(empty($affected_rows))
            {
                InvestException::throwCodeExt(1004);
            }

            // 1.2 项目投资收益表，找到原来的记录，更新状态 ( 必须从 “成功” 到 “转让中” )
            $affected_rows = $db->createCommand()->update(ProjectProfits::tableName(),[
                "status" => ProjectProfits::STATUS_ASSIGNING,
            ],[
                "status" => ProjectProfits::STATUS_SUCCESS,
                "invest_id" => $invest_id,
            ])->execute();

            if(empty($affected_rows))
            {
                InvestException::throwCodeExt(1009,"({$invest_id})");
            }

            $now = TimeHelper::Now();

            // 转让项目开始时间
            $assign_start_time = TimeHelper::zeroClockTimeOfDay($now);

            // 原项目开始时间
            $project_start_time = TimeHelper::zeroClockTimeOfDay( $investInfo['pp_interest_start_date'] );

            // 原项目结束时间
            $project_end_time = TimeHelper::zeroClockTimeOfDay( $investInfo['pp_last_repay_date'] );

            // 已进行时间, 不用 +1 ，因为转让当天的收益属于下家
            $completed_days = intval(($assign_start_time - $project_start_time) / TimeHelper::DAY) ;
            $params['投资已进行时间'] = $completed_days;

            // 项目剩余天数
            $rest_days = intval(($project_end_time - $assign_start_time) / TimeHelper::DAY) + 1;
            $params['投资剩余天数'] = $rest_days;

            // 项目总天数
            $last_days = intval(($project_end_time - $project_start_time) / TimeHelper::DAY) + 1;
            $params['投资总天数'] = $last_days;

            // 参数检查，防止出现负数的天数
            if ( $completed_days <= 0 || $rest_days <= 0 || $last_days <= 0 )
            {
                InvestException::throwCodeExt(1018);
            }

            // 计算 转让利率

            /*
             *  总应收额 = 本金 + 本金 * 项目利率 * 项目总时间 / 365
             *  下家应收收益 = 总应收额 - 转让金额
             *  下家本金 = 转让金额
             *  下家应收额 = 总应收额
             *  下家利率 = (下家应收收益 / 转让金额) / ( 项目剩余时间 / 365  )
             */
            $capital = $investInfo['pi_invest_money']; // 本金

            $total =  $capital + $capital * ($investInfo['pp_project_apr'] / 100) * $last_days / 365; // 总应收额

            $next_duein_profits  = $total - $assign_fee; // 下家应收收益

            $next_duein_rate = ($next_duein_profits / $assign_fee) / ( $rest_days / 365); // 下家利率

            $assign_rate = $next_duein_rate * 100;  // 下家利率

            $params['本金'] = $capital;
            $params['应收总额'] = $total;
            $params['年利率'] = $investInfo['pp_project_apr'];
            $params['剩余天数'] = $rest_days;
            $params['总持续天数'] = $last_days;
            $params['开始日期'] = date("Y-m-d H:i:s", $project_start_time);
            $params['结束日期'] = date("Y-m-d H:i:s", $project_end_time);
            $params['上家转让日期'] = date("Y-m-d H:i:s", $assign_start_time);
            $params['上家价格'] = $assign_fee;
            $params['下家应收收益'] = $next_duein_profits;
            $params['下家利率'] = $next_duein_rate;

            Yii::info(var_export($params,true));

            $totalCount = CreditBaseInfo::find()->where($condition)->count();

            if($totalCount == 0)
            {
                $affected_rows = $db->createCommand()->insert(CreditBaseInfo::tableName(), [
                    'project_type' => $investInfo['pi_type'],
                    'project_id' => $investInfo['project_id'],
                    'invest_id' => $investInfo['invest_id'],
                    'assign_start_date' => $assign_start_time,// 起息时间为转让时间当天0点
                    'assign_end_date' => $project_end_time,// 结息时间为项目结息时间,
                    'assign_fee' => $assign_fee,
                    'assign_rate' => $assign_rate,
                    'user_id' => $investInfo['invest_uid'],
                    'user_name' => $investInfo['pi_username'],
                    'user_ip' => Yii::$app->getRequest()->getUserIP(),
                    'commission_rate' => 0,
                    'status' => CreditBaseInfo::STATUS_ASSIGNING,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->execute();

                if(empty($affected_rows))
                {
                    $transaction->rollBack();
                    InvestException::throwCodeExt(1015);
                }

                // 将invest表中添加转让id
                $ca_base_id = $db->lastInsertID;

                $affected_rows = $db->createCommand()->update(ProjectInvest::tableName(), [
                    "ca_base_id" => $ca_base_id,
                ], [                           
                    "id" => $invest_id,
                ])->execute();

                if (empty($affected_rows)) {
                    InvestException::throwCodeExt(1004);
                }
            }
            else
            {
                $affected_rows = $db->createCommand()->update(CreditBaseInfo::tableName(), [
                    'project_type' => $investInfo['pi_type'],
                    'assign_start_date' => $assign_start_time,// 起息时间为转让时间当天0点
                    'assign_end_date' => $project_end_time,// 结息时间为项目结息时间,
                    'assign_fee' => $assign_fee,
                    'assign_rate' => $assign_rate,
                    'user_name' => $investInfo['pi_username'],
                    'user_ip' => Yii::$app->getRequest()->getUserIP(),
                    'commission_rate' => 0,
                    'status' => CreditBaseInfo::STATUS_ASSIGNING,
                    'created_at' => $now,
                    'updated_at' => $now,
                ], $condition )->execute();

                if(empty($affected_rows))
                {
                    InvestException::throwCodeExt(1015);
                }
            }

            // 提交事务
            $transaction->commit();
        }
        catch (Exception $e)
        {
            Yii::info(var_export($params,true));
            $transaction->rollBack();
            throw $e;
        }

        return CreditBaseInfo::find()->select([
            'id',
            'project_type',
            'project_id',
            'invest_id',
            'assign_start_date',
            'assign_end_date',
            'assign_fee',
            'assign_rate',
            'user_id',
            'user_name',
            'commission_rate',
            'status',
        ])->where($condition)->asArray()->one();
    }

    public function applyAssignment( $curUser, $invest_id, $use_remain, $investInfo)
    {
        // 开启事务
        $uid = $curUser['id']; // 下家uid
        $destination_uid = $investInfo['invest_uid']; // 上家uid

        if( $uid == $destination_uid )
        {
            InvestException::throwCodeExt(1017);
        }

        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();

        try
        {
            // 用户投资金额即为上家投资的金额
            $apply_money = $investInfo['invest_money'];

            // ------- 1. 新增申购用户的投资记录 -------
            $affected_rows = $db->createCommand()->insert( ProjectInvest::tableName(), [
                'project_id' => $investInfo['project_id'],
            	'project_name' => $investInfo['project_name'],
                'user_id' => $uid,
                'username' => $curUser['username'],
                'status' => ProjectInvest::STATUS_SUCCESS,
                'invest_money' => $investInfo['cbi_assign_fee'], // 下家的投资金额为上家转让价格
                'created_ip' => Yii::$app->getRequest()->getUserIP(),
                'type' => $investInfo['pi_type'], // 与原来的项目同类型
                'is_statistics' => 0,
                'is_transfer' => 1, // 是债权转让
                'former_invest_id' => $invest_id, // 被转让的投资ID
                'ca_base_id' => $investInfo['ca_base_id'], // 债权转让表对应的记录ID
                'created_at' => TimeHelper::Now(),
                'updated_at' => TimeHelper::Now(),
            ])->execute();

            if( $affected_rows == 0 )
            {
                InvestException::throwCodeExt(1014);
            }

            $new_invest_id = $db->lastInsertID;


            // ------- 2. 投资表记录信息修改 -------
            // 2.1 原投资记录表累加转让投资的额度，需要确保不超额
            // 剩余额度 等于 申购金额，状态变为 成功转让
            $sql = "update ". ProjectInvest::tableName() .
                " SET transfer_money = transfer_money + {$apply_money},
                    latter_invest_id = {$new_invest_id},
                    status = ". ProjectInvest::STATUS_FULLY_ASSIGNED .  ",
                    updated_at = ". TimeHelper::Now() .
                " WHERE transfer_money + {$apply_money} = invest_money
                  AND id = {$invest_id}
                  AND status =".ProjectInvest::STATUS_ASSIGNING;

            $affected_rows = $db->createCommand($sql)->execute();
            if( $affected_rows == 0 )
            {
                InvestException::throwCodeExt(1014);
            }
            
            // ------- 3. 更新转让项目状态 -------
            $affected_rows = $db->createCommand()->update( CreditBaseInfo::tableName(),[
                'status' => CreditBaseInfo::STATUS_FULLY_ASSIGNED
            ],[
                'invest_id' => $invest_id,
                'status' => CreditBaseInfo::STATUS_ASSIGNING,
            ])->execute();

            if(empty($affected_rows))
            {
                InvestException::throwCodeExt(1014);
            }
            
            // ------- 3. 收益表 -------
            // 3.1 找到原来的收益记录

            Yii::info("investInfo:".var_export($investInfo,true));
            $params = [];

            // 原来的利率
            $former_project_apr = $investInfo['p_apr'];

            // 最后计息日: 发起转让的前一天日期（昨天）
            $transfer_date = TimeHelper::Yesterday();

            // 到目前为止一共进行了多少天
            $last_days = intval((strtotime($transfer_date) - strtotime("{$investInfo['pp_interest_start_date']}")) / TimeHelper::DAY)+1;

            // 转让后的应收收益
            $duein_profits_transferred = $investInfo['cbi_assign_fee'] - $investInfo['pp_duein_capital'];

            // 转让后的利率
            $project_apr_transferred = 100 * ($duein_profits_transferred / $investInfo['pp_duein_capital']) / ($last_days / 365) ;

            // 下家持有天数
            $next_last_days = intval((strtotime($investInfo['pp_last_repay_date']) - strtotime(TimeHelper::Today()) ) / TimeHelper::DAY);

            // 下家收益率
            $new_project_apr = (($investInfo['pp_duein_money'] - $investInfo['cbi_assign_fee']) / $investInfo['cbi_assign_fee']) / ($next_last_days / 365);

            $params['项目利率'] = $former_project_apr;
            $params['上家本金'] = $investInfo['pp_duein_capital'];
            $params['上家收益'] = $duein_profits_transferred;
            $params['上家持有天数'] = $last_days;
            $params['上家起息时间'] = $investInfo['pp_interest_start_date'];
            $params['上家结息时间'] = $transfer_date;
            $params['上家利率'] = $project_apr_transferred;

            $params['下家本金'] = $investInfo['cbi_assign_fee'];
            $params['下家收益'] = $investInfo['pp_duein_money'] - $investInfo['cbi_assign_fee'];
            $params['下家持有时间'] = $next_last_days;
            $params['下家起息时间'] = TimeHelper::Today();
            $params['下家结息时间'] = $investInfo['pp_last_repay_date'];
            $params['下家利率'] = $new_project_apr;

            Yii::info(var_export($params,true));

            /** 记录NoticeSms 转让成功 JohnnyLin */
            //$curUser['username'] // 下家phone      $investInfo['user_name']  // 上家phone
            //$uid = $curUser['id']; // 下家uid    $destination_uid = $investInfo['invest_uid']; // 上家uid
            NoticeSms::instance()->init_sms_str($destination_uid,NoticeSms::NOTICE_ASSIGNED,array('project_name'=>$investInfo['project_name']));

            // 3.2 改变原来的收益记录状态
            $affected_rows = $db->createCommand()->update( ProjectProfits::tableName(), [
                'status' => ProjectProfits::STATUS_FULLY_ASSIGNED,
                'duein_money' => $investInfo['cbi_assign_fee'], // 应收的金额即为原用户的转让价格
                'duein_profits' => $duein_profits_transferred,  // 重新计算收益
                'last_repay_date' => $transfer_date, // 转让日期为最后计息日
                'project_apr' => $project_apr_transferred,
            ],[
                'id' => $investInfo['profit_id'],
                'status' => ProjectProfits::STATUS_ASSIGNING,
            ])->execute();

            if( empty($affected_rows) )
            {
                InvestException::throwCodeExt(1008,"({$investInfo['profit_id']})");
            }

            // 3.3 添加收益记录(下家)
            $profitsNewData = [
                'invest_id' => $new_invest_id,
                'project_id' => $investInfo['project_id'],
                'project_name' => $investInfo['project_name'],
                'project_apr' => $investInfo['cbi_assign_rate'], // 计算结果要和 $new_project_apr 一致
                'invest_uid' => $uid,
                'is_transfer' => 1,
                'profits_uid' => $uid,
                'duein_money' => $investInfo['pp_duein_money'],
                'duein_capital' => $investInfo['cbi_assign_fee'],
                'duein_profits' => $investInfo['pp_duein_money'] - $investInfo['cbi_assign_fee'],
                'interest_start_date' => TimeHelper::Today(),
                'last_repay_date' => $investInfo['pp_last_repay_date'],
                'status' => ProjectProfits::STATUS_SUCCESS,
                'ca_base_id' => $investInfo['ca_base_id'], // 债权转让表对应的记录ID
                'created_at' => TimeHelper::Now(),
                'updated_at' => TimeHelper::Now(),
            ];

            $affected_rows = $db->createCommand()->insert( ProjectProfits::tableName(), $profitsNewData )->execute();
            Yii::info("新插入收益记录:" . var_export($profitsNewData,true));
            if( empty($affected_rows) )
            {
                InvestException::throwCodeExt(1008);
            }

            // ------- 4. 修改账户余额 -------
            $curUserAccount = UserAccount::find()->where(['user_id' => $uid])->asArray()->one();
            if (empty($curUserAccount))
            {
                InvestException::throwCodeExt(1202);
            }

            // 总的扣款金额
            $transfer_money = $investInfo['cbi_assign_fee'];

            // 上家投资的本金
            $capital = $investInfo['pp_duein_capital'];

            // 当前剩余可用余额
            $usable_money = $curUserAccount['usable_money'];

            if( $transfer_money < $capital)
            {
                InvestException::throwCodeExt(1019);
            }

            // 上家的收益，需要累加到累计收益中去
            $profit = $transfer_money - $capital;


            // 流水前缀
            $account_flow_prefix = "债权转让{$invest_id}:";
            if ( $use_remain == 1 ) // 优先使用余额支付
            {
                // 如果当前余额足够使用
                if( $usable_money >= $transfer_money )
                {
                    // 4.1 转账方扣款：可用余额减少，待收本金增加，待收收益增加，累计收益增加
                    $sql = "update ". UserAccount::tableName() .
                        " SET usable_money = usable_money - {$transfer_money}, " .
                        " duein_capital = duein_capital + {$profitsNewData['duein_capital']}, " .
                        " duein_profits = duein_profits + {$profitsNewData['duein_profits']}, " .
                        " total_money = total_money - {$transfer_money},  ".
                        " updated_at = ". TimeHelper::Now() .
                        " WHERE usable_money >= {$transfer_money} ".
                        " AND total_money >= {$transfer_money}".
                        " AND user_id = {$uid}";

                    $affected_rows = $db->createCommand($sql)->execute();
                    if( $affected_rows != 1 )
                    {
                        InvestException::throwCodeExt(1201);
                    }

                    // 4.2 收款方收款：可用余额增加，待收本金减少，待收收益减少
                    $sql = "update ". UserAccount::tableName() .
                        " SET usable_money = usable_money + {$transfer_money}, " .
                        " duein_capital = duein_capital - {$investInfo['pp_duein_capital']}, " .
                        " duein_profits = duein_profits - {$investInfo['pp_duein_profits']}, " .
                        " total_money = total_money + {$transfer_money}, " .
                        " total_profits = total_profits + {$profit} " .
                        " WHERE user_id = {$destination_uid}";

                    $affected_rows = $db->createCommand($sql)->execute();
                    if( $affected_rows != 1 )
                    {
                        InvestException::throwCodeExt(1201);
                    }

                    // 4.3 添加流水
                    UserAccount::addLog(
                        $uid,
                        UserAccount::TRADE_TYPE_TRANSFER_USABLE_OUT,
                        $transfer_money,
                        $account_flow_prefix."{$uid}余额转账给{$destination_uid}"
                    );

                    UserAccount::addLog(
                        $destination_uid,
                        UserAccount::TRADE_TYPE_TRANSFER_USABLE_IN,
                        $transfer_money,
                        $account_flow_prefix."{$destination_uid}从{$uid}收入余额"
                    );

                    // 4.4 添加昨日收益
                    $db->createCommand()->insert( UserDailyProfits::tableName(),[
                        'date' => date("Y-m-d", TimeHelper::Now()),
                        'user_id' => $destination_uid,
                        'project_name' => $investInfo['project_name'],
                        'project_id' => $investInfo['project_id'],
                    ]);
                }
                else
                {
                    // 5.1 转账方银行扣款
                    $payService = Yii::$container->get('payService');
                    $pay_amount = $transfer_money - $usable_money;
                    $pay_src = "";
                    $userBank = UserBankCard::find()->where(['user_id' => $uid])->asArray()->one();
                    if(empty($userBank))
                    {
                        InvestException::throwCodeExt(1203);
                    }

                    $payRet = $payService->pay(
                        $curUser,
                        $pay_amount,
                        $pay_src
                    );

                    if(empty($payRet) or $payRet['code'] != 0)
                    {
                        InvestException::throwCodeExt(1204);
                    }

                    // 5.2 转账方扣款：可用余额减少到0,，待收本金增加，待收收益增加
                    $sql = "update ". UserAccount::tableName() .
                        " SET usable_money = usable_money - {$usable_money}, " .
                        " duein_capital = duein_capital + {$profitsNewData['duein_capital']}, " .
                        " duein_profits = duein_profits + {$profitsNewData['duein_profits']}, " .
                        " total_money = total_money - {$usable_money}, " .
                        " updated_at = ". TimeHelper::Now() .
                        " WHERE usable_money = {$usable_money}".
                        " AND total_money >= {$usable_money}".
                        " AND user_id = {$uid}"; // 更新时的 usable_money 金额与前面 select 出来的 usable_money 金额要一致

                    $affected_rows = $db->createCommand($sql)->execute();
                    if( $affected_rows != 1 )
                    {
                        InvestException::throwCodeExt(1201);
                    }

                    // 5.3 收款方收款：可用余额增加，待收本金减少，待收收益减少
                    $sql = "update ". UserAccount::tableName() .
                        " SET usable_money = usable_money + {$transfer_money}, " .
                        " duein_capital = duein_capital - {$investInfo['pp_duein_capital']}, " .
                        " duein_profits = duein_profits - {$investInfo['pp_duein_profits']}, " .
                        " total_profits = total_profits + {$profit}, " .
                        " total_money = total_money + {$transfer_money}, " .
                        " updated_at = ". TimeHelper::Now() .
                        " WHERE user_id = {$destination_uid}";

                    $affected_rows = $db->createCommand($sql)->execute();
                    if( $affected_rows != 1 )
                    {
                        InvestException::throwCodeExt(1201);
                    }

                    // 5.4 添加流水
                    UserAccount::addLog( // 对应5.1
                        $uid,
                        UserAccount::TRADE_TYPE_TRANSFER_CARD_OUT,
                        $pay_amount,
                        $account_flow_prefix."{$uid}银行卡支付给{$destination_uid}"
                    );

                    UserAccount::addLog( // 对应5.2
                        $uid,
                        UserAccount::TRADE_TYPE_TRANSFER_USABLE_OUT,
                        $usable_money,
                        $account_flow_prefix."{$uid}余额支付给{$destination_uid}"
                    );

                    UserAccount::addLog( // 对应5.3
                        $destination_uid,
                        UserAccount::TRADE_TYPE_TRANSFER_USABLE_IN,
                        $transfer_money,
                        $account_flow_prefix."{$destination_uid}从{$uid}收到金额"
                    );
                }
            }
            else // 只使用银行卡支付
            {
                // 6.1 转账方银行扣款
                $payService = Yii::$container->get('payService');
                $pay_amount = $transfer_money;
                $pay_src = "";
                $userBank = UserBankCard::find()->where(['user_id' => $uid])->asArray()->one();
                if(empty($userBank))
                {
                    InvestException::throwCodeExt(1203);
                }

                $payRet = $payService->pay(
                    $curUser,
                    $pay_amount,
                    $pay_src
                );

                if(empty($payRet) or $payRet['code'] != 0)
                {
                    InvestException::throwCodeExt(1204);
                }

                // 6.2 转账方扣款：可用余额不改，待收本金增加，待收收益增加
                $sql = "update ". UserAccount::tableName() .
                    " SET " .
                    " duein_capital = duein_capital + {$profitsNewData['duein_capital']}, " .
                    " duein_profits = duein_profits + {$profitsNewData['duein_profits']}, " .
                    " updated_at = ". TimeHelper::Now() .
                    " WHERE user_id = {$uid}"; // 更新时的 usable_money金额与前面select出来的usable_money金额要一致

                $affected_rows = $db->createCommand($sql)->execute();
                if( $affected_rows != 1 )
                {
                    InvestException::throwCodeExt(1201);
                }

                // 6.3 收款方收款：可用余额增加（相当于从转让方的银行卡加入到收款方），待收本金减少，待收收益减少，总资产增加
                $sql = "update ". UserAccount::tableName() .
                    " SET usable_money = usable_money + {$pay_amount}, " .
                    " duein_capital = duein_capital - {$investInfo['pp_duein_capital']}, " .
                    " duein_profits = duein_profits - {$investInfo['pp_duein_profits']}, " .
                    " total_profits = total_profits + {$profit}, " .
                    " total_money = total_money + {$pay_amount}, " .
                    " updated_at = ". TimeHelper::Now() .
                    " WHERE user_id = {$destination_uid}";

                $affected_rows = $db->createCommand($sql)->execute();
                if( $affected_rows != 1 )
                {
                    InvestException::throwCodeExt(1201);
                }


                UserAccount::addLog( // 对应6.1
                    $uid,
                    UserAccount::TRADE_TYPE_TRANSFER_CARD_OUT,
                    $pay_amount,
                    $account_flow_prefix."{$uid}银行卡支付给{$destination_uid}"
                );

                UserAccount::addLog( // 对应6.3
                    $destination_uid,
                    UserAccount::TRADE_TYPE_TRANSFER_USABLE_IN,
                    $pay_amount,
                    $account_flow_prefix."{$destination_uid}从{$uid}收到金额"
                );
            }

            // 成功提交
            $transaction->commit();

            // 添加记录流水
            $invest_result = [
                'invest' => [
                    'project_type_desc' => Project::$typeList[$investInfo['p_type']],
                    'project_name' => $profitsNewData['project_name'],
                    'apr' => $profitsNewData['project_apr'],
                    'invest_money' => $profitsNewData['duein_capital'],
                    'date' => date("Y-m-d H:i",time()),
                ],
                'start' => [
                    'date' => "{$profitsNewData['interest_start_date']}",
                    'desc' => "开始计算收益",
                ],
                'end' => [
                    'date' => "{$profitsNewData['last_repay_date']}",
                    'desc' => "收益到账",
                ],
            ];

        }
        catch(Exception $e)
        {
            $transaction->rollBack();
            throw $e;
        }


        return $invest_result;
    }

    public function cancelAssignment( $invest_id )
    {
        // 开启事务
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        $caseBaseInfo = [];
        try{

            // ------- 1. 项目投资状态修改 -------
            $affected_rows = $db->createCommand()->update( ProjectInvest::tableName(),[
                'status' => ProjectProfits::STATUS_SUCCESS,
            ],[
                'id' => $invest_id,
                'status' => ProjectProfits::STATUS_ASSIGNING
            ])->execute();

            if(empty($affected_rows))
            {
                InvestException::throwCodeExt(1013);
            }

            // ------- 2. 项目收益表修改 -------
            $affected_rows = $db->createCommand()->update( ProjectProfits::tableName(),[
                'status' => ProjectProfits::STATUS_SUCCESS,
            ],[
                'invest_id' =>$invest_id,
                'status' => ProjectProfits::STATUS_ASSIGNING,
            ])->execute();

            if(empty($affected_rows))
            {
                InvestException::throwCodeExt(1009);
            }

            // ------- 3. 更新转让项目状态 -------
            $affected_rows = $db->createCommand()->update( CreditBaseInfo::tableName(),[
                'status' => CreditBaseInfo::STATUS_ASSIGN_CANCEL
            ],[
                'invest_id' => $invest_id,
                'status' => CreditBaseInfo::STATUS_ASSIGNING,
            ])->execute();

            if(empty($affected_rows))
            {
                InvestException::throwCodeExt(1012);
            }

            // 提交事务
            $transaction->commit();

            $caseBaseInfo = CreditBaseInfo::find()->select([
                'id',
                'project_type',
                'project_id',
                'invest_id',
                'assign_start_date',
                'assign_end_date',
                'assign_fee',
                'assign_rate',
                'user_id',
                'user_name',
                'commission_rate',
                'status',
            ])->where([
                'invest_id' => $invest_id,
            ])->asArray()->one();

        }
        catch (Exception $e)
        {
            $transaction->rollBack();
            throw $e;
        }

        return $caseBaseInfo;
    }
}
