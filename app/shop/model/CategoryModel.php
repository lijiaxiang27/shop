<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/19/019
 * Time: 10:04
 */

namespace app\shop\model;

use think\Db;
use think\Model;
use think\Request;

class CategoryModel extends Model
{

    /**
     * 获取分类数据
     * TODO 暂不考虑无限级递归分类
     * @return array
     */
    public function get_category()
    {
       $category =   $this->select();
        $type = Db::name('attribute_type') -> select();

        foreach ($type as $key => $value) {
            $n_type[$value['type_id']] = $value['type_name'];
        }
        foreach ($category as $k => $v) {
            $ids = explode(',',$v['tids']);
            $arr = array();
            foreach ($ids as $value)
            {
                if ($value=='') continue;
                $arr[] = $n_type[$value];
            }
            $category[$k]['tids']=implode(',',$arr);
        }
       return $category;
    }

    public function insert_category()
    {
        $param = Request::instance() -> param();
        $param['tids'] = implode(',',$param['tids']);
        return $this -> insert($param);
    }

    public function del_category()
    {
        $param = Request::instance()->param();

        return  $this ->where($param)->delete();

    }




}