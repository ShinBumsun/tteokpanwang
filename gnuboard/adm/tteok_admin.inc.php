<?php
/* 떡판왕 관리 화면 공용 스크립트 · 스타일 (목록 화면 아래에서 include) */
if (!defined('_GNUBOARD_')) exit;
?>
<style>
.td_img {width:80px;}
.tteok_preview {margin:0 0 8px;}
.tteok_preview img {width:220px; height:165px; object-fit:cover; border:2px solid #171717; border-radius:8px;}
.tteok_tabs {display:flex; flex-wrap:wrap; gap:6px; margin:0 0 14px;}
.tteok_tabs a {padding:8px 14px; border:1px solid #ccc; border-radius:20px; background:#fff; color:#333;}
.tteok_tabs a.on {background:#E92828; border-color:#E92828; color:#fff; font-weight:bold;}
.tteok_new td {background:#fff8ef;}
</style>
<script>
function tteok_check_all(el) {
	var chk = el.form.querySelectorAll('input[name="chk[]"]');
	for (var i = 0; i < chk.length; i++) chk[i].checked = el.checked;
}
function tteok_list_submit(f) {
	var act = document.pressed || '';
	if (act === '추가') return true;
	var cnt = f.querySelectorAll('input[name="chk[]"]:checked').length;
	if (!cnt) { alert(act + '할 항목을 선택해 주세요.'); return false; }
	if (act === '선택삭제' && !confirm('선택한 ' + cnt + '건을 정말 삭제하시겠습니까?\n삭제하면 되돌릴 수 없습니다.')) return false;
	return true;
}
// 목록에서 값을 고치면 그 줄은 자동으로 선택
document.addEventListener('input', function (e) {
	var tr = e.target.closest && e.target.closest('tbody tr');
	if (!tr || e.target.name === 'chk[]') return;
	var c = tr.querySelector('input[name="chk[]"]');
	if (c) c.checked = true;
});
document.addEventListener('change', function (e) {
	var tr = e.target.closest && e.target.closest('tbody tr');
	if (!tr || e.target.name === 'chk[]' || e.target.type !== 'checkbox') return;
	var c = tr.querySelector('input[name="chk[]"]');
	if (c) c.checked = true;
});
</script>
