<?php
/**
 * Created by PhpStorm.
 * User: Samuel
 * Date: 18/8/9
 * Time: 下午10:23
 */

namespace Novel\NovelSpider\Models;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Model;
use Novel\NovelSpider\Services\Common\Config\ConfigService;
use Novel\NovelSpider\Services\Common\Mysql\ConnectorService;

/**
 * Class NovelMainModel
 * @property id
 * @property name
 * @property desc
 * @property list_url
 * @property base_url
 * @property novel_status
 * @property insert_date
 * @property update_time
 * @package Novel\NovelSpider\Models
 */
class NovelMainModel extends Model
{
    const UPDATED_AT = null;
    const CREATED_AT = null;
    protected $table = 'novel_main';
    protected $fillable = [
        'id',
        'name',
        'desc',
        'list_url',
        'base_url',
        'novel_status',
        'insert_date',
        'update_time',
    ];

    public function __construct()
    {
        ConnectorService::getOneConn();
    }

    public static function checkExist($paramArr = []): bool
    {
        $options = [
            'id'       => '',//
            'name'     => '',
            'list_url' => '',
            'flag'     => '',
            'fields'   => '*',// string 查询字段
            'isCount'  => '',// 可选：1 是否只返回数据的数量
            'debug'    => '',// 可选：1 调试，为true时，打印出sql语句
            'offset'   => 0,// 可选 int mysql查询数据的偏移量
            'limit'    => 1,// 可选 int mysql查询数据的条数限制
        ];
        $options = array_merge($options, $paramArr);
        $model = new self();
        if (!empty($id)) {
            if (is_array($id)) {
                $model = $model->whereIn('id', $id);
            } else {
                $model = $model->where('id', $id);
            }
        }
        if (!empty($options['list_url'])) {
            $model = $model->where('list_url', $options['list_url']);
        }

        return $model->count();
    }

    public function getList($paramArr = []): array
    {
        $options = [
            'id'       => '',//
            'name'     => '',
            'list_url' => '',
            'flag'     => '',
            'fields'   => '*',// string 查询字段
            'isCount'  => '',// 可选：1 是否只返回数据的数量
            'debug'    => '',// 可选：1 调试，为true时，打印出sql语句
            'orders'   => [],
            'offset'   => 0,// 可选 int mysql查询数据的偏移量
            'limit'    => 1,// 可选 int mysql查询数据的条数限制
        ];
        $options = array_merge($options, $paramArr);
        $model = new self();
        if (!empty($options['id'])) {
            if (is_array($options['id'])) {
                $model = $model->whereIn('id', $options['id']);
            } else {
                $model = $model->where('id', $options['id']);
            }
        }
        if (!empty($options['list_url'])) {
            $model = $model->where('list_url', $options['list_url']);
        }
        if (!empty($options['isCount'])) {
            return $model->count();
        }
        //order
        if (!empty($options['orders'])) {
            foreach ($options['orders'] as $orderField => $orderDir) {
                $model = $model->orderby($orderField, $orderDir);
            }
        } else {
            $model = $model->orderby('id', 'desc');
        }
        $model = $model->offset($options['offset'])->limit($options['limit']);
        if (!empty($options['debug'])) {
            echo $model->toSql() . "-----debug";
        }
        $data = $model->get([$options['fields']])
            ->toArray();

        return $data;
    }
}
