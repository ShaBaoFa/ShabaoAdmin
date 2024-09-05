-- Lua 脚本来存储 region 数据
-- KEYS[1] 是传递的 region 键的前缀，比如 "region"
-- ARGV 是传递的所有区域数据，以分组方式传入

local region_key_prefix = KEYS[1]


redis.log(redis.LOG_NOTICE, "region_key_prefix: " .. region_key_prefix)

-- 解析传递的 region 数据
for i = 1, #ARGV, 9 do
    local id = ARGV[i]
    local parent_id = ARGV[i + 1]
    local level = ARGV[i + 2]
    local name = ARGV[i + 3]
    local initial = ARGV[i + 4]
    local pinyin = ARGV[i + 5]
    local citycode = ARGV[i + 6]
    local adcode = ARGV[i + 7]
    local lng_lat = ARGV[i + 8]

    -- 打印调试信息
    redis.log(redis.LOG_NOTICE, "Storing region with ID: " .. id)

    -- 构造 Redis 键
    local region_key = region_key_prefix .. ":" .. id
    redis.log(redis.LOG_NOTICE, "region_key: " .. region_key)

    -- 将 region 信息存储到哈希表中
    redis.call('HMSET', region_key,
        'id', id,
        'parent_id', parent_id,
        'level', level,
        'name', name,
        'initial', initial,
        'pinyin', pinyin,
        'citycode', citycode,
        'adcode', adcode,
        'lng_lat', lng_lat
    )

    -- 创建索引
    redis.call('ZADD', 'index:name', 0, name .. ':' .. id)
    redis.call('ZADD', 'index:initial', 0, initial .. ':' .. id)
    redis.call('ZADD', 'index:pinyin', 0, pinyin .. ':' .. id)

    -- 创建 parent_id 和子级的映射关系
    local children_key = 'children:' .. parent_id
    redis.call('SADD', children_key, id)

    -- 存储地理位置数据（如果有的话）
    if lng_lat ~= '' then
        local lng_lat_split = {}
        for value in string.gmatch(lng_lat, '([^,]+)') do
            table.insert(lng_lat_split, value)
        end
        local lng = tonumber(lng_lat_split[1])
        local lat = tonumber(lng_lat_split[2])
        redis.call('GEOADD', 'geo:region', lng, lat, id)
    end
end

return "Data stored successfully"
