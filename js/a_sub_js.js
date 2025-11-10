// 영화 밸런스 질문
const balanceQuestions = [
    { left: "얼1음 성에서 열리는 마법 같은 파티", right: "산1타와 함께 전 세계 선물 배달" },
    { left: "슈퍼히어로가 세상을 구하는 액션", right: "연인의 사랑을 그린 감동 로맨스" },
    { left: "외계 생명체와 싸우는 공상과학 영화", right: "웃음 가득한 코미디 영화" },
    { left: "한국에서 제작된 감동 드라마", right: "할리우드 블록버스터 영화" },
    { left: "집에서 편하게 즐기는 영화", right: "영화관에서 스릴 넘치는 영화 관람" }
];

// DOM
const leftCard = document.querySelector(".a_balance_card.left");
const rightCard = document.querySelector(".a_balance_card.right");
const leftText = document.querySelector(".a_balance_text_value_left");
const rightText = document.querySelector(".a_balance_text_value_right");

// 사용한 질문 기록
let usedIndexes = [];

// 질문 로드
function loadQuestion() {
    if (usedIndexes.length >= balanceQuestions.length) {
        leftText.textContent = "오늘의 게임을 모두 완료했습니다!";
        rightText.textContent = "";
        leftCard.style.pointerEvents = "none";
        rightCard.style.pointerEvents = "none";
        return;
    }

    let index;
    do {
        index = Math.floor(Math.random() * balanceQuestions.length);
    } while (usedIndexes.includes(index));
    usedIndexes.push(index);

    const question = balanceQuestions[index];
    if (Math.random() > 0.5) {
        leftText.textContent = question.left;
        rightText.textContent = question.right;
    } else {
        leftText.textContent = question.right;
        rightText.textContent = question.left;
    }

    leftCard.style.pointerEvents = "auto";
    rightCard.style.pointerEvents = "auto";
}

// 팝업 표시
function showPopup(optionText) {
    const existingPopup = document.querySelector(".a_balance_popup");
    if (existingPopup) existingPopup.remove();

    const popup = document.createElement("div");
    popup.className = "a_balance_popup";
    popup.innerHTML = `
        <p>당신의 취향을 보니 "<strong>${optionText}</strong>"를 좋아하시네요!</p>
        <button class="close_popup">닫기</button>
    `;
    document.body.appendChild(popup);

    popup.querySelector(".close_popup").addEventListener("click", () => {
        popup.remove();
        loadQuestion();
    });
}

// 카드 클릭 이벤트
leftCard.addEventListener("click", () => {
    showPopup(leftText.textContent);
    leftCard.style.pointerEvents = "none";
    rightCard.style.pointerEvents = "none";
});

rightCard.addEventListener("click", () => {
    showPopup(rightText.textContent);
    leftCard.style.pointerEvents = "none";
    rightCard.style.pointerEvents = "none";
});

console.log(1);
// 초기 질문 로드
loadQuestion();

console.log(2);