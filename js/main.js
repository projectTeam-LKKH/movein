// 햄버거바 터치 이벤트
document.addEventListener("DOMContentLoaded", () => {
  const hamBtn = document.querySelector(".hambtn");
  const hamMenu = document.querySelector("nav.hamburger");
  const locked = document.body;
  const logoImg = document.querySelector("#header a img");
  const modal = document.querySelector(".modal-bg");

  hamBtn.addEventListener("click", () => {
    hamMenu.classList.toggle("show");

    if (hamMenu.classList.contains("show")) {
      //만약 햄메뉴가 보이면
      modal.classList.add("active");
      locked.classList.add("lock");
      logoImg.classList.add("hide");
    } else {
      //햄메뉴가 안보이면
      modal.classList.remove("active");
      locked.classList.remove("lock");
      logoImg.classList.remove("hide");
    }
  });
});

//헤더 내부 버튼 클릭 이벤트
document.addEventListener("DOMContentLoaded", () => {
  const subMenus = document.querySelectorAll(".ham-sub-box li");

  subMenus.forEach((item) => item.classList.remove("active"));

  subMenus.forEach((sub) => {
    sub.addEventListener("click", () => {
      // 이미 활성화된 걸 다시 클릭한 경우, 해제
      if (sub.classList.contains("active")) {
        sub.classList.remove("active");
      } else {
        // 다른 모든 항목 비활성화 후 현재만 활성화
        subMenus.forEach((item) => item.classList.remove("active"));
        sub.classList.add("active");
      }
    });
  });
});

// 하트 버튼 토글
document.addEventListener("DOMContentLoaded", () => {
  const greenBtn = "img/heart_49e99c.png";
  const grayBtn = "img/heart_6f6c76.png";

  document.querySelectorAll(".likeBtn").forEach((btn) => {
    btn.addEventListener("click", () => {
      const img = btn.querySelector("img");
      const current = img.getAttribute("src");
      img.setAttribute("src", current === grayBtn ? greenBtn : grayBtn);
    });
  });
});

// 요즘 대세(영화/드라마) 버튼 토글 — 섹션별 독립 동작
document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".hot-container").forEach((section) => {
    const nav = section.querySelector(".hot-nav-box");
    if (!nav) return;

    const buttons = () => nav.querySelectorAll(".all-btn");

    // 초기 활성화: 해당 섹션에 active가 하나도 없으면 첫 번째에 부여
    if (!nav.querySelector(".all-btn.active") && buttons().length) {
      buttons()[0].classList.add("active");
    }

    // 이벤트 위임: 섹션 내부 버튼끼리만 토글
    nav.addEventListener("click", (e) => {
      const btn = e.target.closest(".all-btn");
      if (!btn || !nav.contains(btn)) return;

      buttons().forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");
    });
  });
});

//
const reviewBtn = document.querySelector(".review-input");
const reviewForm = document.querySelector(".review-form");
const prevBtn = document.querySelector(".prev-btn");

reviewForm.classList.remove("open");

reviewBtn.addEventListener("click", (e) => {
  e.preventDefault();
  reviewForm.classList.add("open");

  prevBtn.addEventListener("click", () => {
    reviewForm.classList.remove("open");
  });
});

console.log("페이지가 로드되었습니다.");
