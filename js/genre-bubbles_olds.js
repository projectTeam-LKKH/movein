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
      if (bubbles.length >= 5) return;

      const x = Math.random() * (width - 2 * radius) + radius;

      const bubble = Bodies.circle(x, -radius, radius, {
        restitution: 0.6,
        friction: 0.1,
        render: {
          fillStyle: color,
          strokeStyle: '#fff',
          lineWidth: 0,
        }
      });

      bubble.label = name;
      World.add(world, bubble);
      bubbles.push(bubble);
    }

    // 텍스트 렌더링
    Events.on(render, 'afterRender', () => {
      const ctx = render.context;
      ctx.font = '16px Arial';
      ctx.fillStyle = 'black';
      ctx.textAlign = 'center';

      bubbles.forEach(body => {
        ctx.fillText(body.label, body.position.x, body.position.y + 5);
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
