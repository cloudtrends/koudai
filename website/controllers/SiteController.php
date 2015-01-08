<?php
namespace website\controllers;
use common\models\Project;
use yii\db\Query;
use yii\data\Pagination;
use Yii;
use yii\widgets\LinkPager;

class SiteController extends BaseController
{

	//首页
	public function actionIndex()
	{
		$title = '口袋理财-首页';
		return  $this->render('index', [
			'title' => $title,
		]);
	}

	//登录页
	public function actionLogin()
	{
		$title = '口袋理财-登录';
		return  $this->render('login', [
			'title' => $title,
		]);
	}

	//注册页
	public function actionRegister()
	{
		$title = '口袋理财-注册';
		return  $this->render('register', [
			'title' => $title,
		]);
	}

//        列表页
        public function actionList()
	{
            $title_head_ch = array('type'=>'项目类型',
                        'status' => '项目状态',
                        'period' => '项目期限',
                        'apr' => '项目利率',
                    );
            
		$title = '口袋理财-列表页';
                $search = array('type'=>array(0=>'全部项目',1=>'安稳贷',2=>'金融贷'),
                                'status'=>array(0=>'全部项目',3=>'投资中',5=>'投资结束',7=>'还款中',8=>'还款完成'),
                                'period'=>array(0=>'全部项目',1=>'少于一个月',2=>'1-3个月',3=>'3-6个月',4=>'6-12个月',5=>'一年以上'),
                                'apr'=>array(0=>'全部项目',1=>'8%-10%',2=>'10%-12%',3=>'12%以上')
                    
                );


		return  $this->render('list', [
			'title' => $title,
                        'search'=> $search,
                        'title_ch'=>$title_head_ch,
                        
                     
		]);
	}
        	//详情页
	public function actionDetail()
	{
           
              $id=$_GET['id'];
              
		$title = '口袋理财-详情';
		return  $this->render('detail', [
			'title' => $title,
                        'id'=>$id,
		]);
	}



        public function actionAjax(){
            $page_info = self::paging($_POST['pages'],$_POST['cur']);
            return json_encode(self::page_show($_POST['url'],$page_info));
        }


    /**
     * 分页功能
     * @author Johnnylin
     * @return Array(
     *      'final'=>$finalPage,   最后页
     *      'prev'=>$previousPage, 前一页
     *      'next'=>$nextPage,     后一页
     *      'init'=>$init,         初始页（第一页）
     *      'cur'=>$currentPage,   当前页
     *      'size'=>$page_size,    每页显示条目数
     *      'data_count'=>$data_count) 数据总条目数
     *
     * @param type $page_size     每页显示条目数
     * @param type $data_count    数据总条目数
     * @param type $currentPage   当前页
     */
    public static function paging( $data_count, $currentPage=1, $page_size=9){
        $finalPage = intval($data_count/$page_size);
        if($data_count % $page_size !=0){
            $finalPage = intval($data_count/$page_size+1);
        }
        $init=1;
        $previousPage = $currentPage-1;
        if($previousPage == 1 || $previousPage <= 0){
            $previousPage = 1;
        }
        $nextPage = $currentPage+1;
        if($nextPage == $finalPage || $nextPage == $finalPage+1){
            $nextPage = $finalPage;
        }
        return array('final'=>$finalPage, 'prev'=>$previousPage, 'next'=>$nextPage
        ,'init'=>$init, 'cur'=>$currentPage, 'size'=>$page_size,'data_count'=>$data_count);
    }

    /**
     * 页面显示分页的组件
     * @author Johnnylin
     * @param type $request_uri URL地址
     * @param type $page_info   分页数据内容* @return Array(
     *      'final'=>$finalPage,   最后页
     *      'prev'=>$previousPage, 前一页
     *      'next'=>$nextPage,     后一页
     *      'init'=>$init,         初始页（第一页）
     *      'cur'=>$currentPage,   当前页
     *      'size'=>$page_size,    每页显示条目数
     *      'data_count'=>$data_count) 数据总条目数
     * @param type $show_num   显示页数的长度
     * @param type $start_num   显示页数的数量
     * @return string
     */
    public static function page_show($request_uri = '' , $page_info = array(),$show_num = 10,$start_num = 5 ){
        $html = '';
        if (empty($page_info) || empty($request_uri)){
            return $html;
        }

        if (empty($page_info['final'])){
            return $html;
        }
        $html .= '<ul class="pagination">';
        if ($page_info['cur'] == 1){
            $html .=  '<li class="prev disabled"><span>«</span></li>';
        }else{
            $html .=  '<li class="prev"><li class="next"><a href="javascript:search(\'page\',\''.$page_info['prev'].'\')" data-page="1">«</a></li>';

        }

        /** 我是怎么想的？! */
        if ($page_info['final'] <= $show_num){
            for($i=1;$i <= $page_info['final'];$i++){
                if ($page_info['cur'] == $i){
                    $html .= '<li class="active"><a href="javascript:search(\'page\',\''.$i.'\')" data-page="'.intval($i-1).'">'.$i.'</a></li>';
                }else{
                    $html .= '<li ><a href="javascript:search(\'page\',\''.$i.'\')" data-page="'.intval($i-1).'">'.$i.'</a></li>';
                }
            }
        }else{
            $page = empty($page_info['cur']) ? 1 : $page_info['cur'];
            if (intval($page_info['final'] - $page + 1) >= $show_num){
                $page = ($page_info['cur'] - $start_num ) > 0 ? ($page_info['cur'] - $start_num )  : 1 ;
            }else{
                $page = $page_info['final'] - $show_num + 1 ;
            }

            for ($i = $page ; $i < ( $page + $show_num ) ; $i++){
                if ($page_info['cur'] == $i){
                    $html .= '<li class="active"><a href="javascript:search(\'page\',\''.$i.'\')" data-page="'.intval($i-1).'">'.$i.'</a></li>';
                }else{
                    $html .= '<li ><a href="javascript:search(\'page\',\''.$i.'\')" data-page="'.intval($i-1).'">'.$i.'</a></li>';
                }
            }
        }

        if ($page_info['cur'] == $page_info['final']){
            $html .= '<li class="next disabled"><span>»</span></li>';
        }else{
            $html .= '<li class="next"><a href="javascript:search(\'page\',\''.$page_info['next'].'\')" data-page="1">»</a></li>';
        }
        $html .= '</ul>';

        return $html;
    }
}