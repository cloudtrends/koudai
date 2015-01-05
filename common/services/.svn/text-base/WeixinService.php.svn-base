<?php
/**
 * Created by PhpStorm.
 * User: haoyu
 * Date: 2014/11/10
 * Time: 16:06
 */

namespace common\services;

use yii;
use yii\base\Object;
use yii\helpers\Url;
use mobile\components\ApiUrl;
use mobile\controllers\WeixinapiController;


class WeixinService extends Object{

    // 账户信息
    public function accountInformation ($openid){
        if (empty($openid)){
            Yii::error('open is null ..');
            return "需要在微信中浏览";
        }
        $url = ApiUrl::toRoute(['account/home', 'contact_id'=>$openid],true);
        $result = json_decode(WeixinapiController::postData($url),true);

        $r = $this->check_is_login($result,$openid);
        if ($r){
            return $r;
        }

        $lastday_profits_date = date('Y-m-d', $result['data']['lastday_profits_date']);
        $lastday_profits = sprintf('%.2f',$result['data']['lastday_profits'] / 100);
        $total_profits = sprintf('%.2f',$result['data']['total_profits'] / 100);
        $total_money = sprintf('%.2f',$result['data']['total_money'] / 100);
        $hold_money = sprintf('%.2f',$result['data']['hold_money'] / 100);
        $remain_money = sprintf('%.2f',$result['data']['remain_money'] / 100);
        $trade_count = $result['data']['trade_count'];
        $m = "账户信息: \n日期: $lastday_profits_date\n昨日收益:$lastday_profits 元\n总收益:$total_profits 元\n总金额: $total_money 元\n持有资产:$hold_money 元\n剩余金额:$remain_money 元\n交易次数:$trade_count 次";
        return $m;
        // return $this->init_str($result['data'],$this->user_info_title());
    }


    // 交易记录
    public function accountTrades($openid){ 
        if (empty($openid)){
            Yii::error('open is null ..');
            return "需要在微信中浏览";
        }
        $url = ApiUrl::toRoute(['account/project-trades', 'contact_id'=>$openid],true);
        $results = json_decode(WeixinapiController::postData($url),true);

        $r = $this->check_is_login($results,$openid);
        if ($r){ return $r;  }

        if( $results['code'] != 0 or  empty($results['data']) )
        {
            $url = Url::toRoute(['site/index', 'OPENID'=>$openid],true);
            return "您最近，暂无交易记录，欢迎投资<a href='".$url."'>口袋宝</a>";
        }

        $result = $results['data']['0'];
        $name = $result['name'];
        $invest_money= sprintf('%.2f',$result['invest_money'] /100);
        $status= $result['status'];
        $created_at= date('Y-m-d',$result['created_at']);
        $m = "最新投资记录: \n项目名称:$name\n投资金额:$invest_money 元\n状态:$status\n时间:$created_at";
        return $m;
     
    }

    // 持有资产
    public function accountHold ($openid){
        if (empty($openid)){
            Yii::error('open is null ..');
            return "需要在微信中浏览";
        }
        $url = ApiUrl::toRoute(['account/hold', 'contact_id'=>$openid],true);
        $result = json_decode(WeixinapiController::postData($url),true);

        $r = $this->check_is_login($result,$openid);
        if ($r){ return $r;  }

        $total_hold_money =sprintf('%.2f',$result['data']['total_hold_money'] / 100);      
        $kdb_total_money = sprintf('%.2f',$result['data']['kdb_total_money'] / 100);
        $kdb_total_profits = sprintf('%.2f',$result['data']['kdb_total_profits'] / 100);
        $investing_money = sprintf('%.2f',$result['data']['investing_money'] / 100);
        $duein_capital = sprintf('%.2f',$result['data']['duein_capital'] / 100);
        $duein_profits = sprintf('%.2f',$result['data']['duein_profits'] / 100);
        $m = "持有资产:\n总持有资产:$total_hold_money 元\n口袋宝总额:$kdb_total_money 元\n口袋宝总收益: $kdb_total_profits 元\n申购中冻结金额:$investing_money 元\n待收本金:$duein_capital 元\n未结算收益:$duein_profits 元 ";
                 
        return $m;
    }

