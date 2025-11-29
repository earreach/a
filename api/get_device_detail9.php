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
// 查询数据库
$result = $db->query("SELECT fua FROM {$DT_PRE}category WHERE moduleid = {$moduleid} AND catid = {$catid}");

if ($result && $db->num_rows($result) > 0) {
    $category = $db->fetch_array($result);
    $fua_data = $category['fua'];

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

            // 按num字段升序排序
            usort($processed_data, function($a, $b) {
                return intval($b['num']) - intval($a['num']);
            });

            echo json_encode([
                'success' => true,
                'fua_data' => $processed_data
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => '数据格式错误'
            ]);
        }
    } else {
        echo json_encode([
            'success' => true,
            'fua_data' => []
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => '未找到对应的设备信息'
    ]);
}
?>