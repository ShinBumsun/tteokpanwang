<?php
$sub_menu = '960300';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'r');
tteok_setup();

$types = $g5['tteok_gallery_types'];
$w     = isset($_REQUEST['w']) ? $_REQUEST['w'] : '';
$tg_id = isset($_REQUEST['tg_id']) ? (int)$_REQUEST['tg_id'] : 0;

$tg = array('tg_id' => 0, 'tg_order' => 0, 'tg_type' => '', 'tg_caption' => '', 'tg_en' => '', 'tg_image' => '', 'tg_use' => 1);
if ($w === 'u') {
	$row = sql_fetch(" SELECT * FROM ".G5_TTEOK_GALLERY_TABLE." WHERE tg_id = '$tg_id' ");
	if (!$row) alert('존재하지 않는 사진입니다.', './tteok_gallery.php');
	$tg = $row;
} else {
	$row = sql_fetch(" SELECT MAX(tg_order) AS mx FROM ".G5_TTEOK_GALLERY_TABLE);
	$tg['tg_order'] = ($row ? (int)$row['mx'] : 0) + 10;
}

// 저장
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	check_demo();
	auth_check_menu($auth, $sub_menu, 'w');
	check_admin_token();

	$image = tteok_upload_image('tg_image', 'gal', $tg['tg_image']);
	if ($image === '') alert('사진을 올려 주세요. 갤러리는 사진이 있어야 나옵니다.');

	$type = tteok_post('tg_type');
	if (!isset($types[$type])) $type = '';

	$set = " tg_type    = '$type',
	         tg_caption = '".tteok_post('tg_caption')."',
	         tg_en      = '".tteok_post('tg_en')."',
	         tg_order   = '".(int)tteok_post('tg_order')."',
	         tg_use     = '".(isset($_POST['tg_use']) ? 1 : 0)."',
	         tg_image   = '".sql_escape_string($image)."' ";

	if ($w === 'u') sql_query(" UPDATE ".G5_TTEOK_GALLERY_TABLE." SET $set WHERE tg_id = '$tg_id' ");
	else            sql_query(" INSERT INTO ".G5_TTEOK_GALLERY_TABLE." SET $set ");

	if ($image !== $tg['tg_image']) tteok_discard_image($tg['tg_image']);
	goto_url('./tteok_gallery.php');
}

$g5['title'] = '푸드 갤러리 '.($w === 'u' ? '수정' : '등록');
include_once(G5_ADMIN_PATH.'/admin.head.php');
?>

<form name="fgal" method="post" enctype="multipart/form-data">
<input type="hidden" name="token" value="">
<input type="hidden" name="w" value="<?php echo $w ?>">
<input type="hidden" name="tg_id" value="<?php echo (int)$tg['tg_id'] ?>">

<div class="tbl_frm01 tbl_wrap">
	<table>
	<caption><?php echo $g5['title'] ?></caption>
	<colgroup><col class="grid_4"><col></colgroup>
	<tbody>
	<tr>
		<th scope="row"><label for="tg_image">사진 <strong class="sound_only">필수</strong></label></th>
		<td>
			<?php if ($u = tteok_image_url($tg['tg_image'])) { ?>
			<p class="tteok_preview"><img src="<?php echo $u ?>" alt="등록된 사진"></p>
			<?php } ?>
			<input type="file" name="tg_image" id="tg_image" accept="image/jpeg,image/png,image/webp"<?php echo $tg['tg_image'] ? '' : ' required' ?>>
			<span class="frm_info">jpg·png·webp, 8MB 까지. 칸 모양에 맞춰 가운데 기준으로 잘려 보입니다. 가로 1200px 정도면 충분합니다.<br>새 파일을 고르면 기존 사진과 바뀝니다.</span>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="tg_caption">설명</label></th>
		<td>
			<input type="text" name="tg_caption" value="<?php echo get_text($tg['tg_caption']) ?>" id="tg_caption" class="frm_input" size="30" placeholder="예: 차돌 즉석떡볶이">
			<span class="frm_info">사진 왼쪽 아래 검은 말풍선에 나옵니다.</span>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="tg_en">영문 이름</label></th>
		<td>
			<input type="text" name="tg_en" value="<?php echo get_text($tg['tg_en']) ?>" id="tg_en" class="frm_input" size="30" placeholder="예: CHADOL">
			<span class="frm_info">화면에는 보이지 않고, 사진을 불러오지 못할 때만 대신 나옵니다. 비워도 됩니다.</span>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="tg_type">칸 모양</label></th>
		<td>
			<select name="tg_type" id="tg_type">
				<?php foreach ($types as $tv => $tl) { ?><option value="<?php echo $tv ?>"<?php echo $tg['tg_type'] === $tv ? ' selected' : '' ?>><?php echo $tl ?></option><?php } ?>
			</select>
			<span class="frm_info">세로로 긴 사진은 세로형, 가로로 긴 사진은 가로형이 잘 맞습니다.</span>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="tg_order">순서</label></th>
		<td><input type="text" name="tg_order" value="<?php echo (int)$tg['tg_order'] ?>" id="tg_order" class="frm_input" size="5"> <span class="frm_info">숫자가 작을수록 앞에 나옵니다.</span></td>
	</tr>
	<tr>
		<th scope="row">노출</th>
		<td><label><input type="checkbox" name="tg_use" value="1"<?php echo $tg['tg_use'] ? ' checked' : '' ?>> 화면에 보이기</label></td>
	</tr>
	</tbody>
	</table>
</div>

<div class="btn_fixed_top">
	<a href="./tteok_gallery.php" class="btn btn_02">목록</a>
	<input type="submit" value="저장" class="btn btn_submit">
</div>
</form>

<?php
include_once(G5_ADMIN_PATH.'/tteok_admin.inc.php');
include_once(G5_ADMIN_PATH.'/admin.tail.php');
