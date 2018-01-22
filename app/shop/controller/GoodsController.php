<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/19/019
 * Time: 11:49
 */

namespace app\shop\controller;


use app\shop\model\AttrModel;
use app\shop\model\GoodsModel;
use cmf\controller\AdminBaseController;
use think\Db;
use think\Request;

class GoodsController extends AdminBaseController
{
    protected  $model;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this -> model = new  GoodsModel();
    }

    public function index()
    {
        dump($this -> model -> getGoods());
//        return $this -> fetch();
    }

    /**
     * 添加商品
     * @param Request $request
     * @return mixed
     */
    public function  add(Request $request)
    {
        if ($request -> isGet())
        {
            $category = self::_get_category();
            //获取分类
            $this -> assign('category',$category);
            return $this -> fetch();
        }
        if ($request -> isPost())
        {

           if($this -> model -> addGoods())
           {
               $this -> success('添加成功');
           }else{
               $this -> error('添加失败');
           }

        }
    }

    public function  save()
    {
        echo 'add';
    }
    public function  del()
    {
        echo 'add';
    }

    public function get_detailed()
    {
        echo '查看详情';
    }

    /**
     * ajax获取分类对应的属性
     * @param Request $request
     * @return \think\response\Json
     */
    public function ajax_attribute(Request $request)
    {
        $cid = $request->param('cid');
        //获取当前分类下的规格ID
        $ids = Db::name('category')
            -> field('tids')
            -> where('category_id',$cid)
            -> find();
        //根据规格ID 获取规格及属性
        $model = new AttrModel();
        $attr  = $model -> getType('attr_type in('.$ids['tids'].')');
        return json($attr);

    }
    //获取分类
    private  static function _get_category()
    {
        return Db::name('category') -> field('pid,tids',true) -> select();
    }
}