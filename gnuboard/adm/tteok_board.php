<?php
$sub_menu = '960200';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'r');
tteok_setup();

$cats = array();
$res = sql_query(" SELECT * FROM ".G5_TTEOK_CAT_TABLE." ORDER BY tc_order, tc_id ");
while ($r = sql_fetch_array($res)) $cats[(int)$r['tc_id']] = $r;

$tc = isset($_REQUEST['tc']) ? (int)$_REQUEST['tc'] : 0;
if ($tc && !isset($cats[$tc])) $tc = 0;
$back = './tteok_board.php'.($tc ? '?tc='.$tc : '');

// 저장 : 추가 · 선택수정 · 선택삭제
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	check_demo();
	auth_check_menu($auth, $sub_menu, 'w');
	check_admin_token();
	$act = isset($_POST['act_button']) ? $_POST['act_button'] : '';

	if ($act === '추가') {
		$name = tteok_post('new_name');
		$cat  = (int)tteok_post('new_tc');
		if ($name === '') alert('추가할 메뉴명을 입력해 주세요.');
		if (!isset($cats[$cat])) alert('분류를 골라 주세요.');
		$row = sql_fetch(" SELECT MAX(ti_order) AS mx FROM ".G5_TTEOK_ITEM_TABLE." WHERE tc_id = '$cat' ");
		sql_query(" INSERT INTO ".G5_TTEOK_ITEM_TABLE." SET
			tc_id = '$cat', ti_order = '".(($row ? (int)$row['mx'] : 0) + 10)."', ti_name = '$name',
			ti_sub = '".tteok_post('new_sub')."', ti_price = '".(int)preg_replace('/[^0-9]/', '', tteok_post('new_price'))."',
			ti_best = '".(isset($_POST['new_best']) ? 1 : 0)."', ti_use = 1 ");
		goto_url($back);
	}

	$chk = isset($_POST['chk']) ? (array)$_POST['chk'] : array();
	if (!count($chk)) alert('처리할 항목을 선택해 주세요.');
	foreach ($chk as $k) {
		$k  = (int)$k;
		$id = (int)tteok_post('ti_id', $k);
		if (!$id) continue;
		if ($act === '선택삭제') {
			sql_query(" DELETE FROM ".G5_TTEOK_ITEM_TABLE." WHERE ti_id = '$id' ");
			continue;
		}
		$name = tteok_post('ti_name', $k);
		if ($name === '') continue;
		$cat = (int)tteok_post('tc_id', $k);
		sql_query(" UPDATE ".G5_TTEOK_ITEM_TABLE." SET
			tc_id    = '".(isset($cats[$cat]) ? $cat : 0)."',
			ti_name  = '$name',
			ti_sub   = '".tteok_post('ti_sub', $k)."',
			ti_price = '".(int)preg_replace('/[^0-9]/', '', tteok_post('ti_price', $k))."',
			ti_order = '".(int)tteok_post('ti_order', $k)."',
			ti_best  = '".(isset($_POST['ti_best'][$k]) ? 1 : 0)."',
			ti_use   = '".(isset($_POST['ti_use'][$k]) ? 1 : 0)."'
			WHERE ti_id = '$id' ");
	}
	goto_url($back);
}

$where  = $tc ? " WHERE i.tc_id = '$tc' " : '';
$result = sql_query(" SELECT i.* FROM ".G5_TTEOK_ITEM_TABLE." i
	LEFT JOIN ".G5_TTEOK_CAT_TABLE." c ON c.tc_id = i.tc_id
	$where ORDER BY c.tc_order, c.tc_id, i.ti_order, i.ti_id ");

$cat_select = function ($name, $sel) use ($cats) {
	$h = '<select name="'.$name.'">';
	foreach ($cats as $id => $c) $h .= '<option value="'.$id.'"'.($id == $sel ? ' selected' : '').'>'.get_text($c['tc_title']).'</option>';
	return $h.'</select>';
};

$g5['title'] = '전체 메뉴';
include_once(G5_ADMIN_PATH.'/admin.head.php');
?>

<div class="local_desc01 local_desc">
	<p>메인 화면 <b>'전체 메뉴'</b> 판의 항목입니다. 표에서 바로 고치고 <b>선택수정</b>을 누르면 저장됩니다. (고친 줄은 자동으로 체크됩니다)</p>
	<p>가격은 숫자만 넣으면 됩니다. <b>BEST</b> 를 켜면 이름 옆에 빨간 BEST 뱃지가 붙습니다. 분류 이름·순서는 <a href="./tteok_cat.php"><b>분류 관리</b></a>에서 바꿉니다.</p>
</div>

<nav class="tteok_tabs" aria-label="분류">
	<a href="./tteok_board.php"<?php echo $tc ? '' : ' class="on"' ?>>전체</a>
	<?php foreach ($cats as $id => $c) { ?>
	<a href="./tteok_board.php?tc=<?php echo $id ?>"<?php echo $tc === $id ? ' class="on"' : '' ?>><?php echo get_text($c['tc_title']) ?><?php echo $c['tc_use'] ? '' : ' (숨김)' ?></a>
	<?php } ?>
	<a href="./tteok_cat.php" style="margin-left:auto">분류 관리 →</a>
</nav>

<form name="flist" method="post" action="<?php echo $back ?>" onsubmit="return tteok_list_submit(this);">
<input type="hidden" name="token" value="">

<div class="tbl_head01 tbl_wrap">
	<table>
	<caption>전체 메뉴 항목</caption>
	<thead>
	<tr>
		<th scope="col"><input type="checkbox" onclick="tteok_check_all(this)" aria-label="전체선택"></th>
		<th scope="col">분류</th>
		<th scope="col">메뉴명</th>
		<th scope="col">작은 글씨</th>
		<th scope="col">가격(원)</th>
		<th scope="col">BEST</th>
		<th scope="col">순서</th>
		<th scope="col">노출</th>
	</tr>
	</thead>
	<tbody>
	<?php for ($i = 0; $row = sql_fetch_array($result); $i++) { ?>
	<tr class="bg<?php echo $i % 2 ?>">
		<td class="td_chk">
			<input type="hidden" name="ti_id[<?php echo $i ?>]" value="<?php echo $row['ti_id'] ?>">
			<input type="checkbox" name="chk[]" value="<?php echo $i ?>" aria-label="선택">
		</td>
		<td><?php echo $cat_select('tc_id['.$i.']', $row['tc_id']) ?></td>
		<td class="td_left"><input type="text" name="ti_name[<?php echo $i ?>]" value="<?php echo get_text($row['ti_name']) ?>" class="frm_input" size="20"></td>
		<td><input type="text" name="ti_sub[<?php echo $i ?>]" value="<?php echo get_text($row['ti_sub']) ?>" class="frm_input" size="6"></td>
		<td><input type="text" name="ti_price[<?php echo $i ?>]" value="<?php echo number_format($row['ti_price']) ?>" class="frm_input" size="8" style="text-align:right"></td>
		<td class="td_chk"><input type="checkbox" name="ti_best[<?php echo $i ?>]" value="1"<?php echo $row['ti_best'] ? ' checked' : '' ?>></td>
		<td class="td_num"><input type="text" name="ti_order[<?php echo $i ?>]" value="<?php echo (int)$row['ti_order'] ?>" class="frm_input" size="4"></td>
		<td class="td_chk"><input type="checkbox" name="ti_use[<?php echo $i ?>]" value="1"<?php echo $row['ti_use'] ? ' checked' : '' ?>></td>
	</tr>
	<?php }
	if ($i === 0) echo '<tr><td colspan="8" class="empty_table">등록된 메뉴가 없습니다.</td></tr>'; ?>
	</tbody>
	<tfoot>
	<tr class="tteok_new">
		<td class="td_chk"><b>새 메뉴</b></td>
		<td><?php echo $cats ? $cat_select('new_tc', $tc ?: key($cats)) : '<a href="./tteok_cat.php">분류를 먼저 만드세요</a>' ?></td>
		<td class="td_left"><input type="text" name="new_name" value="" class="frm_input" size="20" placeholder="메뉴명"></td>
		<td><input type="text" name="new_sub" value="" class="frm_input" size="6" placeholder="예: 1EA"></td>
		<td><input type="text" name="new_price" value="" class="frm_input" size="8" placeholder="3000" style="text-align:right"></td>
		<td class="td_chk"><input type="checkbox" name="new_best" value="1" aria-label="BEST"></td>
		<td colspan="2"><input type="submit" name="act_button" value="추가" onclick="document.pressed=this.value" class="btn btn_01"></td>
	</tr>
	</tfoot>
	</table>
</div>

<div class="btn_fixed_top">
	<input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value" class="btn btn_03">
	<input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="btn btn_02">
</div>
</form>

<?php
include_once(G5_ADMIN_PATH.'/tteok_admin.inc.php');
include_once(G5_ADMIN_PATH.'/admin.tail.php');
