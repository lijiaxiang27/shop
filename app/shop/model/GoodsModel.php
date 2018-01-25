<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/22/022
 * Time: 17:38
 */

namespace app\shop\model;


use app\portal\controller\SearchController;
use think\Db;
use think\Model;
use think\Request;

class GoodsModel extends Model
{
    /**
     * 获取全部商品
     * @param int $limit
     * @return \think\Paginator
     */
    public function getGoods($limit = 20)
    {
        //获取商品
        $goods =  $this
            -> field('goods_content,goods_img',true)
            -> where('goods_stu',1)
            -> order('goods_order,goods_id','desc')
            -> paginate($limit);

        return $goods;
    }

    /**
     * 获取全部分类
     * @return mixed   array （ID => name）一维数组
     */
    public function getCategory()
    {
        $data = Db::name('category')
            -> field('category_id,category_name')
            -> select();

        foreach ($data as $k => $v)
        {
            $cate[$v['category_id']] = $v['category_name'];
        }

        return $cate;
    }

    /**
     * 添加商品
     * @return bool
     */
    public function addGoods($gid)
    {
        $param = Request::instance()->param();
        $goods = $param["goods"];
        if (isset($gid))
        {
            $goods['goods_id'] = $gid;
        }
        $goods['create_time'] = date('Y-m-d H:i:s',time());
        //处理sku
        foreach ($param['attr'] as $k => $v)
        {
            $attr[$k]['attr_id'] = $v;
            $attr[$k]['sell_price'] = $param['attr_price'][$v];
            $attr[$k]['goods_number'] = $param['goods_number'][$v];
        }
        //处理上传的商品相集
        if(!isset($param['photo_urls'])||empty($param['poto_urls']))
        {
            //TODO 未上传图片时 默认的图片
            $images[] = 'shop/20180122/7c25961c435ed33bec195532fbbd6003.jpg';
        }else{
            $images = $param['photo_urls'];
        }
        $goods['goods_img'] = implode(';',$images);
        $stu = true;
        //开启事务
        $this -> startTrans();
        //商品入库
        $stu = $this -> insert($goods)?$stu:false;
        //sku数据处理 && 入库
        $gid = $this -> getLastInsID();
        foreach ($attr as $k => $v)
        {
            $attr[$k]['g_id'] = $gid;
        }
        $stu = Db::name('goods_attribute') -> insertAll($attr)!==false?$stu:false;
        //提交 or 回滚
        $stu?$this -> commit():$this ->rollback();

        return $stu;

    }
    public function doDel($gid = 0,$type=false)
    {
        if ($gid ==0){
            $gid = Request::instance() -> param('goods_id');
        }

        $stu = true;
        //开启事务
        $this -> startTrans();
        $stu = $this -> where('goods_id',$gid) -> delete()?$stu:false;
        $stu = Db::name('goods_attribute') -> where('g_id',$gid) -> delete()!==false?$stu:false;
        if ($type)
        {
            $stu = $this->addGoods($gid)!==false?$stu:false;
        }
        $stu?$this -> commit():$this -> rollback();

        return $stu;
    }

   public function getDetails($gid)
   {
       //返回
       return $this -> find($gid);
   }

    /**
     * 获取商品延展属性
     * @param $gid
     * @return false|\PDOStatement|string|\think\Collection
     */
   public function getGoodsAttr($gid)
   {
       //获取商品延展的属性
       return Db::name('goods_attribute ga')
           ->field('`id`,`goods_number`,`sell_price`,ga.attr_id,a.attr_name')
           -> join('attribute a','ga.attr_id = a.attr_id','left')
           -> where('ga.g_id',$gid)
           -> select();
   }



}