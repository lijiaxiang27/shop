<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/20/020
 * Time: 11:31
 */

namespace app\shop\model;


use think\Db;
use think\Model;
use think\Request;

class AttrModel extends Model
{
    protected  $name = 'attribute';

    /**
     * 规格添加
     * @return bool
     */
    public function setType()
    {
        //初步处理参数
        $param['type_name'] = Request::instance()-> param('type_name');
        $attr = Request::instance() -> param('attr_name');
        $attr = str_replace('；',';',$attr);
        $attr = explode(';',$attr);

        $stu =true;
        //开始一个事务
        $this -> startTrans();

        //将类型 如果 并获取其ID
        $stu = Db::name('attribute_type')-> insert($param)?$stu:false;
        $type_id = Db::name('attribute_type') -> getLastInsID();
        //对数据做最终处理
        $param = $this -> _doArray($attr,$type_id);
        //入库
        $stu = $this->insertAll($param)?$stu:false;

        //提交or回滚事务
        $stu?$this->commit():$this -> rollback();

        return $stu;
    }

    /**
     * 获取规格
     * @return array
     */
    public function getType($where = '')
    {
        $type = $this ->field('attr_name,type_id,type_name,attr_id')
            ->where($where)
            -> join('cmf_attribute_type t','attr_type =  t.type_id','right')
            -> select();

        $data = array();

        foreach ($type as  $k => $v)
        {
            $data[$v['type_name']][$k]['attr_name'] = $v['attr_name'];
            $data[$v['type_name']][$k]['type_id'] = $v['type_id'];
            $data[$v['type_name']][$k]['attr_id'] = $v['attr_id'];
        }

        $new_data = array();
        foreach ($data as $k => $v)
        {
            foreach ($v as  $val)
            {
                $new_data[$k][] = $val;
            }
        }

        return $new_data;
    }

    /**
     * 修改规格获取
     */
    public function getSave()
    {
        //获取参数
        $id = Request::instance()->param('attr_type');

        $attr = $this-> where('attr_type',$id) -> select();
        if (!empty($attr->toArray()))
        {
            //根据类型获取属性
            $type = Db::name('attribute_type')->field('type_name')
                -> where('type_id',$id)
                -> find();
            //处理数据
            $str = '';
            foreach ($attr as $v)
            {
                $str .=$v['attr_name'].';';
            }
            $type['attr']=$str;
            $type['id'] = $attr[0]['attr_type'];
        }else{
            $type = Db::name('attribute_type')->field('type_name')
                -> where('type_id',$id)
                -> find();
            $type['attr']='';
            $type['id'] = $id;
        }

        return $type;
    }

    /**
     * 修改规格动作
     */
    public function doSave()
    {
        //获取参数
        $param = Request::instance() -> param();

        //开启事务
        $this -> startTrans();
        $stu = true;

        //类型表数据处理
        $type['type_name'] = $param['type_name'];
        $type['type_id'] = $param['type_id'];

        $stu = Db::name('attribute_type') -> update($type)!==false?$stu:false;

        $stu = $this -> where('attr_type',$param['type_id']) -> delete()!==false?$stu:false;
        $attr = Request::instance() -> param('attr_name');
        if ($attr!='')
        {
            $attr = str_replace('；',';',$attr);
            $attr = explode(';',$attr);
            $param = $this -> _doArray($attr,$param['type_id']);
            $stu = $this->insertAll($param)?$stu:false;
        }
        // commit or rollback
        $stu?$this->commit():$this->rollback();
        return $stu;
    }

    /**
     * 删除动作
     */
    public function delType()
    {
        $id = Request::instance() -> param('attr_type');
        $stu = true;
        $this -> startTrans();
        $stu = $this -> where('attr_type',$id) ->  delete()?$stu:false;
        $stu = Db::name('attribute_type') -> where('type_id',$id) -> delete()?$stu:false;
        $stu?$this->commit():$this->rollback();
        return $stu;
    }
    /**
     * 将字符串转换为数组 并处理为可以插入数据表的格式
     * @param $data
     * @param $id
     */
    private function _doArray($data,$id)
    {
        //对数据做最终处理
        $param = array();
        foreach ($data as $k => $v)
        {
            //如果为空则跳过此次遍历
            if ($v=='') continue;
            $param[$k]['attr_type'] = $id;
            $param[$k]['attr_name'] = $v;
        }
        return $param;
    }
}