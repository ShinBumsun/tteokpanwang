/* =========================================================
	떡판왕 콘텐츠 데이터
	- 메뉴/가격/매장정보/갤러리는 이 파일만 수정하면 됩니다.
	- 값이 비어 있으면('' 또는 null) 화면에 '추후 공지'로 표시됩니다.
	- image: 'images/pic_menu_chadol.jpg' 처럼 경로를 넣으면 플레이스홀더 대신 사진이 노출됩니다.
========================================================= */
window.TPW_DATA = {
	// 브랜드 공통 S
	brand: {
		instagram: '',
		youtube: ''
	},
	// 브랜드 공통 E

	// 매장 목록 S (네이버 지도 기준, 2026.10 확인)
	stores: [
		{
			name: '떡판왕',
			badge: 'SEOLLEUNG',
			station: '선릉역',
			line: '2',
			address: '서울 강남구 테헤란로 423 지하1층',
			jibun: '삼성동 143-37',
			hours: '라스트오더 20:50',
			tel: '02-564-2120'
		},
		{
			name: '떡판왕 을지로3가점',
			badge: 'EULJIRO 3-GA',
			station: '을지로3가역',
			line: '2·3',
			address: '서울 중구 수표로10길 19 2층',
			jibun: '초동 156-9',
			hours: '22:00 영업 종료',
			tel: '0507-1381-8824'
		}
	],
	// 매장 목록 E

	// 시그니처 메뉴 S
	menu: [
		{label: 'SIGNATURE', name: '차돌박이 즉석떡볶이', en: 'CHADOL TTEOKBOKKI', desc: '얇게 썬 차돌이 양념에 녹아드는, 떡판왕의 대표 한 판.', price: null, image: ''},
		{label: 'BEST', name: '시그니처 즉석떡볶이', en: 'SIGNATURE TTEOKBOKKI', desc: '처음 온 날엔 이것부터. 떡판왕 양념 그대로의 맛.', price: null, image: ''},
		{label: 'TOPPING', name: '사리 & 토핑', en: 'SARI & TOPPINGS', desc: '라면, 쫄면, 치즈, 계란… 내 마음대로 판을 키운다.', price: null, image: ''},
		{label: 'FINISH', name: '볶음밥', en: 'FRIED RICE', desc: '남은 양념에 밥을 볶아야 비로소 한 판이 끝난다.', price: null, image: ''}
	],
	// 시그니처 메뉴 E

	// 갤러리 S (type: t1 큰 정사각 / t2 가로형 / t3 세로형 / 기본 작은 정사각)
	gallery: [
		{type: 't1', caption: '떡볶이 클로즈업', en: 'CLOSE UP', image: ''},
		{type: 't3', caption: '차돌박이와 토핑', en: 'CHADOL & TOPPING', image: ''},
		{type: '', caption: '끓는 냄비', en: 'BOILING', image: ''},
		{type: '', caption: '음식 테이블', en: 'ON THE TABLE', image: ''},
		{type: 't2', caption: '매장 내부', en: 'INSIDE', image: ''},
		{type: 't2', caption: '마무리 볶음밥', en: 'FINISH', image: ''}
	]
	// 갤러리 E
};
