function lightenColor(hex, percent) {
  // Remove the '#' if present
  hex = hex.replace(/^#/, '');
  // Convert 3-digit hex to 6-digit hex if needed
  if (hex.length === 3) {
    hex = hex.split('').map(function(h) {
      return h + h;
    }).join('');
  }
  const num = parseInt(hex, 16);
  const amt = Math.round(2.55 * percent);
  let R = (num >> 16) + amt;
  let G = (num >> 8 & 0x00FF) + amt;
  let B = (num & 0x0000FF) + amt;
  R = R < 255 ? (R < 0 ? 0 : R) : 255;
  G = G < 255 ? (G < 0 ? 0 : G) : 255;
  B = B < 255 ? (B < 0 ? 0 : B) : 255;
  return "#" + ((1 << 24) + (R << 16) + (G << 8) + B).toString(16).slice(1);
}

function hexToRgbString(hex) {
  // Remove the '#' if present
  hex = hex.replace(/^#/, '');
  // Expand 3-digit hex to 6-digit if necessary
  if (hex.length === 3) {
    hex = hex.split('').map(h => h + h).join('');
  }
  const bigint = parseInt(hex, 16);
  const r = (bigint >> 16) & 255;
  const g = (bigint >> 8) & 255;
  const b = bigint & 255;
  return `${r}, ${g}, ${b}`;
}

// Debug function to check CSS variables
function debugCSSVariables() {
  const root = document.documentElement;
  const computedStyle = getComputedStyle(root);
  
  const variables = [
    '--primary-color',
    '--secondary-color', 
    '--tertiary-color',
    '--quaternary-color',
    '--primary-color-rgb',
    '--secondary-color-rgb',
    '--tertiary-color-rgb',
    '--quaternary-color-rgb'
  ];
  
  console.log('CSS Variables Debug:');
  variables.forEach(variable => {
    const value = computedStyle.getPropertyValue(variable).trim();
    console.log(`${variable}: ${value}`);
  });
  
  // Check if background squares exist
  const squares = document.querySelectorAll('.animated-squares rect');
  console.log(`Background squares found: ${squares.length}`);
  
  if (squares.length > 0) {
    console.log('First square fill:', squares[0].getAttribute('fill'));
    console.log('First square opacity:', squares[0].getAttribute('opacity'));
    console.log('First square visibility:', squares[0].getAttribute('visibility'));
  }
}

// Run debug on page load
document.addEventListener('DOMContentLoaded', function() {
  setTimeout(debugCSSVariables, 1000); // Wait for other scripts to load
});