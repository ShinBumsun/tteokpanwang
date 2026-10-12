<?php
$sub_menu = '960100';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'r');
tteok_setup();

$w     = isset($_REQUEST['w']) ? $_REQUEST['w'] : '';
$ts_id = isset($_REQUEST['ts_id']) ? (int)$_REQUEST['ts_id'] : 0;

$ts = array('ts_id' => 0, 'ts_order' => 0, 'ts_label' => '', 'ts_name' => '', 'ts_en' => '', 'ts_desc' => '',
	'ts_price_note' => '', 'ts_price' => 0, 'ts_from' => 0, 'ts_image' => '', 'ts_use' => 1);

if ($w === 'u') {
	$row = sql_fetch(" SELECT * FROM ".G5_TTEOK_SIG_TABLE." WHERE ts_id = '$ts_id' ");
	if (!$row) alert('존재하지 않는 카드입니다.', './tteok_sig.php');
	$ts = $row;
} else {
	$row = sql_fetch(" SELECT MAX(ts_order) AS mx FROM ".G5_TTEOK_SIG_TABLE);
	$ts['ts_order'] = ($row ? (int)$row['mx'] : 0) + 10;
}

// 저장
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	check_demo();
	auth_check_menu($auth, $sub_menu, 'w');
	check_admin_token();

	$name = tteok_post('ts_name');   // 그누보드가 $_POST 를 이미 이스케이프해 둠
	if ($name === '') alert('메뉴명을 입력해 주세요.');

	$image = tteok_upload_image('ts_image', 'sig', $ts['ts_image']);
	$set = " ts_label      = '".tteok_post('ts_label')."',
	         ts_name       = '$name',
	         ts_en         = '".tteok_post('ts_en')."',
	         ts_desc       = '".tteok_post('ts_desc')."',
	         ts_price_note = '".tteok_post('ts_price_note')."',
	         ts_price      = '".(int)preg_replace('/[^0-9]/', '', $_POST['ts_price'])."',
	         ts_from       = '".(isset($_POST['ts_from']) ? 1 : 0)."',
	         ts_order      = '".(int)$_POST['ts_order']."',
	         ts_use        = '".(isset($_POST['ts_use']) ? 1 : 0)."',
	         ts_image      = '".sql_escape_string($image)."' ";

	if ($w === 'u') sql_query(" UPDATE ".G5_TTEOK_SIG_TABLE." SET $set WHERE ts_id = '$ts_id' ");
	else            sql_query(" INSERT INTO ".G5_TTEOK_SIG_TABLE." SET $set ");

	// DB 가 바뀐 뒤에 예전 파일을 지운다 (저장 실패 시 사진만 사라지는 일이 없게)
	if ($image !== $ts['ts_image']) tteok_discard_image($ts['ts_image']);

	goto_url('./tteok_sig.php');
}

$g5['title'] = '시그니처 메뉴 '.($w === 'u' ? '수정' : '등록');
include_once(G5_ADMIN_PATH.'/admin.head.php');
?>

<form name="fsig" method="post" enctype="multipart/form-data" onsubmit="return fsig_submit(this);">
<input type="hidden" name="token" value="">
<input type="hidden" name="w" value="<?php echo $w ?>">
<input type="hidden" name="ts_id" value="<?php echo (int)$ts['ts_id'] ?>">

