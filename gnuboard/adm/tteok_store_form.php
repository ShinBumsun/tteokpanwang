<?php
$sub_menu = '960400';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'r');
tteok_setup();

$w     = isset($_REQUEST['w']) ? $_REQUEST['w'] : '';
$st_id = isset($_REQUEST['st_id']) ? (int)$_REQUEST['st_id'] : 0;

$cols = array('st_name', 'st_badge', 'st_station', 'st_line', 'st_address', 'st_jibun', 'st_hours', 'st_tel', 'st_instagram');
$st = array('st_id' => 0, 'st_order' => 0, 'st_use' => 1);
foreach ($cols as $c) $st[$c] = '';

if ($w === 'u') {
	$row = sql_fetch(" SELECT * FROM ".G5_TTEOK_STORE_TABLE." WHERE st_id = '$st_id' ");
	if (!$row) alert('존재하지 않는 매장입니다.', './tteok_store.php');
	$st = $row;
} else {
	$row = sql_fetch(" SELECT MAX(st_order) AS mx FROM ".G5_TTEOK_STORE_TABLE);
	$st['st_order'] = ($row ? (int)$row['mx'] : 0) + 10;
}

// 저장
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	check_demo();
	auth_check_menu($auth, $sub_menu, 'w');
	check_admin_token();

	if (tteok_post('st_name') === '') alert('매장명을 입력해 주세요.');

	$set = array();
	foreach ($cols as $c) $set[] = " $c = '".tteok_post($c)."' ";
	$set[] = " st_order = '".(int)tteok_post('st_order')."' ";
	$set[] = " st_use   = '".(isset($_POST['st_use']) ? 1 : 0)."' ";
	$set = implode(',', $set);

	if ($w === 'u') sql_query(" UPDATE ".G5_TTEOK_STORE_TABLE." SET $set WHERE st_id = '$st_id' ");
	else            sql_query(" INSERT INTO ".G5_TTEOK_STORE_TABLE." SET $set ");
	goto_url('./tteok_store.php');
}

$g5['title'] = '매장 '.($w === 'u' ? '수정' : '등록');
include_once(G5_ADMIN_PATH.'/admin.head.php');

$field = function ($name, $label, $ph, $info = '', $size = 40) use ($st) {
	echo '<tr><th scope="row"><label for="'.$name.'">'.$label.'</label></th><td>'
		.'<input type="text" name="'.$name.'" value="'.get_text($st[$name]).'" id="'.$name.'" class="frm_input" size="'.$size.'" placeholder="'.$ph.'">'
		.($info !== '' ? '<span class="frm_info">'.$info.'</span>' : '')
		.'</td></tr>';
};
?>

<form name="fstore" method="post">
<input type="hidden" name="token" value="">
<input type="hidden" name="w" value="<?php echo $w ?>">
<input type="hidden" name="st_id" value="<?php echo (int)$st['st_id'] ?>">

<div class="tbl_frm01 tbl_wrap">
	<table>
	<caption><?php echo $g5['title'] ?></caption>
	<colgroup><col class="grid_4"><col></colgroup>
	<tbody>
	<?php
	$field('st_name', '매장명 <strong class="sound_only">필수</strong>', '예: 떡판왕 을지로3가점', '카드에 크게 나옵니다.');
	$field('st_badge', '영문 뱃지', '예: EULJIRO 3-GA', '카드 왼쪽 위 뱃지입니다. 영문 대문자를 권합니다.', 20);
	$field('st_station', '가까운 역', '예: 을지로3가역', '카드 오른쪽 위에 나옵니다.', 20);
	$field('st_line', '호선', '예: 2·3', '역 이름 앞 초록 동그라미 안에 들어갑니다. 숫자만.', 6);
	$field('st_address', '주소(도로명)', '예: 서울 중구 수표로10길 19 2층', '비우면 \'추후 공지\' 로 나옵니다. 지도 검색에도 쓰입니다.', 50);
	$field('st_jibun', '지번', '예: 초동 156-9', '주소 아래 작은 글씨로 나옵니다. 비워도 됩니다.', 30);
	$field('st_hours', '영업시간', '예: 11:00 - 22:00 · 주말 휴무', '보이는 그대로 적습니다.', 40);
	$field('st_tel', '전화번호', '예: 02-564-2120', '누르면 바로 전화가 걸립니다.', 20);
	$field('st_instagram', '인스타그램', 'https://www.instagram.com/...', '이 매장 전용 계정이 있으면 카드에 인스타그램 버튼이 생깁니다.', 50);
	?>
	<tr>
		<th scope="row"><label for="st_order">순서</label></th>
		<td><input type="text" name="st_order" value="<?php echo (int)$st['st_order'] ?>" id="st_order" class="frm_input" size="5"> <span class="frm_info">숫자가 작을수록 앞에 나옵니다.</span></td>
	</tr>
	<tr>
		<th scope="row">노출</th>
		<td><label><input type="checkbox" name="st_use" value="1"<?php echo $st['st_use'] ? ' checked' : '' ?>> 화면에 보이기</label></td>
	</tr>
	</tbody>
	</table>
</div>

<div class="btn_fixed_top">
	<a href="./tteok_store.php" class="btn btn_02">목록</a>
	<input type="submit" value="저장" class="btn btn_submit">
</div>
</form>

<?php
include_once(G5_ADMIN_PATH.'/tteok_admin.inc.php');
include_once(G5_ADMIN_PATH.'/admin.tail.php');
