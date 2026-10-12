<?php
$sub_menu = '960100';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'r');
tteok_setup();

// 선택수정 · 선택삭제
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	check_demo();
	auth_check_menu($auth, $sub_menu, 'w');
	check_admin_token();

	$chk = isset($_POST['chk']) ? (array)$_POST['chk'] : array();
	if (!count($chk)) alert('처리할 항목을 선택해 주세요.');
	$act = isset($_POST['act_button']) ? $_POST['act_button'] : '';

	foreach ($chk as $k) {
		$k  = (int)$k;
		$id = isset($_POST['ts_id'][$k]) ? (int)$_POST['ts_id'][$k] : 0;
		if (!$id) continue;
		if ($act === '선택삭제') {
			$row = sql_fetch(" SELECT ts_image FROM ".G5_TTEOK_SIG_TABLE." WHERE ts_id = '$id' ");
			sql_query(" DELETE FROM ".G5_TTEOK_SIG_TABLE." WHERE ts_id = '$id' ");
			if ($row) tteok_discard_image($row['ts_image']);
		} else {
			$order = isset($_POST['ts_order'][$k]) ? (int)$_POST['ts_order'][$k] : 0;
			$use   = isset($_POST['ts_use'][$k]) ? 1 : 0;
			sql_query(" UPDATE ".G5_TTEOK_SIG_TABLE." SET ts_order = '$order', ts_use = '$use' WHERE ts_id = '$id' ");
		}
	}
	goto_url('./tteok_sig.php');
}

$result = sql_query(" SELECT * FROM ".G5_TTEOK_SIG_TABLE." ORDER BY ts_order, ts_id ");
$total  = tteok_count(G5_TTEOK_SIG_TABLE);

$g5['title'] = '시그니처 메뉴';
include_once(G5_ADMIN_PATH.'/admin.head.php');
?>

<div class="local_desc01 local_desc">
	<p>메인 화면 <b>'왕의 한 판을 만나보세요'</b> 섹션의 메뉴 카드입니다. 순서가 가장 앞인 카드가 크게(세로로 두 칸) 나옵니다.</p>
	<p>카드가 4개일 때 화면이 가장 꽉 찹니다. 가격·이름 등은 <b>수정</b>에서, 순서·노출은 목록에서 바로 바꿀 수 있습니다.</p>
</div>

<div class="local_ov01 local_ov">
	<span class="btn_ov01"><span class="ov_txt">등록된 카드</span><span class="ov_num"> <?php echo number_format($total) ?>개</span></span>
	<a href="./tteok_sig_form.php" class="ov_listall">새 카드 등록</a>
</div>

<form name="flist" method="post" onsubmit="return tteok_list_submit(this);">
<input type="hidden" name="token" value="">

<div class="tbl_head01 tbl_wrap">
	<table>
	<caption>시그니처 메뉴 목록</caption>
	<thead>
	<tr>
		<th scope="col"><input type="checkbox" onclick="tteok_check_all(this)" aria-label="전체선택"></th>
		<th scope="col">사진</th>
		<th scope="col">라벨</th>
		<th scope="col">메뉴명</th>
		<th scope="col">가격</th>
		<th scope="col">순서</th>
		<th scope="col">노출</th>
		<th scope="col">관리</th>
	</tr>
	</thead>
	<tbody>
	<?php
	for ($i = 0; $row = sql_fetch_array($result); $i++) {
		$price = ($row['ts_price_note'] !== '' ? get_text($row['ts_price_note']).' ' : '').number_format($row['ts_price']).'원'.($row['ts_from'] ? '~' : '');
	?>
	<tr class="bg<?php echo $i % 2 ?>">
		<td class="td_chk">
			<input type="hidden" name="ts_id[<?php echo $i ?>]" value="<?php echo $row['ts_id'] ?>">
			<input type="checkbox" name="chk[]" value="<?php echo $i ?>" aria-label="선택">
		</td>
		<td class="td_img"><?php echo tteok_thumb($row['ts_image']) ?></td>
		<td><?php echo get_text($row['ts_label']) ?></td>
		<td class="td_left"><a href="./tteok_sig_form.php?w=u&amp;ts_id=<?php echo $row['ts_id'] ?>"><b><?php echo get_text($row['ts_name']) ?></b></a> <span style="color:#999"><?php echo get_text($row['ts_en']) ?></span></td>
		<td class="td_num" style="white-space:nowrap"><?php echo $row['ts_price'] ? $price : '-' ?></td>
		<td class="td_num"><input type="text" name="ts_order[<?php echo $i ?>]" value="<?php echo (int)$row['ts_order'] ?>" class="frm_input" size="4"></td>
		<td class="td_chk"><input type="checkbox" name="ts_use[<?php echo $i ?>]" value="1"<?php echo $row['ts_use'] ? ' checked' : '' ?>></td>
		<td class="td_mng td_mng_s"><a href="./tteok_sig_form.php?w=u&amp;ts_id=<?php echo $row['ts_id'] ?>" class="btn btn_03">수정</a></td>
	</tr>
	<?php }
	if ($i === 0) echo '<tr><td colspan="8" class="empty_table">등록된 카드가 없습니다.</td></tr>'; ?>
	</tbody>
	</table>
</div>

<div class="btn_fixed_top">
	<input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value" class="btn btn_03">
	<input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="btn btn_02">
	<a href="./tteok_sig_form.php" class="btn btn_01">새 카드 등록</a>
</div>
</form>

<?php
include_once(G5_ADMIN_PATH.'/tteok_admin.inc.php');
include_once(G5_ADMIN_PATH.'/admin.tail.php');
