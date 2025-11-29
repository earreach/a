<?php
//defined('IN_DESTOON') or exit('Access Denied');
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
//if($processed_data) {
//    log_write("FUA数据从缓存读取: catid={$catid}", 'info');
//} else {
//    log_write("FUA数据从数据库读取: catid={$catid}", 'info');
//}
//测试代码
// 检查所有可能的缓存位置
//$correct_path = DT_ROOT.'/file/cache/'.$cache_key;
//$wrong_path = DT_ROOT.'/file/cache/2592000/'.$cache_key;
//echo "<!-- 缓存键: $cache_key -->";
//echo "<!-- 正确路径: $correct_path -->";
//echo "<!-- 正确路径存在: ".(file_exists($correct_path) ? '是' : '否')." -->";
//echo "<!-- 错误路径: $wrong_path -->";
//echo "<!-- 错误路径存在: ".(file_exists($wrong_path) ? '是' : '否')." -->";
//echo "<!-- 缓存数据: ".(empty($processed_data) ? '空' : '非空')." -->";


//if($processed_data) {
//    echo "<!-- 故障数据从缓存读取，catid: $catid -->";
//    $cache_file = DT_ROOT.'/file/cache/'.$cache_key.'.php';
//    if(file_exists($cache_file)) {
//        echo "<!-- 故障缓存文件在正确位置 -->";
//    } else {
//        echo "<!-- 故障缓存文件在错误位置 -->";
//    }
//}
//测试代码


//var_dump($processed_data);
//die();
// 如果缓存不存在或读取失败，从数据库获取并处理
if (empty($processed_data) || $processed_data === false) {


//    var_dump(9999);
//    die();
    // 记录缓存未命中
    log_write("FUA数据缓存未命中，catid: {$catid}", 'info');

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
//    var_dump($processed_data);
//    die();
                // 按num字段降序排序
                usort($processed_data, function($a, $b) {
                    return intval($b['num']) - intval($a['num']);
                });
            } else {
                $processed_data = [];
            }
        } else {
            $processed_data = [];
        }
//    var_dump($processed_data);
//    die();
        // 写入缓存，缓存30天（无论数据是否为空都缓存）
//        $cache_result = cache_write($cache_key, $processed_data, 30 * 24 * 3600);
        $cache_result = cache_write($cache_key, $processed_data, '', 30 * 24 * 3600);
//            var_dump($cache_result);
//    die();
        if (!$cache_result) {
            log_write("FUA数111据缓存写入失败，catid: {$catid}", 'error');
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => '未找到对应的设备信息'
        ]);
        exit;
    }
} else {
    // 缓存命中
    // 可以在这里添加命中率统计代码
}

// 返回处理后的数据
echo json_encode([
    'success' => true,
    'fua_data' => $processed_data
]);
?>