import TubesCursor from "https://cdn.jsdelivr.net/npm/threejs-components@0.0.19/build/cursors/tubes1.min.js";

const app = TubesCursor(document.getElementById("canvas"), {
  tubes: {
    radius: 3.0,          // Much larger tubes to fill more space
    tubularSegments: 400, // Even smoother for larger scale
    radialSegments: 24,
    amplitude: 4.0,       // Larger amplitude for bigger movements
    speed: 1.0,           // Dynamic speed
    colors: ["#f967fb", "#53bc28", "#6958d5"],
    lights: {
      intensity: 600,
      colors: ["#83f36e", "#fe8a2e", "#ff008a", "#60aed5"]
    }
  },
  camera: {
    fov: 120,             // Very wide field of view
    distance: 1            // Much closer camera for full window coverage
  }
});

// Different animation styles
const animationStyles = [
  { name: "Flowing", speed: 0.1, amplitude: 2.0, radius: 2.5 }, // Slower speed
  { name: "Pulsing", speed: 0.3, amplitude: 5.0, radius: 3.5 }, // Slower speed
  { name: "Chaotic", speed: 0.4, amplitude: 6.0, radius: 4.0 }, // Slower speed
  { name: "Gentle", speed: 0.08, amplitude: 1.5, radius: 2.0 }, // Much slower
  { name: "Wild", speed: 0.5, amplitude: 7.0, radius: 5.0 }, // Slower speed
  { name: "Flower", speed: 0.15, amplitude: 4.0, radius: 3.0 }, // Slower speed
  { name: "Infinity", speed: 0.25, amplitude: 8.0, radius: 2.8 }, // Slower speed
  { name: "S-Wave", speed: 0.2, amplitude: 6.0, radius: 3.2 }, // Slower speed
  { name: "Q-Loop", speed: 0.18, amplitude: 5.5, radius: 3.5 } // Slower speed
];

let currentStyleIndex = 0;

// Click to randomize colors and lights
document.body.addEventListener("click", () => {
  app.tubes.setColors(randomColors(3));
  app.tubes.setLightsColors(randomColors(4));
});

// Cycle through different animation styles every 8 seconds
setInterval(() => {
  currentStyleIndex = (currentStyleIndex + 1) % animationStyles.length;
  const style = animationStyles[currentStyleIndex];
  
  app.tubes.setSpeed(style.speed);
  app.tubes.setAmplitude(style.amplitude);
  app.tubes.setRadius(style.radius);
  
  // Change colors every loop cycle
  app.tubes.setColors(randomColors(3));
  app.tubes.setLightsColors(randomColors(4));
  
  console.log(`Switched to ${style.name} style with new colors`);
}, 8000);

// Colors now change automatically every 8 seconds with style cycles
// Removed additional random variations

function randomColors(count) {
  return Array.from({ length: count }, () =>
    "#" + Math.floor(Math.random() * 16777215)
      .toString(16)
      .padStart(6, "0")
  );
}
// Auto resize on window resize
window.addEventListener("resize", () => app.resize());