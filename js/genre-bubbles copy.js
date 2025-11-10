// genre-bubbles.js
(function () {
  const { Engine, Render, Runner, World, Bodies, Events } = Matter;

  function initGenreBubbleApp(containerId) {
    const container = document.getElementById(containerId);
    if (!container) {
      console.error(`❌ Container with ID '${containerId}' not found.`);
      return null;
    }

    const width = container.clientWidth;
    const height = container.clientHeight;

    const engine = Engine.create();
    const world = engine.world;

    const render = Render.create({
      element: container,
      engine: engine,
      options: {
        width,
        height,
        wireframes: false,
        background: '#222',
        pixelRatio: window.devicePixelRatio || 1,
      }
    });

    Render.run(render);
    Runner.run(Runner.create(), engine);

    const ground = Bodies.rectangle(width / 2, height + 50, width, 100, { isStatic: true });
    const leftWall = Bodies.rectangle(-50, height / 2, 100, height, { isStatic: true });
    const rightWall = Bodies.rectangle(width + 50, height / 2, 100, height, { isStatic: true });
    World.add(world, [ground, leftWall, rightWall]);

    const bubbles = [];
    function createGenreBubble(name, color, radius) {
      let x;
      let tries = 0;
      const maxTries = 100;
    
      do {
        x = Math.random() * (width - 2 * radius) + radius;
        tries++;
        // 기존 버블과 충돌 확인
        const collides = bubbles.some(b => Math.abs(b.position.x - x) < b.circleRadius + radius + 5);
        if (!collides) break; // 충돌 없으면 사용
      } while (tries < maxTries);
    
      const bubble = Bodies.circle(x, -radius, radius, {
        restitution: 0.6,
        friction: 0.1,
        render: {
          fillStyle: color,
          strokeStyle: '#fff',
          lineWidth: 0,
        }
      });
    
      const nameMap = {
        "다큐멘터리": "다큐",
        "애니메이션": "애니"
      };
      
      const displayName = nameMap[name] || name;
      bubble.label = displayName;
    
      World.add(world, bubble);
      bubbles.push(bubble);
    }
    

    // 텍스트 렌더링
    Events.on(render, 'afterRender', () => {
      const ctx = render.context;
      ctx.fillStyle = 'black';
      ctx.textAlign = 'center';
      ctx.textBaseline = 'middle';
    
      bubbles.forEach(body => {
        const radius = body.circleRadius;
        const name = body.label;
    
        // 1️⃣ 글자 크기 자동 조정 (최대 16px, 최소 8px)
        let fontSize = Math.min(radius / 1.2, 16);
        ctx.font = `${fontSize}px Arial`;
    
        // 2️⃣ 글자 폭이 버블보다 크면 줄이기
        let textWidth = ctx.measureText(name).width;
        while (textWidth > radius * 1.8 && fontSize > 8) {
          fontSize -= 1;
          ctx.font = `${fontSize}px Arial`;
          textWidth = ctx.measureText(name).width;
        }
    
        // 3️⃣ 텍스트 여러 줄 처리
        const words = name.split(''); // 글자 단위
        let lines = [];
        let line = '';
        for (let i = 0; i < words.length; i++) {
          const testLine = line + words[i];
          if (ctx.measureText(testLine).width > radius * 1.6 && line !== '') {
            lines.push(line);
            line = words[i];
          } else {
            line = testLine;
          }
        }
        lines.push(line);
    
        // 최대 2줄만
        if (lines.length > 2) lines = [lines[0], lines.slice(1).join('')];
    
        // 4️⃣ 화면에 출력
        const lineHeight = fontSize * 1.1;
        lines.forEach((l, idx) => {
          const yOffset = (lines.length === 2) ? (idx === 0 ? -lineHeight/2 : lineHeight/2) : 0;
          ctx.fillText(l, body.position.x, body.position.y + yOffset);
        });
      });
    });
    

    return {
      createGenreBubble
    };
  }

  // 전역 객체 등록
  window.genreBubbleApp = {
    init: initGenreBubbleApp
  };
})();
