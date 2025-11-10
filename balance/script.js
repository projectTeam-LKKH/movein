const questions = [
    { q: "액션 영화 vs 로맨스 영화 중 어떤 장르를 주로 선호하시나요?", options: ["액션 영화", "로맨스 영화"] },
    { q: "마블 영화와 DC 영화 중 어떤 세계관이 더 끌리나요?", options: ["마블 영화", "DC 영화"] },
    { q: "공포 영화 vs 코미디 영화, 영화를 볼 때 주로 어떤 기분을 원하시나요?", options: ["공포 영화", "코미디 영화"] },
    { q: "한국 영화와 해외 영화 중 어떤 영화를 더 즐겨보시나요?", options: ["한국 영화", "해외 영화"] },
    { q: "영화를 관객과 함께 영화관에서 보는 편인가요, 아니면 집에서 편하게 보는 편인가요?", options: ["영화관에서 보기", "집에서 보기"] }
];

let usedQuestions = [];
let currentQuestion;

const questionEl = document.getElementById('question');
const option1Btn = document.getElementById('option1');
const option2Btn = document.getElementById('option2');
const popup = document.getElementById('popup');
const popupText = document.getElementById('popup-text');
const closePopupBtn = document.getElementById('close-popup');
const restartBtn = document.getElementById('restart');

function getRandomQuestion() {
    if(usedQuestions.length >= questions.length) return null;
    let index;
    do {
        index = Math.floor(Math.random() * questions.length);
    } while(usedQuestions.includes(index));
    usedQuestions.push(index);
    return questions[index];
}

function loadQuestion() {
    currentQuestion = getRandomQuestion();
    if(currentQuestion) {
        questionEl.textContent = currentQuestion.q;
        // 랜덤으로 옵션 위치 섞기
        if(Math.random() > 0.5){
            option1Btn.textContent = currentQuestion.options[0];
            option2Btn.textContent = currentQuestion.options[1];
        } else {
            option1Btn.textContent = currentQuestion.options[1];
            option2Btn.textContent = currentQuestion.options[0];
        }
        option1Btn.disabled = false;
        option2Btn.disabled = false;
    } else {
        questionEl.textContent = "모든 질문을 완료했습니다!";
        option1Btn.classList.add('hidden');
        option2Btn.classList.add('hidden');
        restartBtn.classList.remove('hidden');
    }
}

function showPopup(option) {
    popupText.textContent = `당신의 취향을 보니 "${option}"를 좋아하시네요!`;
    popup.classList.remove('hidden');
}

option1Btn.addEventListener('click', () => {
    showPopup(option1Btn.textContent);
    option1Btn.disabled = true;
    option2Btn.disabled = true;
});
option2Btn.addEventListener('click', () => {
    showPopup(option2Btn.textContent);
    option1Btn.disabled = true;
    option2Btn.disabled = true;
});

closePopupBtn.addEventListener('click', () => {
    popup.classList.add('hidden');
    loadQuestion();
});

restartBtn.addEventListener('click', () => {
    usedQuestions = [];
    currentQuestion = null;
    option1Btn.classList.remove('hidden');
    option2Btn.classList.remove('hidden');
    restartBtn.classList.add('hidden');
    loadQuestion();
});

loadQuestion();
