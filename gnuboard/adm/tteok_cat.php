<?php
$sub_menu = '960200';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'r');
tteok_setup();

// 저장 : 추가 · 선택수정 · 선택삭제
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	check_demo();
	auth_check_menu($auth, $sub_menu, 'w');
	check_admin_token();
	$act = isset($_POST['act_button']) ? $_POST['act_button'] : '';

	if ($act === '추가') {
		$title = tteok_post('new_title');
		if ($title === '') alert('분류 이름을 입력해 주세요.');
		$row = sql_fetch(" SELECT MAX(tc_order) AS mx FROM ".G5_TTEOK_CAT_TABLE);
		sql_query(" INSERT INTO ".G5_TTEOK_CAT_TABLE." SET
			tc_order = '".(($row ? (int)$row['mx'] : 0) + 10)."', tc_title = '$title',
			tc_en = '".tteok_post('new_en')."', tc_note = '".tteok_post('new_note')."', tc_use = 1 ");
		goto_url('./tteok_cat.php');
	}

	$chk = isset($_POST['chk']) ? (array)$_POST['chk'] : array();
	if (!count($chk)) alert('처리할 분류를 선택해 주세요.');
	foreach ($chk as $k) {
		$k  = (int)$k;
		$id = (int)tteok_post('tc_id', $k);
		if (!$id) continue;
		if ($act === '선택삭제') {
			$n = sql_fetch(" SELECT COUNT(*) AS cnt FROM ".G5_TTEOK_ITEM_TABLE." WHERE tc_id = '$id' ");
			if ($n && (int)$n['cnt'] > 0) alert('메뉴가 들어 있는 분류는 지울 수 없습니다. 메뉴를 먼저 옮기거나 지워 주세요.');
			sql_query(" DELETE FROM ".G5_TTEOK_CAT_TABLE." WHERE tc_id = '$id' ");
			continue;
		}
		$title = tteok_post('tc_title', $k);
		if ($title === '') continue;
		sql_query(" UPDATE ".G5_TTEOK_CAT_TABLE." SET
			tc_title = '$title',
			tc_en    = '".tteok_post('tc_en', $k)."',
			tc_note  = '".tteok_post('tc_note', $k)."',
			tc_order = '".(int)tteok_post('tc_order', $k)."',
			tc_use   = '".(isset($_POST['tc_use'][$k]) ? 1 : 0)."'
			WHERE tc_id = '$id' ");
	}
	goto_url('./tteok_cat.php');
}

$result = sql_query(" SELECT c.*, (SELECT COUNT(*) FROM ".G5_TTEOK_ITEM_TABLE." i WHERE i.tc_id = c.tc_id) AS cnt
	FROM ".G5_TTEOK_CAT_TABLE." c ORDER BY c.tc_order, c.tc_id ");

$g5['title'] = '전체 메뉴 · 분류 관리';
include_once(G5_ADMIN_PATH.'/admin.head.php');
?>

<div class="local_desc01 local_desc">
	<p>전체 메뉴판의 분류(즉석떡볶이, 사리 및 토핑 …)입니다. 순서가 곧 화면 순서이고, PC 에서는 3단으로 자동 배치됩니다.</p>
	<p><b>뱃지</b>는 분류 이름 옆의 작은 검은 뱃지입니다. 예: 저녁. 메뉴가 하나도 없는 분류는 화면에 나오지 않습니다.</p>
</div>

<nav class="tteok_tabs"><a href="./tteok_board.php">← 전체 메뉴 항목으로</a></nav>

<form name="flist" method="post" onsubmit="return tteok_list_submit(this);">
<input type="hidden" name="token" value="">

<div class="tbl_head01 tbl_wrap">
	<table>
	<caption>분류 목록</caption>
	<thead>
	<tr>
		<th scope="col"><input type="checkbox" onclick="tteok_check_all(this)" aria-label="전체선택"></th>
		<th scope="col">분류 이름</th>
		<th scope="col">영문 (오른쪽 작은 글씨)</th>
		<th scope="col">뱃지</th>
		<th scope="col">메뉴 수</th>
		<th scope="col">순서</th>
		<th scope="col">노출</th>
	</tr>
	</thead>
	<tbody>
	<?php for ($i = 0; $row = sql_fetch_array($result); $i++) { ?>
	<tr class="bg<?php echo $i % 2 ?>">
		<td class="td_chk">
			<input type="hidden" name="tc_id[<?php echo $i ?>]" value="<?php echo $row['tc_id'] ?>">
			<input type="checkbox" name="chk[]" value="<?php echo $i ?>" aria-label="선택">
		</td>
		<td><input type="text" name="tc_title[<?php echo $i ?>]" value="<?php echo get_text($row['tc_title']) ?>" class="frm_input" size="16"></td>
		<td><input type="text" name="tc_en[<?php echo $i ?>]" value="<?php echo get_text($row['tc_en']) ?>" class="frm_input" size="16"></td>
		<td><input type="text" name="tc_note[<?php echo $i ?>]" value="<?php echo get_text($row['tc_note']) ?>" class="frm_input" size="6"></td>
		<td class="td_num"><a href="./tteok_board.php?tc=<?php echo $row['tc_id'] ?>"><?php echo (int)$row['cnt'] ?>개</a></td>
		<td class="td_num"><input type="text" name="tc_order[<?php echo $i ?>]" value="<?php echo (int)$row['tc_order'] ?>" class="frm_input" size="4"></td>
		<td class="td_chk"><input type="checkbox" name="tc_use[<?php echo $i ?>]" value="1"<?php echo $row['tc_use'] ? ' checked' : '' ?>></td>
	</tr>
	<?php }
	if ($i === 0) echo '<tr><td colspan="7" class="empty_table">분류가 없습니다.</td></tr>'; ?>
	</tbody>
	<tfoot>
	<tr class="tteok_new">
		<td class="td_chk"><b>새 분류</b></td>
		<td><input type="text" name="new_title" value="" class="frm_input" size="16" placeholder="예: 세트 메뉴"></td>
		<td><input type="text" name="new_en" value="" class="frm_input" size="16" placeholder="예: SET MENU"></td>
		<td><input type="text" name="new_note" value="" class="frm_input" size="6"></td>
		<td colspan="3"><input type="submit" name="act_button" value="추가" onclick="document.pressed=this.value" class="btn btn_01"></td>
	</tr>
	</tfoot>
	</table>
</div>

<div class="btn_fixed_top">
	<a href="./tteok_board.php" class="btn btn_02">메뉴 항목</a>
	<input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value" class="btn btn_03">
	<input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="btn btn_02">
</div>
</form>

<?php
include_once(G5_ADMIN_PATH.'/tteok_admin.inc.php');
include_once(G5_ADMIN_PATH.'/admin.tail.php');