<div class="tbl_frm01 tbl_wrap">
	<table>
	<caption><?php echo $g5['title'] ?></caption>
	<colgroup><col class="grid_4"><col></colgroup>
	<tbody>
	<tr>
		<th scope="row"><label for="ts_name">메뉴명 <strong class="sound_only">필수</strong></label></th>
		<td><input type="text" name="ts_name" value="<?php echo get_text($ts['ts_name']) ?>" id="ts_name" required class="frm_input required" size="40" placeholder="예: 차돌 즉석떡볶이"></td>
	</tr>
	<tr>
		<th scope="row"><label for="ts_en">영문명</label></th>
		<td>
			<input type="text" name="ts_en" value="<?php echo get_text($ts['ts_en']) ?>" id="ts_en" class="frm_input" size="40" placeholder="예: CHADOL TTEOKBOKKI">
			<span class="frm_info">메뉴명 위에 작은 빨간 글씨로 나옵니다.</span>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="ts_label">라벨</label></th>
		<td>
			<input type="text" name="ts_label" value="<?php echo get_text($ts['ts_label']) ?>" id="ts_label" class="frm_input" size="20" placeholder="예: BEST">
			<span class="frm_info">사진 왼쪽 위 노란 뱃지입니다. 예: BEST, BEST SIDE, BEST 안주. 비우면 뱃지가 나오지 않습니다.</span>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="ts_desc">한 줄 소개</label></th>
		<td><textarea name="ts_desc" id="ts_desc" class="frm_input" rows="3"><?php echo get_text($ts['ts_desc']) ?></textarea></td>
	</tr>
	<tr>
		<th scope="row"><label for="ts_price">가격</label></th>
		<td>
			<input type="text" name="ts_price_note" value="<?php echo get_text($ts['ts_price_note']) ?>" class="frm_input" size="10" placeholder="예: 2인 세트" aria-label="가격 앞 설명">
			<input type="text" name="ts_price" value="<?php echo $ts['ts_price'] ? number_format($ts['ts_price']) : '' ?>" id="ts_price" class="frm_input" size="10" placeholder="20000"> 원
			<label style="margin-left:10px"><input type="checkbox" name="ts_from" value="1"<?php echo $ts['ts_from'] ? ' checked' : '' ?>> 뒤에 '~' 붙이기</label>
			<span class="frm_info">화면에는 <b>2인 세트 20,000원~</b> 처럼 나옵니다. 앞 설명은 비워도 됩니다. 가격을 비우면 '가격 추후 공지' 로 나옵니다.</span>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="ts_image">사진</label></th>
		<td>
			<?php if ($u = tteok_image_url($ts['ts_image'])) { ?>
			<p class="tteok_preview"><img src="<?php echo $u ?>" alt="등록된 사진"></p>
			<p class="tteok_preview"><label><input type="checkbox" name="ts_image_del" value="1"> 이 사진 지우기</label></p>
			<?php } ?>
			<input type="file" name="ts_image" id="ts_image" accept="image/jpeg,image/png,image/webp">
			<span class="frm_info">가로:세로 4:3 비율로 잘려 보입니다. jpg·png·webp, 8MB 까지. 가로 1000~1400px 정도면 충분합니다.<br>사진이 없으면 영문명이 적힌 'PHOTO COMING SOON' 칸이 대신 나옵니다.</span>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="ts_order">순서</label></th>
		<td>
			<input type="text" name="ts_order" value="<?php echo (int)$ts['ts_order'] ?>" id="ts_order" class="frm_input" size="5">
			<span class="frm_info">숫자가 작을수록 앞에 나옵니다. 가장 앞 카드가 크게 나옵니다.</span>
		</td>
	</tr>
	<tr>
		<th scope="row">노출</th>
		<td><label><input type="checkbox" name="ts_use" value="1"<?php echo $ts['ts_use'] ? ' checked' : '' ?>> 화면에 보이기</label></td>
	</tr>
	</tbody>
	</table>
</div>

<div class="btn_fixed_top">
	<a href="./tteok_sig.php" class="btn btn_02">목록</a>
	<input type="submit" value="저장" class="btn btn_submit">
</div>
</form>

<script>
function fsig_submit(f) {
	if (!f.ts_name.value.replace(/\s/g, '')) { alert('메뉴명을 입력해 주세요.'); f.ts_name.focus(); return false; }
	return true;
}
</script>

<?php
include_once(G5_ADMIN_PATH.'/tteok_admin.inc.php');
include_once(G5_ADMIN_PATH.'/admin.tail.php');
