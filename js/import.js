// 헤더 연결 및 기능 구현
$(function () {
  // 헤더 임포트 주소 연결
  /* 추후 서브 연결하실 때
  $("#header-slot").load("import/header.html", () => {
  $("#subheader-slot").load("import/subheader.html", () => {
    (이하 내용 똑같이)
    console.log("header + subheader 로드 완료");
  });
}); 이런 식으로 고쳐주시면 됩니다
  */
  $("#header-slot").load("import/header.html", () => {
    const r = document.querySelector("#header-slot");
    const btn = r.querySelector(".hambtn");
    const nav = r.querySelector(".hamburger");
    const modal = r.querySelector(".modal-bg");
    const logo = r.querySelector("header a img");

    // 초기화
    r.querySelectorAll(".ham-sub").forEach((el) =>
      el.classList.remove("active")
    );

    // 메뉴 열기/닫기
    btn.addEventListener("click", () => {
      const open = nav.classList.toggle("show");
      modal.classList.toggle("active", open);
      document.body.classList.toggle("lock", open);
      logo.classList.toggle("hide", open);
    });

    // 배경 클릭 시 닫기
    modal.addEventListener("click", () => {
      nav.classList.remove("show");
      modal.classList.remove("active");
      document.body.classList.remove("lock");
      logo.classList.remove("hide");
    });

    // 단일 선택
    r.addEventListener("click", (e) => {
      const sub = e.target.closest(".ham-sub");
      if (!sub) return;
      r.querySelectorAll(".ham-sub.active").forEach((el) =>
        el.classList.remove("active")
      );
      sub.classList.add("active");
    });
  });
});

//푸터 루트 연결
$(function () {
  $("#bottom-nav").load("import/bottom-nav.html", () => {
    const botNavs = document.querySelectorAll(".bot-nav-icon");
    const botImg = document.querySelectorAll(".bot-nav-icon > img");

    botNavs.forEach((nav) => {
      nav.addEventListener("click", () => {
        // 1. 전체 active 해제
        botNavs.forEach((n) => n.classList.remove("active"));
        // 2. 클릭된 요소만 활성화
        nav.classList.add("active");
      });
    });
  });
});

console.log("페이지가 로드되었습니다.");
