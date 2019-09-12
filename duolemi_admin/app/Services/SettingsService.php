<?php
/**
 * Created by PhpStorm.
 * User: gcs
 * Date: 2019-09-10
 * Time: 16:00
 */
namespace App\Services;

use App\Models\Settings;

class SettingsService extends BaseService
{
    protected $model;

    /**
     * 构造方法
     *
     * SettingsService constructor.
     * @param Settings $settings
     */
    public function __construct(Settings $settings)
    {
        $this->model = $settings;
    }

    /**
     * 获取设置
     *
     * @return mixed
     */
    public function getSettings()
    {
       return $this->model::where('is_delete', 0)->get();
    }

    /**
     * 设置
     *
     * @param $params
     * @return array
     */
    public function setSettings($params)
    {
        $settings_model = $this->model::find($params['id']);
        $settings_model->value = $params['type'] == 'true' ? 1 : 0;
        $res = $settings_model->save();

        if ($res) {
            return [
                'code' => 0,
                'msg'  => '设置成功'
            ];
        } else {
            return [
                'code' => 1,
                'msg'  => '设置失败'
            ];
        }
    }
}