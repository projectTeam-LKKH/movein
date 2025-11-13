//<!-- 이 파일은 테스트용 파일입니다. 절대 다른 파일에 덮어쓰지 마세요.

const BUBBLE_PADDING = 20; // 버블 간격(px)
const OUTLINE_COLOR = "#252426"; // 테두리 색상
const OUTLINE_WIDTH = 4; // 테두리 두께(px)

// === 라벨 폰트 설정(고정 크기) ===
const LABEL_FONT_FAMILY = `"NanumSquare", sans-serif`; // 폰트
const LABEL_FONT_REM = 0.9375; // = 15px @ root 16px (고정)
const LABEL_FONT_WEIGHT_DEFAULT = 400; // 기본 굵기(보통)
const LABEL_FONT_COLOR_DEFAULT = "#faf5f5"; // 기본 글자색

(function () {
  const { Engine, Render, Runner, World, Bodies, Events } = Matter;

  function initGenreBubbleApp(containerId) {
    const container = document.getElementById(containerId);
    if (!container) {
      console.error(`❌ '${containerId}' 컨테이너를 찾을 수 없습니다.`);
      return null;
    }

    const width = container.clientWidth;
    const height = container.clientHeight; // CSS로 고정 높이 필수

    const engine = Engine.create();
    const world = engine.world;

    const render = Render.create({
      element: container,
      engine,
      options: {
        width,
        height,
        wireframes: false,
        pixelRatio: window.devicePixelRatio || 1,
        background: "transparent",
      },
    });

    Render.run(render);
    Runner.run(Runner.create(), engine);

    // 경계 생성
    const ground = Bodies.rectangle(width / 2, height + 50, width, 100, {
      isStatic: true,
    });
    const left = Bodies.rectangle(-50, height / 2, 100, height, {
      isStatic: true,
    });
    const right = Bodies.rectangle(width + 50, height / 2, 100, height, {
      isStatic: true,
    });
    World.add(world, [ground, left, right]);

    const bubbles = [];

    /**
     * [데이터 주입 지점 ①]
     * createGenreBubble()는 나중에 서버/DB/API에서 불러온
     * 장르별 데이터(이름, 색상, 크기, 비율, 선호도 등)를
     * 기반으로 호출될 예정.
     */
    function createGenreBubble(name, color, radius, opts = {}) {
      const lw = Number.isFinite(opts.lineWidth)
        ? opts.lineWidth
        : OUTLINE_WIDTH;
      const strokeColor = opts.strokeColor || OUTLINE_COLOR;

      // 배치(충돌 최소화)
      let x;
      let tries = 0;
      const maxTries = 80;
      while (tries < maxTries) {
        x = Math.random() * (width - 2 * radius) + radius;
        const spawnY = -radius;
        const ok = bubbles.every((b) => {
          const dx = b.position.x - x;
          const dy = b.position.y - spawnY;
          return Math.hypot(dx, dy) >= b.circleRadius + radius + BUBBLE_PADDING;
        });
        if (ok) break;
        tries++;
      }
      if (x === undefined) x = Math.random() * (width - 2 * radius) + radius;

      const body = Bodies.circle(x, -radius, radius, {
        restitution: 0.6,
        friction: 0.1,
        render: { visible: false },
      });

      // [데이터 주입 지점 ②]
      // labelMap은 "장르명"을 보여주기 위한 표기용 매핑.
      // 나중에 다국어 데이터나 API 반환값(key:value)을 매핑할 때 수정 가능.
      const labelMap = {
        애니: "애니",
        드라마: "드라마",
        액션: "액션",
        SF: "SF",
        코미디: "코미디",
        판타지: "판타지",
        스릴러: "스릴러",
        로맨스: "로맨스",
        // 예: "다큐멘터리": "다큐", "범죄": "CRIME"
      };

      body.plugin = {
        label: labelMap[name] || name, // ← [장르명]
        fill: color, // ← [버블 배경색: 장르별 색상 데이터]
        stroke: strokeColor, // ← [테두리 색상: 필요 시 장르별 지정 가능]
        lineWidth: lw,
        gradient: opts.gradient || null,
        fontWeight: opts.fontWeight || LABEL_FONT_WEIGHT_DEFAULT, // ← [강조 데이터용 굵기]
        fontColor: opts.fontColor || LABEL_FONT_COLOR_DEFAULT, // ← [글자색(테마별 변경 가능)]
        overlayColor: opts.overlayColor || null,
      };

      World.add(world, body);
      bubbles.push(body);
    }

    // 커스텀 캔버스 렌더링
    Events.on(render, "afterRender", () => {
      const ctx = render.context;
      ctx.textAlign = "center";
      ctx.textBaseline = "middle";

      // rem → px 환산(루트 폰트 크기 기준)
      const rootPx =
        parseFloat(getComputedStyle(document.documentElement).fontSize) || 16;
      const fixedPx = Math.round(LABEL_FONT_REM * rootPx);

      bubbles.forEach((b) => {
        const rOuter = b.circleRadius;
        const lw = b.plugin.lineWidth || OUTLINE_WIDTH;
        const rDraw = Math.max(0, rOuter - lw / 2);

        // 채우기
        if (b.plugin.gradient?.inner && b.plugin.gradient?.outer) {
          const grd = ctx.createRadialGradient(
            b.position.x,
            b.position.y,
            0,
            b.position.x,
            b.position.y,
            rDraw
          );
          grd.addColorStop(0, b.plugin.gradient.inner);
          grd.addColorStop(1, b.plugin.gradient.outer);
          ctx.fillStyle = grd;
        } else {
          ctx.fillStyle = b.plugin.fill;
        }

        ctx.beginPath();
        ctx.arc(b.position.x, b.position.y, rDraw, 0, Math.PI * 2);
        ctx.fill();

        // 테두리
        ctx.lineWidth = lw;
        ctx.strokeStyle = b.plugin.stroke || OUTLINE_COLOR;
        ctx.beginPath();
        ctx.arc(b.position.x, b.position.y, rDraw, 0, Math.PI * 2);
        ctx.stroke();

        // [데이터 주입 지점 ③]
        // 여기서 라벨 대신 다른 정보를 함께 표시할 수도 있음.
        // 예: 장르명 + (%) 비율 or 점수
        const name = b.plugin.label;
        const weight = b.plugin.fontWeight || LABEL_FONT_WEIGHT_DEFAULT;
        ctx.font = `${weight} ${fixedPx}px ${LABEL_FONT_FAMILY}`;
        ctx.fillStyle = b.plugin.fontColor || LABEL_FONT_COLOR_DEFAULT;
        ctx.fillText(name, b.position.x, b.position.y);

        // ⭐ 1순위 등 overlayColor가 지정된 버블만 반투명 오버레이 추가
        if (b.plugin.overlayColor) {
          ctx.save();
          ctx.beginPath();
          ctx.arc(b.position.x, b.position.y, rDraw, 0, Math.PI * 2);
          ctx.clip();

          ctx.fillStyle = b.plugin.overlayColor; // 예: rgba(73, 233, 156, 0.5)
          ctx.fillRect(
            b.position.x - rDraw,
            b.position.y - rDraw,
            rDraw * 2,
            rDraw * 2
          );

          ctx.restore();
        }
      });
    });

    return { createGenreBubble };
  }

  window.genreBubbleApp = { init: initGenreBubbleApp };
})();
