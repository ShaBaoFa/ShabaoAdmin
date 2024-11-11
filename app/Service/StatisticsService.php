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

namespace App\Service;

use App\Base\BaseService;
use App\Dao\ExhLibObjDao;
use App\Dao\LoginLogDao;
use App\Dao\ObjDlApprovalDao;
use App\Dao\OrganizationDao;
use App\Dao\RegionDao;
use App\Dao\RentApprovalDao;
use App\Dao\UserDao;
use App\Model\LoginLog;
use Carbon\Carbon;
use Hyperf\Collection\Arr;
use Hyperf\Collection\Collection;

use function Hyperf\Collection\collect;

class StatisticsService extends BaseService
{
    private LoginLogDao $loginLogDao;

    private UserDao $userDao;

    private RegionDao $regionDao;

    private ExhLibObjDao $exhLibObjDao;

    private OrganizationDao $organizationDao;

    private ObjDlApprovalDao $objDlApprovalDao;

    private RentApprovalDao $rentApprovalDao;

    public function __construct(RentApprovalDao $rentApprovalDao, ObjDlApprovalDao $objDlApprovalDao, OrganizationDao $organizationDao, ExhLibObjDao $exhLibObjDao, LoginLogDao $loginLogDao, UserDao $userDao, RegionDao $regionDao)
    {
        $this->loginLogDao = $loginLogDao;
        $this->userDao = $userDao;
        $this->regionDao = $regionDao;
        $this->exhLibObjDao = $exhLibObjDao;
        $this->organizationDao = $organizationDao;
        $this->objDlApprovalDao = $objDlApprovalDao;
        $this->rentApprovalDao = $rentApprovalDao;
    }

    public function visit(): array
    {
        // Fetch all login logs
        $logs = $this->loginLogDao->getAll();

        // Calculate PV (Page Views) - simply the count of all items
        $totalPv = $logs->count();

        // Calculate UV (Unique Visitors) - unique count of usernames
        $totalUv = $logs->unique('username')->count();

        // Calculate unique IPs
        $totalUniqueIps = $logs->unique('ip')->count();

        // For today's metrics, filter by the current date
        $today = Carbon::now()->toDateString();
        $todayLogs = $logs->filter(function ($log) use ($today) {
            /**
             * @var LoginLog $log
             */
            return Carbon::parse($log->login_time)->toDateString() === $today;
        });

        $todayPv = $todayLogs->count();
        $todayUv = $todayLogs->unique('username')->count();
        $todayUniqueIps = $todayLogs->unique('ip')->count();

        // Return all metrics in an array
        return [
            'total_pv' => $totalPv,
            'total_uv' => $totalUv,
            'total_unique_ips' => $totalUniqueIps,
            'today_pv' => $todayPv,
            'today_uv' => $todayUv,
            'today_unique_ips' => $todayUniqueIps,
        ];
    }

