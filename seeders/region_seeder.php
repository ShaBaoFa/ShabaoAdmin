<?php

declare(strict_types=1);
/**
 * This file is part of web-api.
 *
 * @link     https://blog.wlfpanda1012.com/
 * @github   https://github.com/ShaBaoFa
 * @gitee    https://gitee.com/wlfpanda/web-api
 * @contact  mail@wlfpanda1012.com
 */
use Hyperf\Database\Seeders\Seeder;
use Hyperf\DbConnection\Db;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $json_file = BASE_PATH . '/storage/region/region.json';
        echo '正在读取json数据···' . "\n";
        $json = file_get_contents($json_file);
        $data = json_decode($json, true);
        $msg = "\n" . '数据导入成功';
        $total = count($data) + 1;

        echo '正在导入数据···' . "\n";
        Db::table('region')->truncate();
        try {
            foreach ($data as $key => $value) {
                Db::table('region')->insert($value);
            }
        } catch (Exception $e) {
            $msg = $e->getMessage();
        }
        echo $msg . "\n";
        unset($total, $data, $json);
    }
}
