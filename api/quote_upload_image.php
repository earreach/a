<?php
//define('IN_DESTOON', true);
require '../common.inc.php';
require DT_ROOT.'/include/upload.class.php';

header('Content-Type: application/json; charset=utf-8');
//下面加上两行（调试用）：
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
// 1. 校验是否有文件
if(empty($_FILES['file']) || !$_FILES['file']['size']) {
    echo json_encode(['success'=>false,'message'=>'没有选择文件']);
    exit;
}

// 2. 限制文件类型（只允许图片）
$ext = file_ext($_FILES['file']['name']);
if(!in_array(strtolower($ext), array('jpg','jpeg','png','gif','webp'))) {
    echo json_encode(['success'=>false,'message'=>'只允许上传图片文件']);
    exit;
}

// ================== 关键路径逻辑从这里开始 ==================

// 模块ID（文章模块：21）
$moduleid = 21;

// 日期子目录：例如 202412/19/
$subdir = date('Ym').'/'.date('d').'/';
// 如果你想完全跟 DT 内置 timetodir 一致，也可以用：
// $subdir = timetodir($DT_TIME);

// 最终保存目录：file/upload/21/202412/19/
$savepath = 'file/upload/'.$moduleid.'/'.$subdir;

// 确保目录存在（Destoon 自带递归创建目录函数，common.inc.php 里已经引入）
$full_dir = DT_ROOT.'/'.$savepath;
if(!is_dir($full_dir)) {
    // 递归创建目录，0777 权限，本地环境没问题
    if(!mkdir($full_dir, 0777, true)) {
        echo json_encode(array('success' => false, 'message' => '创建目录失败：'.$full_dir));
        exit;
    }
}


// 生成文件名：时间戳 + 随机数
$filename = date('YmdHis').mt_rand(10,99).'.'.$ext;

// 创建 upload 对象
// 注意：这里传的是 array('file' => $_FILES['file'])，对应 upload.class 里对单个文件的处理
$upload = new upload(array('file' => $_FILES['file']), $savepath, $filename, 'jpg|jpeg|png|gif|webp');

// 不需要再按会员ID分目录
$upload->adduserid = false;

// 保存文件
if($upload->save()) {
    // saveto 形如：file/upload/21/202412/19/2024121919194539.jpg
    $rel_path = $upload->saveto;
    $url      = DT_PATH.$rel_path;

    echo json_encode([
        'success' => true,
        'url'     => $url,      // 前端 <img src="url">
        'path'    => $rel_path, // 前端 hidden images[] 用这个
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => $upload->errmsg ? $upload->errmsg : '上传失败'
    ]);
}
