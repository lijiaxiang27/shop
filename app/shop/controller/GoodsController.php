<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/19/019
 * Time: 11:49
 */

namespace app\shop\controller;


use app\shop\model\AttrModel;
use app\shop\model\CategoryModel;
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
        //获取商品 每页20条
        $goods = $this -> model -> getGoods(20);
        //获取商品分类
        $cate  = $this -> model -> getCategory();

        $this -> assign('cate',$cate);
        $this -> assign('goods',$goods);
        $this -> assign('page',$goods -> render());
        return $this -> fetch();
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
//            $this -> model -> addGoods();die;
//            dump($request->param());die;
           if($this -> model -> addGoods())
           {
               $this -> success('添加成功');
           }else{
               $this -> error('添加失败');
           }

        }
    }


    public function  save(Request $request)
    {
        if($request -> isGet())
        {
            $id = $request -> param('goods_id');
            $goods = $this -> model -> getDetails($id);
            $cate  = self::_get_category();
            //获取商品属性
            $attr  = $this -> model -> getGoodsAttr($id);
            //获取当前商品分类下的所有规格属性
            $all   = $this -> ajax_attribute($goods['category_id'],true);
            $attr  = self::_do_goodsAttr($attr,json_decode($all,true));
            unset($all);

            $this -> assign('cate',$cate);
            $this -> assign('good',$goods);
            $this -> assign('attr',$attr);
            return $this -> fetch();
        }

        if ($request -> isPost())
        {
            $param = $request -> param();
            dump($param);
        }

    }

    private static function _do_goodsAttr($attr,$all)
    {
        $arr = array();
        foreach ($all as $k => $v)
        {
            foreach ($v as $key => $val)
            {
                //循环判断商品是否存在此属性
                foreach ($attr as $i => $item)
                {
                    if ($item['attr_id'] == $val['attr_id'])
                    {
                        $all[$k][$key] = $item;
                        $all[$k][$key]['stu'] = 1;
                        break ;
                    }
                }
                if (!key_exists('stu',$all[$k][$key]))
                {
                    $all[$k][$key]['stu'] = 2;
                }

            }
        }
        return $all;
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
    public function ajax_attribute($cid = 0,$stu=false)
    {
        if ($cid == 0){
            $cid = Request::instance()->param('cid');
        }

        //获取当前分类下的规格ID
        $ids = Db::name('category')
            -> field('tids')
            -> where('category_id',$cid)
            -> find();
        //根据规格ID 获取规格及属性
        $model = new AttrModel();
        $attr  = $model -> getType('attr_type in('.$ids['tids'].')');
        if ($stu){
            return json_encode($attr);
        }else{
            return json($attr);die;
        }

    }

    //获取分类
    private  static function _get_category()
    {
        return Db::name('category') -> field('pid,tids',true) -> select();
    }




}