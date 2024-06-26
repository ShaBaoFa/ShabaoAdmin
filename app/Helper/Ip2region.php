<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Helper;

use Composer\Autoload\ClassLoader;
use Exception;
use Hyperf\Contract\StdoutLoggerInterface;
use RuntimeException;
use XdbSearcher;

class Ip2region
{
    protected XdbSearcher $searcher;

    /**
     * @throws Exception
     */
    public function __construct(protected ?StdoutLoggerInterface $logger = null)
    {
        $composerLoader = $this->getLoader();
        $path = $composerLoader->findFile(XdbSearcher::class);

        $dbFile = dirname(realpath($path)) . '/ip2region.xdb';

        // 1、从 dbPath 加载整个 xdb 到内存。
        $cBuff = XdbSearcher::loadContentFromFile($dbFile);
        if ($cBuff === null) {
            $this->logger?->error('failed to load content buffer from {db_file}', ['db_file' => $dbFile]);
            return;
        }
        // 2、使用全局的 cBuff 创建带完全基于内存的查询对象。
        $this->searcher = XdbSearcher::newWithBuffer($cBuff);

        // 备注：并发使用，用整个 xdb 缓存创建的 searcher 对象可以安全用于并发。
    }

    /**
     * @throws Exception
     */
    public function search(string $ip): string
    {
        $region = $this->searcher->search($ip);

        if (! $region) {
            return 'Unknown';
        }

        [$country, $number, $province, $city, $network] = explode('|', $region);
        if ($country == '中国') {
            return $province . '-' . $city . ':' . $network;
        }
        if ($country == '0') {
            return 'Unknown';
        }
        return $country;
    }

    private function getLoader(): ClassLoader
    {
        $loaders = spl_autoload_functions();

        foreach ($loaders as $loader) {
            if (is_array($loader) && $loader[0] instanceof ClassLoader) {
                return $loader[0];
            }
        }

        throw new RuntimeException('Composer loader not found.');
    }
}