    //活动中心
    public function pageListactivity($openid){
        if (empty($openid)){
            Yii::error('open is null ..');
            return "需要在微信中浏览";
        }
        $url = ApiUrl::toRoute(['page/list-activity', 'contact_id'=>$openid],true);
        $result = json_decode(WeixinapiController::postData($url),true);

        $r = $this->check_is_login($result,$openid);
        if ($r){ return $r;  }

        $index = 1;
        $msg = "活动列表:\n";
        if (!empty($result['activityList'])){
            foreach ($result['activityList'] as $key => $val){
                $url = Url::toRoute(['page/activity-detail', 'id'=>$val['id']],true);
                $msg .= $index . "." . "<a href='".$url."'>" . $val['title'] . "</a> \n";
                $index++ ;
            }
        }
        return $msg;
    }

    //活动中心
    public function pageListactivitybyNews($openid){
        if (empty($openid)){
            Yii::error('open is null ..');
            return "需要在微信中浏览";
        }
        $url = ApiUrl::toRoute(['page/list-activity', 'contact_id'=>$openid],true);
        $result = json_decode(WeixinapiController::postData($url),true);
        $r = array();
        $r['content'] = 'ceshiceshieryi';
        if (!empty($result['activityList'])){
            foreach ($result['activityList'] as $key => $val){
                $url = Url::toRoute(['page/activity-detail', 'id'=>$val['id']],true);
                $r['items'][$key]['title']= $val['title'];
                $r['items'][$key]['description']= $val['title'];
                $r['items'][$key]['url']= $url;
                $r['items'][$key]['picurl'] = $val['thumbnail'];
            }
        }
        return $r;
    }



    private function check_is_login($result,$openid){
        $bool = '' ;
        //表示未登录
        if (isset($result['code']) && $result['code'] == -2 ){
            $str = "口袋君终于等到您啦!\n1.精品推荐助您第一时间查看最新优质投资项目;\n2.活动中心让您不错过与口袋君一期一会;\n3.我的口袋帮您随时随地对接会员专享服务;\n艾玛，收益太高，不敢看！小伙伴，要先绑定<a href=\"".Url::toRoute(['site/login', 'OPENID'=>$openid],true)."\" >口袋理财账户</a>哦！";
            $bool = $str ;//. "<a href='".Url::toRoute(['app-login/login', 'OPENID'=>$openid],true)."'>点我登录</a>"
        }
        return $bool;
    }

    /***  **************************************  自动回复 数据 获取 拼接 方法 START   **************************************  **/
   

    //账户信息
    private function user_info_title(){        
        return array('lastday_profits_date'=>'昨天日期',
            'lastday_profits'=>'昨日收益',
            'total_profits'=>'总收益',
            'total_money'=>'总金额',
            'hold_money'=>'持有资产',
            'remain_money'=>'剩余金额',
            'trade_count'=>'交易次数'
        );
    }

    //持有资产
    private function assets_title(){
        return array('total_hold_money'=>'总持有资产',
            'kdb_total_money'=>'口袋宝总额',
            'kdb_total_profits'=>'口袋宝总收益',
            'investing_money'=>'申购中冻结金额',
            'duein_capital'=>'待收本金',
            'duein_profits'=>'未结算收益'
        );
    }

    /**
     * 信息回复 字符串重组
     * @param $data
     * @return string
     */
    private function init_str($data,$title){
        if (empty($data) || empty($title)){
            return '暂无信息';
        }
        $msg = '';
        foreach ($title as $info_title_Key => $info_title_Val){
            if (!empty($data[$info_title_Key])){
                $msg .= $info_title_Val .' : '. $data[$info_title_Key] . "\n";
            }else{
                $msg .= $info_title_Val ." : \n";
            }
        }
        return $msg;
    }


    /*** ************************ 自动回复 数据 获取 拼接 方法 END ************************************** **/

}