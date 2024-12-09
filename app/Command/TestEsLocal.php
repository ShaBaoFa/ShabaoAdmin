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

namespace App\Command;

use App\Dao\LoginLogDao;
use App\Dao\NewsDao;
use Carbon\Carbon;
use Elasticsearch\Client;
use GuzzleHttp\Exception\GuzzleException;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Elasticsearch\ClientBuilderFactory;
use OSS\Core\OssException;
use OSS\Http\RequestCore_Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use RedisException;
use stdClass;

#[Command]
class TestEsLocal extends HyperfCommand
{
    protected Client $esClient;

    public function __construct(protected ContainerInterface $container)
    {
        $builder = di()->get(ClientBuilderFactory::class)->create();
        $this->esClient = $builder->setHosts(['localhost:9200'])->build();
        parent::__construct('test:es');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Hyperf Demo Command');
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws RequestCore_Exception
     * @throws ContainerExceptionInterface
     * @throws OssException
     * @throws RedisException
     * @throws GuzzleException
     */
    public function handle(): void
    {
        //        $this->transNewsToEs();
        //        $this->transLogsToEs();
        //        $result = $this->visit();
        //        $this->searchNews();
        //        $this->getAnalyzeSetting();
    }

    protected function getAnalyzeSetting()
    {
        $params = ['index' => 'news'];
        $response = $this->esClient->indices()->getSettings($params);
        print_r($response);
    }

    protected function searchNews()
    {
        $params = [
            'index' => 'news',
            'body' => [
                'analyzer' => 'ik_smart',
                'text' => '我是人民接班人',
            ],
        ];
        $builder = di()->get(ClientBuilderFactory::class)->create();
        $client = $builder->setHosts(['localhost:9200'])->build();
        $response = $client->indices()->analyze($params);
        var_dump($response);
        //        $totalCount = $response['hits']['total']['value'];
        //        $hits = $response['hits']['hits'];
        //        $data = [
        //            'items' => $hits,
        //            'pageInfo' => [
        //                'total' => $totalCount,
        //                'currentPage' => 1,
        //                'totalPage' => ceil($totalCount / 20),
        //            ]
        //        ];
    }

    protected function transNewsToEs()
    {
        $builder = di()->get(ClientBuilderFactory::class)->create();
        $client = $builder->setHosts(['localhost:9200'])->build();
        $indexExists = $client->indices()->exists(['index' => 'news']);

        if ($indexExists) {
            $response = $client->indices()->delete(['index' => 'news']);
        }
        $params = [
            'index' => 'news',  // 索引名称
            'body' => [
                'settings' => [
                    'analysis' => [
                        'tokenizer' => [
                            'ik_max_word' => [
                                'type' => 'ik_max_word',  // 使用 ik_max_word 分词器
                            ],
                            'ik_smart' => [
                                'type' => 'ik_smart',  // 使用 ik_smart 分词器
                            ],
                        ],
                        'analyzer' => [
                            'ik_max_word_analyzer' => [
                                'type' => 'custom',
                                'tokenizer' => 'ik_max_word',  // 使用 ik_max_word 分词器进行分词
                            ],
                            'ik_smart_analyzer' => [
                                'type' => 'custom',
                                'tokenizer' => 'ik_smart',  // 使用 ik_smart 分词器进行分词
                            ],
                        ],
                    ],
                ],
                'mappings' => [
                    'properties' => [
                        'id' => [
                            'type' => 'long',
                        ],
                        'title' => [
                            'type' => 'text',
                            'analyzer' => 'ik_max_word_analyzer',  // 使用 ik_max_word 分词器
                            'search_analyzer' => 'ik_smart_analyzer',  // 搜索时使用 ik_smart 分词器
                        ],
                        'author' => [
                            'type' => 'keyword',
                        ],
                        'lib_area_type' => [
                            'type' => 'short',
                        ],
                        'profile' => [
                            'type' => 'text',
                            'analyzer' => 'ik_smart_analyzer',  // 使用 ik_smart 分词器
                            'search_analyzer' => 'ik_smart_analyzer',  // 搜索时也使用 ik_smart 分词器
                        ],
                        'content' => [
                            'type' => 'text',
                            'analyzer' => 'ik_max_word_analyzer',  // 使用 ik_max_word 分词器
                            'search_analyzer' => 'ik_smart_analyzer',  // 搜索时使用 ik_smart 分词器
                        ],
                    ],
                ],
            ],
        ];

        // 创建索引
        $client->indices()->create($params);

        $newsDao = di()->get(NewsDao::class);
        $news = $newsDao->model::all()->toArray();
        $batchSize = 1000;  // 每批次导入的数据量，您可以根据实际情况调整

        // 准备批量数据
        $body = [];
        $counter = 0;  // 计数器，用来控制每批次的大小

        foreach ($news as $item) {
            // 添加每条记录的index操作
            $body[] = [
                'index' => [
                    '_index' => 'news',  // Elasticsearch索引名称
                    '_id' => $item['id'],  // 使用MySQL的id作为文档ID
                ],
            ];

            // 格式化要插入的数据
            $body[] = [
                'id' => $item['id'],
                'title' => $item['title'],
                'author' => $item['author'],
                'lib_area_type' => $item['lib_area_type'],
                'profile' => $item['profile'],
                'content' => $item['content'],
            ];

            ++$counter;

            // 当计数器达到batchSize时，发送一次bulk请求
            if ($counter >= $batchSize) {
                // 批量插入
                $response = $client->bulk(['body' => $body]);

                // 检查批量操作结果
                if ($response['errors']) {
                    $this->error("批量插入时发生错误！\n");
                    print_r($response);
                } else {
                    $this->info("批量插入{$counter}成功！\n");
                }

                // 重置计数器和body，准备下一个批次
                $body = [];
                $counter = 0;
            }
        }

        // 如果还有剩余的数据（小于batchSize的部分），也需要执行一次bulk操作
        if ($counter > 0) {
            $response = $client->bulk(['body' => $body]);

            // 检查最后一个批次的导入结果
            if ($response['errors']) {
                $this->error("最后批次插入时发生错误！\n");
                print_r($response);
            } else {
                $this->info("最后批次插入{$counter}成功！\n");
            }
        }
    }

    protected function visit(): array
    {
        $builder = di()->get(ClientBuilderFactory::class)->create();
        $client = $builder->setHosts(['localhost:9200'])->build();
        $now = Carbon::now();  // 当前时间
        $today = $now->toDateString();  // 今天的日期
        // Elasticsearch 查询
        $response = $client->search([
            'index' => 'login_logs',
            'body' => [
                'query' => [
                    'match_all' => new stdClass(),
                ],
                'aggs' => [
                    'total_pv' => [
                        'value_count' => [
                            'field' => 'id',  // 用 id 字段计算总数（假设每条日志有一个唯一的 id）
                        ],
                    ],
                    'total_uv' => [
                        'cardinality' => [
                            'field' => 'username',  // UV 通过唯一的 username 计算
                        ],
                    ],
                    'total_unique_ips' => [
                        'cardinality' => [
                            'field' => 'ip',  // Unique IPs 通过唯一的 ip 计算
                        ],
                    ],
                    'today_stats' => [
                        'filter' => [
                            'range' => [
                                'login_time' => [
                                    'gte' => $now->startOfDay()->toDateTimeString(),
                                    'lte' => $now->endOfDay()->toDateTimeString(),
                                ],
                            ],
                        ],
                        'aggs' => [
                            'today_pv' => [
                                'value_count' => [
                                    'field' => 'id',
                                ],
                            ],
                            'today_uv' => [
                                'cardinality' => [
                                    'field' => 'username',
                                ],
                            ],
                            'today_unique_ips' => [
                                'cardinality' => [
                                    'field' => 'ip',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        // 解析 Elasticsearch 返回的结果
        $totalPv = $response['aggregations']['total_pv']['value'];
        $totalUv = $response['aggregations']['total_uv']['value'];
        $totalUniqueIps = $response['aggregations']['total_unique_ips']['value'];

        $todayPv = $response['aggregations']['today_stats']['today_pv']['value'];
        $todayUv = $response['aggregations']['today_stats']['today_uv']['value'];
        $todayUniqueIps = $response['aggregations']['today_stats']['today_unique_ips']['value'];
        //        // 返回结果
        return [
            'total_pv' => $totalPv,
            'total_uv' => $totalUv,
            'total_unique_ips' => $totalUniqueIps,
            'today_pv' => $todayPv,
            'today_uv' => $todayUv,
            'today_unique_ips' => $todayUniqueIps,
        ];
    }

    protected function transLogsToEs(): void
    {
        $builder = di()->get(ClientBuilderFactory::class)->create();
        $client = $builder->setHosts(['localhost:9200'])->build();
        $indexParams = [
            'index' => 'login_logs',  // 设置索引名称
            'body' => [
                'mappings' => [
                    'properties' => [
                        'id' => ['type' => 'long'],
                        'username' => ['type' => 'keyword'],
                        'ip' => ['type' => 'ip'],
                        'ip_location' => ['type' => 'text'],
                        'os' => ['type' => 'keyword'],
                        'browser' => ['type' => 'keyword'],
                        'status' => ['type' => 'short'],
                        'message' => ['type' => 'text'],
                        'login_time' => [
                            'type' => 'date',
                            'format' => 'yyyy-MM-dd HH:mm:ss',  // 更新日期格式
                        ],
                    ],
                ],
            ],
        ];
        $indexExists = $client->indices()->exists(['index' => 'login_logs']);

        if ($indexExists) {
            $response = $client->indices()->delete(['index' => 'login_logs']);
        }
        $response = $client->indices()->create($indexParams);

        // 创建索引
        $logs = di()->get(LoginLogDao::class)->getAll()->toArray();
        $batchSize = 1000;  // 每批次导入的数据量，您可以根据实际情况调整

        // 准备批量数据
        $body = [];
        $counter = 0;  // 计数器，用来控制每批次的大小

        foreach ($logs as $log) {
            // 添加每条记录的index操作
            $body[] = [
                'index' => [
                    '_index' => 'login_logs',  // Elasticsearch索引名称
                    '_id' => $log['id'],  // 使用MySQL的id作为文档ID
                ],
            ];

            // 格式化要插入的数据
            $body[] = [
                'id' => $log['id'],
                'username' => $log['username'],
                'ip' => $log['ip'],
                'ip_location' => $log['ip_location'],
                'os' => $log['os'],
                'browser' => $log['browser'],
                'status' => $log['status'],
                'message' => $log['message'],
                'login_time' => $log['login_time'],
            ];

            ++$counter;

            // 当计数器达到batchSize时，发送一次bulk请求
            if ($counter >= $batchSize) {
                // 批量插入
                $response = $client->bulk(['body' => $body]);

                // 检查批量操作结果
                if ($response['errors']) {
                    $this->error("批量插入时发生错误！\n");
                    print_r($response);
                } else {
                    $this->info("批量插入{$counter}成功！\n");
                }

                // 重置计数器和body，准备下一个批次
                $body = [];
                $counter = 0;
            }
        }

        // 如果还有剩余的数据（小于batchSize的部分），也需要执行一次bulk操作
        if ($counter > 0) {
            $response = $client->bulk(['body' => $body]);

            // 检查最后一个批次的导入结果
            if ($response['errors']) {
                $this->error("最后批次插入时发生错误！\n");
                print_r($response);
            } else {
                $this->info("最后批次插入{$counter}成功！\n");
            }
        }
    }
}
