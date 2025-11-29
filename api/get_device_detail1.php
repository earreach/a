<?php
require_once '../common.inc.php';
header('Content-Type: application/json; charset=utf-8');

$catid = isset($_GET['catid']) ? intval($_GET['catid']) : 0;

if (empty($catid)) {
    echo json_encode(['success' => false, 'message' => '分类ID参数不能为空']);
    exit;
}

// 模块ID固定为21
$moduleid = 21;

// 使用缓存的fua数据
$cache_key = 'module21_fua_' . $catid;
$processed_data = cache_read($cache_key);

// 记录缓存状态
if ($processed_data === false) {
    log_write("FUA数据缓存未命中，开始从数据库查询，catid: {$catid}", 'info');

    // 查询数据库
    $result = $db->query("SELECT fua FROM {$DT_PRE}category WHERE moduleid = {$moduleid} AND catid = {$catid}");

    if ($result && $db->num_rows($result) > 0) {
        $category = $db->fetch_array($result);
        $fua_data = $category['fua'];

        log_write("从数据库获取到FUA原始数据，长度: " . strlen($fua_data), 'info');

        // 处理fua数据
        if (!empty($fua_data)) {
            // JSON解码
            $fua_array = json_decode($fua_data, true);

            if ($fua_array && is_array($fua_array)) {
                $processed_data = [];

                foreach ($fua_array as $item) {
                    // 过滤空项（num、ming、bi都为空）
                    if (empty($item['num']) && empty($item['ming']) && empty($item['bi'])) {
                        continue;
                    }

                    // 对ming字段进行urldecode
                    if (!empty($item['ming'])) {
                        $item['ming'] = urldecode($item['ming']);
                    }

                    $processed_data[] = $item;
                }

                // 按num字段降序排序
                usort($processed_data, function($a, $b) {
                    return intval($b['num']) - intval($a['num']);
                });

                log_write("处理后的FUA数据记录数: " . count($processed_data), 'info');
            } else {
                log_write("FUA数据JSON解码失败或不是数组", 'error');
                $processed_data = [];
            }
        } else {
            log_write("数据库中的FUA数据为空", 'info');
            $processed_data = [];
        }

        // 写入缓存，缓存30天（无论数据是否为空都缓存）
        $cache_result = cache_write($cache_key, $processed_data, 30 * 24 * 3600);
        if ($cache_result) {
            log_write("FUA数据缓存写入成功，catid: {$catid}, 记录数: " . count($processed_data), 'info');
        } else {
            log_write("FUA数据缓存写入失败，catid: {$catid}", 'error');
        }
    } else {
        log_write("未找到对应的设备信息，catid: {$catid}", 'error');
        echo json_encode([
            'success' => false,
            'message' => '未找到对应的设备信息'
        ]);
        exit;
    }
} else {
    // 缓存命中
    log_write("FUA数据缓存命中，catid: {$catid}, 记录数: " . count($processed_data), 'info');
}

// 返回处理后的数据
echo json_encode([
    'success' => true,
    'fua_data' => $processed_data
]);
?>