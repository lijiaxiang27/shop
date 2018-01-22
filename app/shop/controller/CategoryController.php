<?php
/**
 * 分类处理
 * Created by PhpStorm.
 * User: lijiaxiang
 * Date: 2018/1/19/019
 * Time: 9:42
 */

namespace app\shop\controller;


use app\shop\model\CategoryModel;
use cmf\controller\AdminBaseController;
use think\Db;
use think\Request;

class CategoryController extends  AdminBaseController
{
    protected $model;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this -> model = new CategoryModel();
    }

    /**
     * 展示列表
     * @return mixed
     */
    public function index()
    {
        $category = $this -> model -> get_category();
        $this -> assign('category',$category);
        return $this -> fetch();
    }

    /**
     * 新增分类
     * @param Request $request
     * @return mixed
     */
    public function add(Request $request)
    {
        if ($request -> isGet())
        {
            $type = self::_get_type();
            $this -> assign('type',$type);
            return $this -> fetch();
        }

        if ($request -> isPost())
        {
            if($this -> model -> insert_category())
            {
                $this -> success('添加成功');
            }else{
                $this -> error('添加失败');
            }

        }

    }

    /**
     * 修改动作
     * @param Request $request
     * @return mixed
     */
    public function save(Request $request)
    {
        if ($request->isGet())
        {
            //获取请求修改的数据
            $data = CategoryModel::get($request->param());
            $type = self::_get_type();

            $this -> assign('type',$type);
            $this -> assign('data',$data);

            return $this -> fetch();
        }

        if ($request -> isPost())
        {
            $param = $request->param();
            $param['tids']=implode(',',$param['tids']);
            //修改数据
            if (CategoryModel::update($param)!==false)
            {
                $this -> success('修改成功');
            }else{
                $this -> error('修改失败');
            }

        }
    }

    /**
     * 删除动作
     */
    public function del()
    {
        if($this -> model -> del_category())
        {
            $this -> success('删除成功');
        }else{
            $this -> error('删除失败');
        }

    }

    private static function _get_type()
    {
        return Db::name('attribute_type') -> select();
    }

}