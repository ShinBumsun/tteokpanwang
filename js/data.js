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
		},
		{
			name: '떡판왕 구의역점',
			badge: 'GUUI',
			station: '구의역',
			line: '2',
			address: '서울 광진구 아차산로51길 11 2층',
			jibun: '구의동 246-59',
			hours: '22:30 영업 종료',
			tel: '0507-1341-4735'
		}
	],
	// 매장 목록 E

	// 시그니처 메뉴 S (price: 숫자, priceNote: 가격 앞 설명, from: true 면 '~' 표시)
	menu: [
		{label: 'SIGNATURE', name: '차돌 즉석떡볶이', en: 'CHADOL TTEOKBOKKI', desc: '차돌이 양념에 녹아드는, 떡판왕의 대표 한 판. 2인부터 4인 세트까지.', priceNote: '2인 세트', price: 20000, from: true, image: ''},
		{label: 'BEST', name: '기본 즉석떡볶이', en: 'CLASSIC TTEOKBOKKI', desc: '처음 온 날엔 이것부터. 떡판왕 양념 그대로의 맛.', priceNote: '2인 세트', price: 18000, from: true, image: ''},
		{label: 'TOPPING', name: '사리 & 토핑', en: 'SARI & TOPPINGS', desc: '양념만두, 피자치즈, 라면, 쫄면, 김말이튀김… 내 마음대로 판을 키운다.', priceNote: '', price: 700, from: true, image: ''},
		{label: 'FINISH', name: '셀프볶음밥', en: 'FRIED RICE', desc: '남은 양념에 직접 볶아야 비로소 한 판이 끝난다. (200g)', priceNote: '', price: 3000, from: false, image: ''}
	],
	// 시그니처 메뉴 E

	// 전체 메뉴판 S (메뉴판 원본 기준 / sub: 이름 옆 작은 글씨)
	// ※ 메뉴판 원본에 '3인 세트'가 두 번 표기되어 있어, 금액 순서상 두 번째를 '4인 세트'로 반영 — 매장 확인 필요
	menuBoard: [
		{title: '즉석떡볶이', en: 'TTEOKBOKKI', items: [
			{name: '기본 2인 세트', price: 18000},
			{name: '기본 3인 세트', price: 26000},
			{name: '기본 4인 세트', price: 34000},
			{name: '차돌 2인 세트', price: 20000, best: true},
			{name: '차돌 3인 세트', price: 28000},
			{name: '차돌 4인 세트', price: 36000},
			{name: '+ 고기 추가', sub: '120g', price: 4000}
		]},
		{title: '사리 및 토핑', en: 'SARI & TOPPING', items: [
			{name: '양념만두', sub: '4EA', price: 3500},
			{name: '피자치즈', price: 3000},
			{name: '떡', price: 2500},
			{name: '어묵', price: 2000},
			{name: '라면', price: 2000},
			{name: '쫄면', price: 2000},
			{name: '계란', sub: '1EA', price: 700},
			{name: '야끼만두', sub: '1EA', price: 700},
			{name: '김말이튀김', sub: '1EA', price: 700}
		]},
		{title: '볶음밥', en: 'FINISH', items: [
			{name: '셀프볶음밥', sub: '200g', price: 3000},
			{name: '셀프주먹밥', sub: '200g', price: 3500}
		]},
		{title: '안주류', en: 'DINNER ONLY', note: '저녁', items: [
			{name: '후라이드 치킨', price: 20000},
			{name: '불닭발', price: 19000},
			{name: '주전자 꼬치어묵', sub: '6EA', price: 12000},
			{name: '감자튀김', price: 15000},
			{name: '쥐포와 땅콩', price: 15000},
			{name: '황태구이', price: 15000}
		]},
		{title: '주류 & 음료', en: 'DRINKS', items: [
			{name: '소주', price: 5000},
			{name: '병맥주', price: 5000},
			{name: '생맥주', price: 5000},
			{name: '무알콜맥주', sub: '병', price: 5000},
			{name: '음료', price: 2000},
			{name: '쿨피스', price: 2000}
		]}
	],
	// 전체 메뉴판 E

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
