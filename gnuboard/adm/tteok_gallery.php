<?php
$sub_menu = '960300';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'r');
tteok_setup();

$types = $g5['tteok_gallery_types'];

// 선택수정 · 선택삭제
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	check_demo();
	auth_check_menu($auth, $sub_menu, 'w');
	check_admin_token();

	$chk = isset($_POST['chk']) ? (array)$_POST['chk'] : array();
	if (!count($chk)) alert('처리할 사진을 선택해 주세요.');
	$act = isset($_POST['act_button']) ? $_POST['act_button'] : '';

	foreach ($chk as $k) {
		$k  = (int)$k;
		$id = (int)tteok_post('tg_id', $k);
		if (!$id) continue;
		if ($act === '선택삭제') {
			$row = sql_fetch(" SELECT tg_image FROM ".G5_TTEOK_GALLERY_TABLE." WHERE tg_id = '$id' ");
			sql_query(" DELETE FROM ".G5_TTEOK_GALLERY_TABLE." WHERE tg_id = '$id' ");
			if ($row) tteok_discard_image($row['tg_image']);
			continue;
		}
		$type = tteok_post('tg_type', $k);
		if (!isset($types[$type])) $type = '';
		sql_query(" UPDATE ".G5_TTEOK_GALLERY_TABLE." SET
			tg_type    = '$type',
			tg_caption = '".tteok_post('tg_caption', $k)."',
			tg_order   = '".(int)tteok_post('tg_order', $k)."',
			tg_use     = '".(isset($_POST['tg_use'][$k]) ? 1 : 0)."'
			WHERE tg_id = '$id' ");
	}
	goto_url('./tteok_gallery.php');
}

$result = sql_query(" SELECT * FROM ".G5_TTEOK_GALLERY_TABLE." ORDER BY tg_order, tg_id ");

// 화면 그리드(PC 4열) 칸 수 계산 : 빈칸 없이 채워지는지 안내
$cells = 0;
$r = sql_query(" SELECT tg_type FROM ".G5_TTEOK_GALLERY_TABLE." WHERE tg_use = 1 ");
while ($row = sql_fetch_array($r)) $cells += ($row['tg_type'] === 't1' ? 4 : ($row['tg_type'] === '' ? 1 : 2));

$g5['title'] = '푸드 갤러리';
include_once(G5_ADMIN_PATH.'/admin.head.php');
?>

<div class="local_desc01 local_desc">
	<p>메인 화면 <b>'보는 순간, 이미 배고프다'</b> 갤러리입니다. 사진마다 칸 모양을 골라 크기를 섞을 수 있습니다.</p>
	<p>PC 화면은 한 줄에 4칸입니다. 노출 중인 사진이 차지하는 칸 수가 <b>4의 배수</b>이면 빈칸 없이 꽉 찹니다.
		지금 <b style="color:<?php echo $cells % 4 ? '#c33' : '#2f7346' ?>"><?php echo $cells ?>칸</b><?php echo $cells % 4 ? ' — '.(4 - $cells % 4).'칸이 비어 보일 수 있습니다.' : ' — 딱 맞습니다.' ?>
		(큰 정사각 4칸, 가로형·세로형 2칸, 작은 정사각 1칸)</p>
</div>

<div class="local_ov01 local_ov">
	<a href="./tteok_gallery_form.php" class="ov_listall">새 사진 등록</a>
</div>

<form name="flist" method="post" onsubmit="return tteok_list_submit(this);">
<input type="hidden" name="token" value="">

<div class="tbl_head01 tbl_wrap">
	<table>
	<caption>갤러리 사진 목록</caption>
	<thead>
	<tr>
		<th scope="col"><input type="checkbox" onclick="tteok_check_all(this)" aria-label="전체선택"></th>
		<th scope="col">사진</th>
		<th scope="col">설명(사진 위 글씨)</th>
		<th scope="col">칸 모양</th>
		<th scope="col">순서</th>
		<th scope="col">노출</th>
		<th scope="col">관리</th>
	</tr>
	</thead>
	<tbody>
	<?php for ($i = 0; $row = sql_fetch_array($result); $i++) { ?>
	<tr class="bg<?php echo $i % 2 ?>">
		<td class="td_chk">
			<input type="hidden" name="tg_id[<?php echo $i ?>]" value="<?php echo $row['tg_id'] ?>">
			<input type="checkbox" name="chk[]" value="<?php echo $i ?>" aria-label="선택">
		</td>
		<td class="td_img"><a href="./tteok_gallery_form.php?w=u&amp;tg_id=<?php echo $row['tg_id'] ?>"><?php echo tteok_thumb($row['tg_image'], 96, 72) ?></a></td>
		<td><input type="text" name="tg_caption[<?php echo $i ?>]" value="<?php echo get_text($row['tg_caption']) ?>" class="frm_input" size="22"></td>
		<td>
			<select name="tg_type[<?php echo $i ?>]">
				<?php foreach ($types as $tv => $tl) { ?><option value="<?php echo $tv ?>"<?php echo $row['tg_type'] === $tv ? ' selected' : '' ?>><?php echo $tl ?></option><?php } ?>
			</select>
		</td>
		<td class="td_num"><input type="text" name="tg_order[<?php echo $i ?>]" value="<?php echo (int)$row['tg_order'] ?>" class="frm_input" size="4"></td>
		<td class="td_chk"><input type="checkbox" name="tg_use[<?php echo $i ?>]" value="1"<?php echo $row['tg_use'] ? ' checked' : '' ?>></td>
		<td class="td_mng td_mng_s"><a href="./tteok_gallery_form.php?w=u&amp;tg_id=<?php echo $row['tg_id'] ?>" class="btn btn_03">수정</a></td>
	</tr>
	<?php }
	if ($i === 0) echo '<tr><td colspan="7" class="empty_table">등록된 사진이 없습니다.</td></tr>'; ?>
	</tbody>
	</table>
</div>

<div class="btn_fixed_top">
	<input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value" class="btn btn_03">
	<input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="btn btn_02">
	<a href="./tteok_gallery_form.php" class="btn btn_01">새 사진 등록</a>
</div>
</form>

<?php
include_once(G5_ADMIN_PATH.'/tteok_admin.inc.php');
include_once(G5_ADMIN_PATH.'/admin.tail.php');
