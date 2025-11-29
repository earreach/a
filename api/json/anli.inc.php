<?php
defined('IN_DESTOON') or exit('Access Denied');
#JSON数据输出示例
// echo 111111111;
var_dump($_GET);
die()
$jsonPayload = file_get_contents('php://input');

// 将 JSON 字符串解码为 PHP 关联数组
// 第二个参数设为 true，表示返回数组；设为 false 或省略则返回对象。
$data = json_decode($jsonPayload, true); 

// 现在就可以像普通数组一样访问数据了
$userName = $data['user']['name'];
$firstScore = $data['scores'][0];

echo "用户名: " . $userName;
echo "第一个分数: " . $firstScore;
die();

// var_dump($_GET);die()
// console.log();

   $a="11111111";
echo json_encode($a,JSON_UNESCAPED_UNICODE);

die();
$lists = [];
$condition = 'itemid>0 ';
$offset = $_GET['offset']?$_GET['offset']:0;
$pagesize = $_GET['pagesize']?$_GET['pagesize']:10;
// $index0=$_GET['index0'];
// $index1=$_GET['index1'];JSON.parse(options.region)
$a1=json_decode($_GET['a1']);
// $a2 =json_decode($_GET['a2']);
// $a2 = $_GET['a2'];
// $a2 = str_replace('[','',$a2);
// $a2 = str_replace(']','',$a2);

// echo ("111111111");
// $a2=json_encode($a2);
// $arr = explode(",",$a2);
// $arr = json_encode($arr);///////
// echo ($arr);


// die();
// echo(66);
// print_r($a1);
// die();


foreach ($a1 as $k=>$v){
switch ($k) {
    case 0:
        $hx=$v;
        break;
    case 1:
        $fg=$v;
        break;
    case 2:
         $zj=$v;
        break;
    case 3:
        $mj=$v;
        break;

}

    // if($v[0]==0){
    //     $kj=$v[1];
    // }
    // if($v[0]==1){
    //     $jb=$v[1];
    // }
    // if($v[0]==2){
    //     $fg=$v[1];
    // }
    // if($v[0]==3){
    //     $ys=$v[1];
    // }
}

// echo ($kj);echo ($jb);echo ($fg);echo ($ys);
// var_dump($kj);
	if($hx) $condition.=" and huxing='$hx'"; 
// 	if($jb) $condition.=" and jubu='$jb'"; 
	if($fg) $condition.=" and fengge='$fg'"; 
	if($zj) $condition.=" and price='$zj'"; 
    if($mj) $condition.=" and mianji='$mj'"; 

		

$sql = "SELECT * FROM {$DT_PRE}photo_12 WHERE {$condition} ORDER BY hits DESC LIMIT {$offset},{$pagesize}";

// echo $sql;die();
$resqust = $db->query($sql);
while($r = $db->fetch_array($resqust)){
      $r[username0] = getCaseById($r[itemid])[username];
      $r[userid]=getComByUname(getCaseById($r[itemid])[username])[userid];
      if($r[userid]==null){
          $r[userid]=getComByAllUname(getCaseById($r[itemid])[username])[userid];
          
      };
    $r[username] = getCaseById($r[itemid])[username];
    // $r[bigtitle] = getCaseById($r[itemid])[title];
    $r[avatar]=useravatar($r[username],'middle');
    $r[username]=getmem($r[username]);
    // print_r($r[username]);die();
    $r[username]=$r[username][company]?$r[username][company]:$r[username][truename];
    //  $r[name]=$r[title];
  $r[fenggemingzi]=getserv('photo_12','fengge',$r['fengge']);
   $r[huxingmingzi]=getserv('photo_12','huxing',$r['huxing']);
   $r[zongjiamingzi]=getserv('photo_12','price',$r['price']);
    $r[mianjimingzi]=getserv('photo_12','mianji',$r['mianji']);
    $lists[] = $r;
    
}
foreach ($lists as $v){
    // $v[hits]=$v[hits]+1;
    $sql = "UPDATE {$DT_PRE}photo_12 set hits = $v[hits]+1 WHERE itemid = $v[itemid]";
    $db->query($sql);
}



// 			echo($sql);
    // print_r($lists);        
     $a = 5555;
        
echo json_encode($a,JSON_UNESCAPED_UNICODE);



?>
