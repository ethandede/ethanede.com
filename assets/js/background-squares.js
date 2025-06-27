document.addEventListener("DOMContentLoaded", function() {
  console.log("backgroundSquares.js loaded");

  // Helper function to convert hex to RGB string
  function hexToRgbString(hex) {
    const rgb = hexToRgb(hex);
    return `${rgb.r}, ${rgb.g}, ${rgb.b}`;
  }

  // Helper function to convert hex to RGB string
  function hexToRgbString(hex) {
    const rgb = hexToRgb(hex);
    return `${rgb.r}, ${rgb.g}, ${rgb.b}`;
  }

  // Helper function to lighten a color
  function lightenColor(hex, percent) {
    // Convert hex to RGB
    const rgb = hexToRgb(hex);
    
    // Convert to HSL to adjust lightness
    const hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);
    
    // Adjust lightness
    hsl[2] = Math.min(100, hsl[2] + percent);
    
    // Convert back to RGB
    const lightRgb = hslToRgb(hsl[0], hsl[1], hsl[2]);
    
    // Convert to hex
    return rgbToHex(lightRgb.r, lightRgb.g, lightRgb.b);
  }

  // Helper function to convert RGB to HSL
  function rgbToHsl(r, g, b) {
    r /= 255;
    g /= 255;
    b /= 255;
    const max = Math.max(r, g, b);
    const min = Math.min(r, g, b);
    let h, s, l = (max + min) / 2;

    if (max === min) {
      h = s = 0;
    } else {
      const d = max - min;
      s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
      switch (max) {
        case r: h = (g - b) / d + (g < b ? 6 : 0); break;
        case g: h = (b - r) / d + 2; break;
        case b: h = (r - g) / d + 4; break;
      }
      h /= 6;
    }

    return [h * 360, s * 100, l * 100];
  }

  // Helper function to convert HSL to RGB
  function hslToRgb(h, s, l) {
    h /= 360;
    s /= 100;
    l /= 100;
    let r, g, b;

    if (s === 0) {
      r = g = b = l;
    } else {
      const hue2rgb = (p, q, t) => {
        if (t < 0) t += 1;
        if (t > 1) t -= 1;
        if (t < 1/6) return p + (q - p) * 6 * t;
        if (t < 1/2) return q;
        if (t < 2/3) return p + (q - p) * (2/3 - t) * 6;
        return p;
      };

      const q = l < 0.5 ? l * (1 + s) : l + s - l * s;
      const p = 2 * l - q;
      r = hue2rgb(p, q, h + 1/3);
      g = hue2rgb(p, q, h);
      b = hue2rgb(p, q, h - 1/3);
    }

    return {
      r: Math.round(r * 255),
      g: Math.round(g * 255),
      b: Math.round(b * 255)
    };
  }

  // Helper function to convert RGB to Hex
  function rgbToHex(r, g, b) {
    const toHex = (c) => {
      const hex = c.toString(16);
      return hex.length === 1 ? '0' + hex : hex;
    };
    return `#${toHex(r)}${toHex(g)}${toHex(b)}`;
  }

  // Helper function to convert hex to RGB
  function hexToRgb(hex) {
    hex = hex.replace(/^#/, '');
    if (hex.length === 3) {
      hex = hex.split('').map(function (h) {
        return h + h;
      }).join('');
    }
    const bigint = parseInt(hex, 16);
    return {
      r: (bigint >> 16) & 255,
      g: (bigint >> 8) & 255,
      b: bigint & 255
    };
  }

  // Define default colors (from your Sass variables)
  const defaultPrimaryColor = '#45748C';
  const defaultSecondaryColor = '#BF3978';
  const defaultPrimaryColorLight = lightenColor(defaultPrimaryColor, 10); // Lightened by 10%

  // Get references to the inputs and button
  const primaryInput = document.getElementById('primaryColor');
  const secondaryInput = document.getElementById('secondaryColor');
  const resetButton = document.getElementById('resetColors');

  if (primaryInput) {
    primaryInput.addEventListener('input', function() {
      const newPrimary = this.value;
      // Update hex value
      document.documentElement.style.setProperty('--primary-color', newPrimary);
      // Update rgb value based on the new hex
      const newRgb = hexToRgbString(newPrimary);
      document.documentElement.style.setProperty('--primary-color-rgb', newRgb);
      // Also update squares or any other elements as needed
      updateSquaresColor();
    });
  } else {
    console.log("Primary input not found.");
  }

  if (secondaryInput) {
    secondaryInput.addEventListener('input', function() {
      const newSecondary = this.value;
      document.documentElement.style.setProperty('--secondary-color', newSecondary);
      const newSecondaryRgb = hexToRgbString(newSecondary);
      document.documentElement.style.setProperty('--secondary-color-rgb', newSecondaryRgb);
      // Update any elements that use the secondary color if needed.
    });
  }
  
  if (resetButton) {
    resetButton.addEventListener('click', function() {
      console.log("Resetting colors to default.");
      document.documentElement.style.setProperty('--primary-color', defaultPrimaryColor);
      document.documentElement.style.setProperty('--secondary-color', defaultSecondaryColor);
      document.documentElement.style.setProperty('--primary-color-light', defaultPrimaryColorLight);
      if (primaryInput) {
        primaryInput.value = defaultPrimaryColor;
      }
      if (secondaryInput) {
        secondaryInput.value = defaultSecondaryColor;
      }
      updateSquaresColor();
    });
  }

  // Retrieve the current primary color from the CSS variable and convert it to RGB
  const primaryHex = getComputedStyle(document.documentElement)
                      .getPropertyValue('--primary-color').trim();
  const primaryRGB = hexToRgb(primaryHex);

  // Select the SVG element that contains the animated squares
  const svg = document.querySelector('.animated-squares');
  
  // Only proceed if SVG element exists
  if (!svg) {
    console.log('Background animation SVG not found - skipping animation');
    return;
  }
  
  const numSquares = 15;
  const svgWidth = 1920;
  const svgHeight = 1080;
  let allowedPercentage = window.innerHeight > window.innerWidth ? 0.9 : 0.75;
  const allowedHeight = svgHeight * allowedPercentage;

  // Loop to create and animate each square
  for (let i = 0; i < numSquares; i++) {
    const rect = document.createElementNS("http://www.w3.org/2000/svg", "rect");

    const size = (Math.floor(Math.random() * 180) + 90) * 2;
    const posX = Math.random() * (svgWidth - size);
    const maxPosY = allowedHeight - size;
    const posY = maxPosY > 0 ? Math.random() * maxPosY : 0;
    
    rect.setAttribute("x", posX);
    rect.setAttribute("y", posY);
    rect.setAttribute("width", size);
    rect.setAttribute("height", size);
    
    // Create a subtle variation around the primary color.
    const variation = 50;
    const r = Math.max(0, Math.min(255, primaryRGB.r + Math.floor((Math.random() - 0.5) * variation)));
    const g = Math.max(0, Math.min(255, primaryRGB.g + Math.floor((Math.random() - 0.5) * variation)));
    const b = Math.max(0, Math.min(255, primaryRGB.b + Math.floor((Math.random() - 0.5) * variation)));
    const alpha = 0.1;
    rect.setAttribute("fill", `rgba(${r}, ${g}, ${b}, ${alpha})`);
    svg.appendChild(rect);
    
    const shiftX = (Math.random() - 0.5) * 3500;
    const shiftY = (Math.random() - 0.5) * 350;
    const duration = Math.random() * 60 + 30;
    
    gsap.to(rect, {
      duration: duration,
      x: shiftX,
      y: shiftY,
      repeat: -1,
      yoyo: true,
      ease: "sine.inOut"
    });
  }

  // Function to update the fill colors of the squares based on the current primary color
  function updateSquaresColor() {
    const primaryHex = getComputedStyle(document.documentElement)
                        .getPropertyValue('--primary-color').trim();
    console.log('New primary color:', primaryHex);
    const primaryRGB = hexToRgb(primaryHex);
    const variation = 50;
  
    document.querySelectorAll('.animated-squares rect').forEach(rect => {
      const r = Math.max(0, Math.min(255, primaryRGB.r + Math.floor((Math.random() - 0.5) * variation)));
      const g = Math.max(0, Math.min(255, primaryRGB.g + Math.floor((Math.random() - 0.5) * variation)));
      const b = Math.max(0, Math.min(255, primaryRGB.b + Math.floor((Math.random() - 0.5) * variation)));
      rect.setAttribute("fill", `rgba(${r}, ${g}, ${b}, 0.1)`);
      console.log('Updated square fill:', rect.getAttribute("fill"));
    });
  }
});
