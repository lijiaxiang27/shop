<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/20/020
 * Time: 10:57
 */

namespace app\shop\controller;


use app\shop\model\AttrModel;
use cmf\controller\AdminBaseController;
use think\Db;
use think\Request;

class AttrController extends AdminBaseController
{
    protected $model;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this -> model = new AttrModel();
    }

    /**
     * 展示页
     * @return mixed
     */
    public function type_index()
    {
        //获取数据
        $attr = $this -> model -> getType();

        $this -> assign('data',$attr);

        return $this -> fetch();
    }

    /**
     * 规格添加
     * @param Request $request
     * @return mixed
     */
    public function type_add(Request $request)
    {
        //get动作
        if ($request -> isGet())
        {
            return $this -> fetch();
        }

        //post动作
        if ($request -> isPost())
        {
           // $this -> model -> setType();die;
            //入库处理
            if($this -> model -> setType())
            {
                $this -> success('添加成功');
            }else{
                $this -> error('添加失败');
            }


        }
    }

    /**
     * 修改
     * @param Request $request
     * @return mixed
     */
    public function type_save(Request $request)
    {
        if ($request -> isGet())
        {
            $data = $this -> model -> getSave();

            $this -> assign('type',$data);
            return $this -> fetch();
        }

        if ($request -> isPost())
        {
            //$this -> model -> doSave();die;
            if($this -> model -> doSave())
            {
                $this -> success('修改成功');
            }else{
                $this -> error('修改失败');
            }
        }
    }

    public function type_del()
    {
        if ($this->model->delType())
        {
            $this -> success('删除成功');
        }else{
            $this -> error('删除失败');
        }

    }

}