    public function user(): array
    {
        $users = $this->userDao->getAll();

        // 用户总数
        $total = $users->count();
        // 本月新增
        $month = $users->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->count();
        // 本月活跃 login_time
        $active = $users->whereBetween('login_time', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->count();
        return [
            'total_users' => $total,
            'month_new_users' => $month,
            'month_active_users' => $active,
        ];
    }

    public function regionActive(array $params): array
    {
        return $this->regionDao->regionActive($params);
    }

    public function visitStatistics(): array
    {
        // Fetch all logs from the database
        $logs = $this->loginLogDao->getAll();

        // Initialize Carbon instances for date filtering
        $now = Carbon::now();
        $sevenDaysAgo = $now->copy()->subDays(6);  // Last 7 days inclusive
        $thirtyDaysAgo = $now->copy()->subDays(29); // Last 30 days inclusive
        $oneYearAgo = $now->copy()->subYear()->startOfMonth(); // Last 1 year inclusive, starting at the beginning of the month


        // Filter logs for each time range
        $logs7Days = $logs->filter(function ($log) use ($sevenDaysAgo, $now) {
            return $log->login_time >= $sevenDaysAgo && $log->login_time <= $now;
        });

        $logs30Days = $logs->filter(function ($log) use ($thirtyDaysAgo, $now) {
            return $log->login_time >= $thirtyDaysAgo && $log->login_time <= $now;
        });

        $logs1Year = $logs->filter(function ($log) use ($oneYearAgo, $now) {
            return $log->login_time >= $oneYearAgo && $log->login_time <= $now;
        });

        // Group data for each range
        $data7Days = $this->generateDateRangeData($logs7Days, $sevenDaysAgo, $now, 'Y-m-d');
        $data30Days = $this->generateDateRangeData($logs30Days, $thirtyDaysAgo, $now, 'Y-m-d');
        $data1Year = $this->generateDateRangeData($logs1Year, $oneYearAgo, $now, 'Y-m');

        return [
            '7_days' => $data7Days,
            '30_days' => $data30Days,
            '1_year' => $data1Year,
        ];
    }

    public function fileUploadStatistics(array $params): array
    {
        $userIds = [];
        if ($orgId = Arr::get($params, 'organization_id')) {
            $userIds = $this->organizationDao->getAllStaffIds((int) $orgId);
        }

        if ($userIds) {
            Arr::set($params, 'createdBy', $userIds);
        }

        Arr::set($params, '_with', [
            'files' => [
                'aggregate' => [
                    'count' => '*',              // 获取附件数量
                ],
            ],
        ]);
        $list = $this->exhLibObjDao->getList($params, false);

        // Initialize date ranges and group the data for each time period
        $data7Days = $this->generateFileDateRangeData($list, 7, 'Y-m-d');
        $data30Days = $this->generateFileDateRangeData($list, 30, 'Y-m-d');
        $data1Year = $this->generateFileDateRangeData($list, 365, 'Y-m');

        return [
            '7_days' => $data7Days,
            '30_days' => $data30Days,
            '1_year' => $data1Year,
        ];
    }

    public function fileDownloadStatistics(array $params): array
    {
        $userIds = [];
        if ($orgId = Arr::get($params, 'organization_id')) {
            $userIds = $this->organizationDao->getAllStaffIds((int) $orgId);
        }
        $exhLibObjQuery = $this->objDlApprovalDao->model::query();
        if ($userIds) {
            Arr::set($params, 'createdBy', $userIds);
            $exhLibObjQuery->whereIn('created_by', $userIds);
        }
        $objIds = $exhLibObjQuery->pluck('exh_lib_obj_id')->toArray();

        Arr::set($params, 'ids', $objIds);
        Arr::set($params, '_with', [
            'files' => [
                'aggregate' => [
                    'count' => '*',              // 获取附件数量
                ],
            ],
        ]);
        $list = $this->exhLibObjDao->getList($params, false);

        // Initialize date ranges and group the data for each time period
        $data7Days = $this->generateFileDateRangeData($list, 7, 'Y-m-d');
        $data30Days = $this->generateFileDateRangeData($list, 30, 'Y-m-d');
        $data1Year = $this->generateFileDateRangeData($list, 365, 'Y-m');

        return [
            '7_days' => $data7Days,
            '30_days' => $data30Days,
            '1_year' => $data1Year,
        ];
    }

    public function rentStatistics(array $params): array
    {
        $list = $this->rentApprovalDao->getList($params, false);

        // Initialize date ranges and group the data for each time period
        $data7Days = $this->generateRentDateRangeData($list, 7, 'Y-m-d');
        $data30Days = $this->generateRentDateRangeData($list, 30, 'Y-m-d');
        $data1Year = $this->generateRentDateRangeData($list, 365, 'Y-m');

        return [
            '7_days' => $data7Days,
            '30_days' => $data30Days,
            '1_year' => $data1Year,
        ];
    }

    public function rentRanking(array $params): array
    {
        return $this->rentApprovalDao->getTopRanking();
    }

    // Helper function to generate data for a given date range and format
    private function generateDateRangeData($logs, $startDate, $endDate, $format): Collection
    {
        // Generate a complete date range with zeroed data
        $dateRangeData = collect();
        $datePointer = $startDate->copy();

        while ($datePointer->lte($endDate)) {
            $formattedDate = $datePointer->format($format);
            $dateRangeData[$formattedDate] = [
                'uv' => 0,
                'pv' => 0,
                'ip' => 0,
            ];

            // Move pointer by one day or one month based on the format
            $datePointer->add($format === 'Y-m-d' ? '1 day' : '1 month');
        }

        // Group logs by date format and calculate metrics
        $groupedLogs = $logs->groupBy(function ($log) use ($format) {
            /**
             * @var LoginLog $log
             */
            return Carbon::parse($log->login_time)->format($format);
        })->map(function ($group) {
            return [
                'uv' => $group->unique('username')->count(),
                'pv' => $group->count(),
                'ip' => $group->unique('ip')->count(),
            ];
        });

        // Merge grouped log data into the date range, replacing zeros with actual counts
        return $dateRangeData->merge($groupedLogs);
    }


    // Helper function to generate a complete date range and sum files_count data
    private function generateFileDateRangeData(array $dataList, int $days, string $format): array
    {
        // Determine the start date based on the number of days
        $startDate = $days === 365 ? Carbon::now()->subYear()->startOfMonth() : Carbon::now()->subDays($days - 1);
        $endDate = Carbon::now();

        // Generate a complete date range with zeroed data
        $dateRangeData = [];
        $datePointer = $startDate->copy();

        while ($datePointer->lte($endDate)) {
            $formattedDate = $datePointer->format($format);
            $dateRangeData[$formattedDate] = 0; // Initialize each date with zero

            // Move pointer by one day or one month based on the format
            $datePointer->add($format === 'Y-m-d' ? '1 day' : '1 month');
        }

        // Convert data list to collection for grouping and summing files_count
        $dataCollection = collect($dataList);

        // Group data by date format and sum the files_count for each group
        $groupedData = $dataCollection->groupBy(function ($item) use ($format) {
            return Carbon::parse($item['created_at'])->format($format);
        })->map(function ($group) {
            return $group->sum('files_count'); // Sum files_count for each day or month
        });

        // Merge grouped data with the zero-filled date range
        return array_merge($dateRangeData, $groupedData->toArray());
    }

    // Helper function to generate a complete date range and merge actual data into it
    private function generateRentDateRangeData(array $dataList, int $days, string $format): array
    {
        // Determine the start date based on the number of days
        $startDate = $days === 365 ? Carbon::now()->subYear()->startOfMonth() : Carbon::now()->subDays($days - 1);
        $endDate = Carbon::now();

        // Generate a complete date range with zeroed data
        $dateRangeData = [];
        $datePointer = $startDate->copy();

        while ($datePointer->lte($endDate)) {
            $formattedDate = $datePointer->format($format);
            $dateRangeData[$formattedDate] = 0; // Initialize each date with zero

            // Move pointer by one day or one month based on the format
            $datePointer->add($format === 'Y-m-d' ? '1 day' : '1 month');
        }

        // Convert data list to collection for grouping and counting
        $dataCollection = collect($dataList);

        // Group data by date format and count the file uploads
        $groupedData = $dataCollection->groupBy(function ($item) use ($format) {
            return Carbon::parse($item['created_at'])->format($format);
        })->map(function ($group) {
            return $group->count();
        });

        // Merge grouped data with the zero-filled date range
        return array_merge($dateRangeData, $groupedData->toArray());
    }
}
