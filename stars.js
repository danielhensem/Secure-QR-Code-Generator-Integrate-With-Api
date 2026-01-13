document.addEventListener("DOMContentLoaded", () => {
  const layers = ["stars", "stars2", "stars3"];

  layers.forEach(id => {
    const el = document.getElementById(id);
    const computedStyle = getComputedStyle(el);
    const originalShadows = computedStyle.boxShadow.split(",");

    setInterval(() => {
      const newShadows = originalShadows.map(sh => {
        const parts = sh.trim().split(" ");
        // replace color with new random opacity
        const x = parts[0];
        const y = parts[1];
        const blur = parts[2] || "0px";
        const opacity = Math.random() * 0.8 + 0.2;
        return `${x} ${y} ${blur} rgba(255,255,255,${opacity})`;
      });
      el.style.boxShadow = newShadows.join(",");
    }, 1000);
  });
});
