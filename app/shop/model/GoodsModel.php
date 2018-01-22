<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/22/022
 * Time: 17:38
 */

namespace app\shop\model;


use think\Db;
use think\Model;
use think\Request;

class GoodsModel extends Model
{
    public function getGoods()
    {
        //获取所有商品
        $goods =  $this -> field('goods_content,goods_img',true) -> where('goods_stu',1) -> paginate();

        return $goods -> toArray();
    }

    /**
     * 添加商品
     * @return bool
     */
    public function addGoods()
    {
        $param = Request::instance()->param();
        $goods = $param["goods"];
        $goods['create_time'] = date('Y-m-d H:i:s',time());
        $c_id  = $param['goods_category'];
        //处理sku
        foreach ($param['attr'] as $k => $v)
        {
            $attr[$k]['attr_id'] = $v;
            $attr[$k]['sell_price'] = $param['attr_price'][$v];
            $attr[$k]['goods_number'] = $param['goods_number'][$v];
        }
        //处理上传的商品相集
        if(isset($param['photo_urls']))
        {
            //TODO 未上传图片时 默认的图片
            $images[] = '"shop/20180122/7c25961c435ed33bec195532fbbd6003.jpg';
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
        $stu = Db::name('goods_attribute') -> insertAll($attr)?$stu:false;
        //提交 or 回滚
        $stu?$this -> commit():$this ->rollback();

        return $stu;

    }
}