<?php
// +----------------------------------------------------------------------
// | WaitShop开源电商系统 [ 助力中小企业快速落地 ]
// +----------------------------------------------------------------------
// | 欢迎阅读学习程序代码
// | gitee:   https://gitee.com/wafts/WaitShop
// | github:  https://github.com/topwait/waitshop
// | 官方网站: https://www.waitshop.cn
// +----------------------------------------------------------------------
// | 禁止对本系统程序代码以任何目的、任何形式再次发布或出售
// | WaitShop并不是自由软件,未经许可不能去掉WaitShop相关版权
// | WaitShop系统产品必须购买商业授权后,方可去除前后端官方标识
// | WaitShop团队版权所有并拥有最终解释权
// +----------------------------------------------------------------------
// | Author: WaitShop Team <2474369941@qq.com>
// +----------------------------------------------------------------------

namespace app\common\model\goods;


use app\common\basics\Models;
use app\common\utils\UrlUtils;

class GoodsCategory extends Models
{
    // 设置字段信息
    protected $schema = [
        'id'          => 'int',     //主键ID
        'pid'         => 'int',     //父级ID
        'name'        => 'string',  //分类名称
        'image'       => 'string',  //图片地址
        'relation'    => 'string',  //关系链条
        'level'       => 'int',     //层级排序
        'sort'        => 'int',     //排序编号
        'is_show'     => 'int',     //是否显示[0=否,1=是]
        'is_delete'   => 'int',     //是否删除[0=否,1=是]
        'create_time' => 'int',     //创建时间
        'update_time' => 'int',     //更新时间
        'delete_time' => 'int',     //删除时间
    ];

    /**
     * 修改器: 处理图片路径
     *
     * @author windy
     * @param $value
     * @return string
     */
    public function setImageAttr($value)
    {
        return UrlUtils::getRelativeUrl($value);
    }

    /**
     * 修改器: 处理图片路径
     *
     * @author windy
     * @param $value
     * @return string
     */
    public function getImageAttr($value)
    {
        if (!$value) {
            return '';
        } else {
            return UrlUtils::getAbsoluteUrl($value);
        }
    }
    /**
     * 获取某id的父级ID链条(包含自己,数组最后一个元素)
     *
     * @author windy
     * @param int $cid
     * @return array
     */
    public static function getParentIds(int $cid)
    {
        $model = new self();
        $category = $model->field('id,name,relation')
            ->where(['id'=>$cid])
            ->findOrEmpty()
            ->toArray();

        $array = [];
        $relation = explode(',', trim($category['relation']));
        foreach ($relation as $id) {
            if (intval($id) !== 0) {
                array_push($array, intval($id));
            }
        }

        return $array;
    }


    /**
     * 获取某id的子级ID链条(包含自己,数组第一个元素)
     *
     * @author windy
     * @param int $cid
     * @return array
     */
    public static function getChildrenIds(int $cid)
    {
        $model = new self();
        return $model->field('id,name')
            ->where("find_in_set(".$cid.",relation)")
            ->order('level asc')
            ->column('id');
    }
}