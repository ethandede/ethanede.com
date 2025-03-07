// sudoku.js
const grid = document.getElementById('sudoku-grid');
const difficultySelect = document.getElementById('difficulty');
const mistakeCounter = document.getElementById('mistake-counter');
const numberStatusGrid = document.getElementById('number-status-grid');
let board = Array(81).fill(0);
let notes = Array(81).fill().map(() => new Set());
let initialBoard = [];
let mistakeCount = 0;
let gameOver = false;
let gameWon = false;
let highlightedNumber = null;
let timerInterval = null;
let startTime = null;
let autoCandidates = Array(81).fill().map(() => new Set());
let isAutoCandidatesEnabled = false;

// Initialize game with empty grid and overlay
function initializeGame() {
  createGrid();
  createNumberStatusGrid();
  updateMistakeCounter();
  const timerElement = document.getElementById('timer');
  if (timerElement) timerElement.textContent = '0:00';
  showStartOverlay();
}

// Show start overlay over the grid only
function showStartOverlay() {
  const existingOverlay = document.querySelector('.start-overlay');
  if (existingOverlay) existingOverlay.remove();
  const overlay = document.createElement('div');
  overlay.classList.add('start-overlay');
  overlay.innerHTML = `
    <div class="start-message">
      <h2>Ready to Play?</h2>
      <p>Choose your difficulty level and click "Start Game" to begin.</p>
    </div>
  `;
  document.querySelector('.sudoku-grid').appendChild(overlay);
}

async function newGame() {
  if (!difficultySelect) return console.error('Difficulty select not found');
  console.log('New game triggered with difficulty:', difficultySelect.value);

  board = Array(81).fill(0);
  notes = Array(81).fill().map(() => new Set());
  initialBoard = [];
  mistakeCount = 0;
  gameOver = false;
  gameWon = false;
  highlightedNumber = null;
  const cells = document.querySelectorAll('.cell');
  cells.forEach(cell => {
    cell.innerHTML = '';
    cell.classList.remove('notes', 'highlight', 'thinking-type');
    cell.style.color = 'var(--primary-color)';
  });
  const overlay = document.querySelector('.game-over-overlay') || document.querySelector('.success-overlay') || document.querySelector('.start-overlay');
  if (overlay) overlay.remove();

  if (timerInterval) clearInterval(timerInterval);
  startTime = Date.now();
  timerInterval = setInterval(updateTimerDisplay, 1000);
  const timerElement = document.getElementById('timer');
  if (timerElement) timerElement.textContent = '0:00';

  const difficulty = difficultySelect.value;
  const newBoard = generatePuzzle(difficulty);
  await thinkingAnimation(newBoard);
  board = newBoard;
  initialBoard = board.slice();
  notes = Array(81).fill().map(() => new Set());
  computeAutoCandidates();
  updateGrid();
}

function updateTimerDisplay() {
  if (!startTime || gameWon || gameOver) return;
  const timerElement = document.getElementById('timer');
  if (!timerElement) return console.error('Timer element not found');
  const elapsed = Math.floor((Date.now() - startTime) / 1000);
  const minutes = Math.floor(elapsed / 60);
  const seconds = elapsed % 60;
  timerElement.textContent = `${minutes}:${seconds < 10 ? '0' + seconds : seconds}`;
}

function createGrid() {
  if (!grid) return console.error('Sudoku grid element not found');
  grid.innerHTML = '';
  for (let i = 0; i < 81; i++) {
    const cell = document.createElement('div');
    cell.classList.add('cell');
    cell.addEventListener('click', (e) => editCell(i, e));
    grid.appendChild(cell);
  }
}

function editCell(index, event) {
  if (gameOver || gameWon) return;
  const cell = event.target;

  if (board[index] !== 0) {
    toggleHighlight(board[index]);
    console.log('Clicked number, toggling highlight:', board[index]);
    return;
  }

  if (initialBoard[index] !== 0) return;

  cell.contentEditable = true;
  cell.focus();
  cell.textContent = '';
  cell.removeAttribute('data-typed');

  console.log('Cell focused, showing overlay', index);
  showNotesOverlay(index, cell);

  cell.addEventListener('keydown', (e) => handleKeydown(e, index), { once: true });
  cell.addEventListener('blur', () => {
    handleBlur(index);
    const overlay = cell.querySelector('.notes-overlay');
    if (overlay) overlay.remove();
    console.log('Blurred, overlay removed, re-rendering grid');
    updateGrid(index);
  }, { once: true });
}

function createNumberStatusGrid() {
  if (!numberStatusGrid) return console.error('Number status grid element not found');
  numberStatusGrid.innerHTML = '';
  for (let num = 1; num <= 9; num++) {
    const cell = document.createElement('div');
    cell.classList.add('number-cell');
    const text = document.createElement('span');
    text.classList.add('number-text');
    text.textContent = num;
    cell.dataset.number = num;
    cell.appendChild(text);
    for (let i = 0; i < 9; i++) {
      const segment = document.createElement('div');
      segment.classList.add('segment');
      cell.appendChild(segment);
    }
    cell.addEventListener('click', () => toggleHighlight(num));
    numberStatusGrid.appendChild(cell);
  }
}

function computeAutoCandidates() {
  autoCandidates = Array(81).fill().map(() => new Set());
  for (let index = 0; index < 81; index++) {
    if (board[index] === 0 && initialBoard[index] === 0) {
      for (let num = 1; num <= 9; num++) {
        if (isValidMove(index, num, board)) {
          autoCandidates[index].add(num);
        }
      }
    }
  }
}

function updateGrid(clickedIndex = null) {
  const cells = document.querySelectorAll('.cell');
  cells.forEach((cell, index) => {
    cell.innerHTML = '';
    cell.classList.remove('notes', 'highlighted', 'highlight');
    if (board[index] !== 0) {
      cell.textContent = board[index];
      cell.style.color = initialBoard[index] === 0 ? 'var(--primary-color)' : 'rgba(255, 255, 255, 0.6)';
    } else {
      const displayNotes = new Set(notes[index]);
      if (isAutoCandidatesEnabled) {
        autoCandidates[index].forEach(num => displayNotes.add(num));
      }
      if (displayNotes.size > 0) {
        cell.classList.add('notes');
        for (let num = 1; num <= 9; num++) {
          const span = document.createElement('span');
          span.textContent = displayNotes.has(num) ? num : '';
          cell.appendChild(span);
        }
      } else {
        cell.textContent = '';
      }
    }
    if (highlightedNumber && board[index] === highlightedNumber) {
      cell.classList.add('highlight');
    }
  });

  if (clickedIndex !== null && clickedIndex >= 0 && clickedIndex < 81 && board[clickedIndex] !== 0) {
    highlightedNumber = board[clickedIndex];
    cells.forEach((cell, index) => {
      if (board[index] === highlightedNumber) cell.classList.add('highlight');
    });
  }

  updateMistakeCounter();
  updateNumberStatusGrid();
}

function updateMistakeCounter() {
  if (!mistakeCounter) return;
  mistakeCounter.innerHTML = '';
  for (let i = 0; i < 4; i++) {
    const dot = document.createElement('div');
    dot.classList.add('dot');
    if (i < mistakeCount) dot.classList.add('filled');
    mistakeCounter.appendChild(dot);
  }
  if (mistakeCount >= 4 && !gameOver && !gameWon) {
    showGameOver();
  } else if (board.every((val, idx) => val !== 0 && isValidMove(idx, val, board)) && !gameWon) {
    showSuccessAnimation();
  }
}

function updateNumberStatusGrid() {
  const numberCells = document.querySelectorAll('.number-cell');
  const numberCounts = Array(10).fill(0);
  const noteCounts = Array(10).fill(0);
  board.forEach(val => numberCounts[val]++);
  notes.forEach(noteSet => noteSet.forEach(num => noteCounts[num]++));
  numberCells.forEach(cell => {
    const num = parseInt(cell.dataset.number);
    const count = numberCounts[num];
    const segments = cell.querySelectorAll('.segment');
    segments.forEach((segment, index) => {
      segment.classList.toggle('filled', index < count);
    });
    cell.classList.toggle('solved', count === 9);
    cell.classList.toggle('noted', noteCounts[num] > 0 && count < 9);
  });
}

function toggleHighlight(num) {
  if (gameOver || gameWon) return;
  highlightedNumber = highlightedNumber === num ? null : num;
  console.log('Toggling highlight for number:', num, 'highlightedNumber:', highlightedNumber);
  updateGrid();
}

function showNotesOverlay(index, cell) {
  const existingOverlay = cell.querySelector('.notes-overlay');
  if (existingOverlay) existingOverlay.remove();

  const overlay = document.createElement('div');
  overlay.classList.add('notes-overlay');

  function updateOverlay() {
    overlay.innerHTML = '';
    for (let num = 1; num <= 9; num++) {
      const option = document.createElement('div');
      option.classList.add('note-option');
      option.textContent = num;
      if (notes[index].has(num)) option.classList.add('selected');
      option.addEventListener('click', (e) => {
        e.preventDefault();
        toggleNote(index, num);
        updateGrid(index);
        updateOverlay();
      });
      overlay.appendChild(option);
    }
  }

  updateOverlay();
  cell.appendChild(overlay);
}

function toggleNote(index, num) {
  if (notes[index].has(num)) {
    notes[index].delete(num);
  } else {
    notes[index].add(num);
  }
  console.log(`Toggled ${num} at index ${index}`, notes[index]);
}

function clearNotesInSection(index, value) {
  const row = Math.floor(index / 9);
  const col = index % 9;
  const startRow = Math.floor(row / 3) * 3;
  const startCol = Math.floor(col / 3) * 3;

  for (let i = row * 9; i < row * 9 + 9; i++) notes[i].delete(value);
  for (let i = col; i < 81; i += 9) notes[i].delete(value);
  for (let i = 0; i < 3; i++) {
    for (let j = 0; j < 3; j++) {
      notes[(startRow + i) * 9 + (startCol + j)].delete(value);
    }
  }
}

function handleKeydown(event, index) {
  const cell = event.target;
  const key = event.key;
  console.log('Keydown:', key, 'at index', index);

  if (key >= '1' && key <= '9') {
    event.preventDefault();
    cell.setAttribute('data-typed', 'true');
    const value = parseInt(key);
    console.log('Processing guess:', value, 'Valid?', isValidMove(index, value, board));
    if (isValidMove(index, value, board)) {
      board[index] = value;
      notes[index].clear();
      clearNotesInSection(index, value);
      cell.textContent = value;
      cell.style.color = 'var(--primary-color)';
      cell.contentEditable = false;
      computeAutoCandidates();
      updateGrid(index);
    } else {
      mistakeCount++;
      cell.textContent = value;
      cell.classList.add('invalid');
      setTimeout(() => {
        board[index] = 0;
        cell.textContent = '';
        cell.classList.remove('invalid');
        cell.contentEditable = false;
        computeAutoCandidates();
        updateGrid(index);
      }, 1000);
    }
  } else if (key === 'Backspace' || key === 'Delete') {
    event.preventDefault();
    board[index] = 0;
    notes[index].clear();
    cell.textContent = '';
    cell.contentEditable = false;
    computeAutoCandidates();
    updateGrid(index);
  } else if (key !== 'Enter' && key !== 'Tab') {
    event.preventDefault();
    console.log('Prevented non-numeric key:', key);
  }
}

function handleBlur(index) {
  const cell = document.querySelectorAll('.cell')[index];
  const value = cell.textContent.trim();

  if (cell.getAttribute('data-typed') === 'true' && value && !isNaN(value) && value >= 1 && value <= 9 && board[index] === 0) {
    const num = parseInt(value);
    if (isValidMove(index, num, board)) {
      board[index] = num;
      notes[index].clear();
      clearNotesInSection(index, num);
    } else {
      mistakeCount++;
      cell.classList.add('invalid');
      setTimeout(() => {
        board[index] = 0;
        cell.textContent = '';
        cell.classList.remove('invalid');
        updateGrid(index);
      }, 1000);
    }
  }
  cell.contentEditable = false;
  cell.removeAttribute('data-typed');
  updateGrid(index);
}

function isValidMove(index, value, checkBoard) {
  const row = Math.floor(index / 9);
  const col = index % 9;
  for (let i = 0; i < 9; i++) {
    if (checkBoard[row * 9 + i] === value && i !== col) return false;
    if (checkBoard[i * 9 + col] === value && i !== row) return false;
  }
  const startRow = Math.floor(row / 3) * 3;
  const startCol = Math.floor(col / 3) * 3;
  for (let i = 0; i < 3; i++) {
    for (let j = 0; j < 3; j++) {
      if (checkBoard[(startRow + i) * 9 + (startCol + j)] === value &&
          (startRow + i) !== row && (startCol + j) !== col) return false;
    }
  }
  return true;
}

function generatePuzzle(difficulty) {
  console.log(`Generating puzzle with difficulty: ${difficulty}`);
  const full = Array(81).fill(0);
  if (!solve(full)) {
    console.error('Failed to solve initial board');
    return full;
  }
  const solution = full.slice();
  const difficultyTargets = {
    quick: { minClues: 40, maxClues: 45, minTechnique: 0 },
    easy: { minClues: 36, maxClues: 40, minTechnique: 0 },
    'not easy': { minClues: 30, maxClues: 35, minTechnique: 1 },
    hard: { minClues: 27, maxClues: 31, minTechnique: 2 },
    expert: { minClues: 22, maxClues: 26, minTechnique: 3 }, // New Expert
    mental: { minClues: 20, maxClues: 24, minTechnique: 4 }
  };
  const target = difficultyTargets[difficulty] || difficultyTargets.easy;

  let base = Array(81).fill(0);
  let clues = 0;
  let indices = Array.from({ length: 81 }, (_, i) => i);
  const minSeed = difficulty === 'quick' || difficulty === 'easy' ? 30 : 17;
  let targetClues = Math.floor(Math.random() * (target.maxClues - target.minClues + 1)) + target.minClues;

  while (clues < minSeed && indices.length > 0) {
    const idx = Math.floor(Math.random() * indices.length);
    const pos = indices.splice(idx, 1)[0];
    base[pos] = solution[pos];
    clues++;
    console.log(`Seeded cell ${pos}, clues: ${clues}`);
  }

  while (clues < targetClues && indices.length > 0) {
    const idx = Math.floor(Math.random() * indices.length);
    const pos = indices.splice(idx, 1)[0];
    base[pos] = solution[pos];
    clues++;
    console.log(`Added cell ${pos}, clues: ${clues}`);
    if (!hasUniqueSolution(base, solution)) {
      console.log(`Checking uniqueness at ${clues} clues`);
    }
  }

  if (!hasUniqueSolution(base, solution)) {
    console.warn('Puzzle not unique, adjusting...');
    while (clues < target.maxClues && indices.length > 0) {
      const idx = Math.floor(Math.random() * indices.length);
      const pos = indices.splice(idx, 1)[0];
      base[pos] = solution[pos];
      clues++;
      if (hasUniqueSolution(base, solution)) break;
    }
  }

  console.log(`Initial clues: ${clues}, board:`, base);
  let analysis = analyzeDifficulty(base, solution, difficulty);
  console.log(`Final puzzle: clues=${clues}, hardest technique=${analysis.hardestTechnique}`);
  if (clues > target.maxClues) {
    console.warn(`Stopped at ${clues} clues, above max ${target.maxClues} for ${difficulty}`);
  } else if (clues < target.minClues) {
    console.warn(`Stopped at ${clues} clues, below min ${target.minClues} for ${difficulty}`);
  }
  return base;
}

function hasUniqueSolution(board, solution) {
  let solutions = 0;
  const maxSolutions = 2;
  function countSolutions(b, depth = 0) {
    if (depth > 1500) return true; // Max depth
    const empty = b.indexOf(0);
    if (empty === -1) {
      solutions++;
      return solutions > 1;
    }
    for (let num = 1; num <= 9 && solutions < maxSolutions; num++) {
      if (isValidMove(empty, num, b)) {
        b[empty] = num;
        if (countSolutions(b, depth + 1)) return true;
        b[empty] = 0;
      }
    }
    return false;
  }
  const temp = board.slice();
  countSolutions(temp);
  return solutions === 1;
}

function analyzeDifficulty(base, solution, difficulty) {
  const difficultyTargets = {
    quick: { minClues: 40, maxClues: 45, minTechnique: 0 },
    easy: { minClues: 36, maxClues: 40, minTechnique: 0 },
    'not easy': { minClues: 30, maxClues: 35, minTechnique: 1 },
    hard: { minClues: 27, maxClues: 31, minTechnique: 2 },
    expert: { minClues: 22, maxClues: 26, minTechnique: 3 },
    mental: { minClues: 20, maxClues: 24, minTechnique: 4 }
  };
  const target = difficultyTargets[difficulty] || difficultyTargets.easy;
  let tempBoard = base.slice();
  let hardestTechnique = -1;
  let zeros = tempBoard.filter(x => x === 0).length;

  for (let level = 0; level <= 4; level++) {
    let progress = applyTechnique(level, tempBoard, true);
    let newZeros = tempBoard.filter(x => x === 0).length;
    if (progress) {
      hardestTechnique = level;
      zeros = newZeros;
    }
    console.log(`Analyzing level ${level}, progress: ${progress}, solved: ${zeros === 0}, zeros: ${zeros} -> ${newZeros}`);
    if (zeros === 0 || hardestTechnique >= target.minTechnique) break;
  }

  hardestTechnique = hardestTechnique === -1 ? target.minTechnique : Math.min(hardestTechnique, 
    difficulty === 'hard' ? 2 : (difficulty === 'expert' ? 3 : target.minTechnique));
  return { hardestTechnique };
}

function hasUniqueSolution(board, solution) {
  let solutions = 0;
  const maxSolutions = 2;
  function countSolutions(b, depth = 0) {
    if (depth > 500) return true; // Max depth
    const empty = b.indexOf(0);
    if (empty === -1) {
      solutions++;
      return solutions > 1;
    }
    for (let num = 1; num <= 9 && solutions < maxSolutions; num++) {
      if (isValidMove(empty, num, b)) {
        b[empty] = num;
        if (countSolutions(b, depth + 1)) return true;
        b[empty] = 0;
      }
    }
    return false;
  }
  const temp = board.slice();
  countSolutions(temp);
  return solutions === 1;
}

function applyTechnique(level, board, analyzeOnly = false) {
  let progress = false;
  let tempBoard = analyzeOnly ? board.slice() : board;

  function getCandidates(index) {
    if (tempBoard[index] !== 0) return [];
    const candidates = [];
    for (let num = 1; num <= 9; num++) {
      if (isValidMove(index, num, tempBoard)) candidates.push(num);
    }
    return candidates;
  }

  if (level === 0) { // Singles
    for (let i = 0; i < 81; i++) {
      if (tempBoard[i] === 0) {
        const candidates = getCandidates(i);
        if (candidates.length === 1) {
          if (!analyzeOnly) tempBoard[i] = candidates[0];
          progress = true;
        }
      }
    }
    for (let unit = 0; unit < 27; unit++) {
      const cells = getUnitCells(unit);
      const counts = Array(10).fill(0);
      const positions = Array(10).fill().map(() => []);
      cells.forEach(i => {
        if (tempBoard[i] === 0) {
          getCandidates(i).forEach(num => {
            counts[num]++;
            positions[num].push(i);
          });
        }
      });
      for (let num = 1; num <= 9; num++) {
        if (counts[num] === 1) {
          if (!analyzeOnly) tempBoard[positions[num][0]] = num;
          progress = true;
        }
      }
    }
  } else if (level === 1) { // Locked Candidates
    for (let box = 0; box < 9; box++) {
      const boxStart = Math.floor(box / 3) * 27 + (box % 3) * 3;
      const boxCells = [];
      for (let i = 0; i < 9; i++) boxCells.push(boxStart + Math.floor(i / 3) * 9 + (i % 3));
      const boxCands = Array(10).fill().map(() => []);
      boxCells.forEach(i => {
        if (tempBoard[i] === 0) getCandidates(i).forEach(num => boxCands[num].push(i));
      });
      for (let num = 1; num <= 9; num++) {
        if (boxCands[num].length > 0) {
          const rows = new Set(boxCands[num].map(i => Math.floor(i / 9)));
          const cols = new Set(boxCands[num].map(i => i % 9));
          if (rows.size === 1 || cols.size === 1) {
            progress = true;
            if (!analyzeOnly) {
              if (rows.size === 1) {
                const row = [...rows][0];
                for (let col = 0; col < 9; col++) {
                  const idx = row * 9 + col;
                  if (!boxCells.includes(idx) && tempBoard[idx] === 0 && isValidMove(idx, num, tempBoard)) {
                    tempBoard[idx] = num;
                  }
                }
              } else if (cols.size === 1) {
                const col = [...cols][0];
                for (let row = 0; row < 9; row++) {
                  const idx = row * 9 + col;
                  if (!boxCells.includes(idx) && tempBoard[idx] === 0 && isValidMove(idx, num, tempBoard)) {
                    tempBoard[idx] = num;
                  }
                }
              }
            }
          }
        }
      }
    }
  } else if (level === 2) { // Naked Pairs/Triples
    for (let unit = 0; unit < 27; unit++) {
      const cells = getUnitCells(unit);
      const cands = cells.map(i => tempBoard[i] === 0 ? getCandidates(i) : []);
      for (let i = 0; i < cells.length; i++) {
        if (cands[i].length === 2 || cands[i].length === 3) {
          const pair = cands[i];
          const matches = [];
          for (let j = 0; j < cells.length; j++) {
            if (i !== j && cands[j].length === pair.length && pair.every(n => cands[j].includes(n))) {
              matches.push(j);
            }
          }
          if (matches.length === pair.length - 1) {
            progress = true;
            if (!analyzeOnly) {
              const group = [i, ...matches].map(idx => cells[idx]);
              for (let k = 0; k < cells.length; k++) {
                const idx = cells[k];
                if (!group.includes(idx) && tempBoard[idx] === 0) {
                  const cand = getCandidates(idx);
                  if (cand.length === 1 && pair.includes(cand[0])) {
                    tempBoard[idx] = cand[0];
                  }
                }
              }
            }
          }
        }
      }
    }
  } else if (level === 3) { // X-Wing
    for (let num = 1; num <= 9; num++) {
      const rowCands = Array(9).fill().map(() => []);
      for (let i = 0; i < 81; i++) {
        if (tempBoard[i] === 0 && isValidMove(i, num, tempBoard)) {
          rowCands[Math.floor(i / 9)].push(i);
        }
      }
      for (let r1 = 0; r1 < 8; r1++) {
        if (rowCands[r1].length === 2) {
          for (let r2 = r1 + 1; r2 < 9; r2++) {
            if (rowCands[r2].length === 2 && rowCands[r1][0] % 9 === rowCands[r2][0] % 9 && rowCands[r1][1] % 9 === rowCands[r2][1] % 9) {
              progress = true;
              if (!analyzeOnly) {
                const cols = [rowCands[r1][0] % 9, rowCands[r1][1] % 9];
                for (let row = 0; row < 9; row++) {
                  if (row !== r1 && row !== r2) {
                    cols.forEach(col => {
                      const idx = row * 9 + col;
                      if (tempBoard[idx] === 0 && isValidMove(idx, num, tempBoard)) {
                        tempBoard[idx] = num;
                      }
                    });
                  }
                }
              }
            }
          }
        }
      }
    }
  } else if (level === 4) { // Forcing Chains
    for (let i = 0; i < 81; i++) {
      if (tempBoard[i] === 0) {
        const candidates = getCandidates(i);
        if (candidates.length > 1) {
          for (let num of candidates) {
            const testBoard = tempBoard.slice();
            testBoard[i] = num;
            if (!canSolveWithSingles(testBoard)) {
              progress = true;
              if (!analyzeOnly) tempBoard[i] = num;
              break;
            }
          }
          if (progress) break;
        }
      }
    }
  }

  return progress; // Just return progress, modify board in-place
}

function getUnitCells(unit) {
  const cells = [];
  if (unit < 9) {
    for (let i = 0; i < 9; i++) cells.push(unit * 9 + i);
  } else if (unit < 18) {
    for (let i = 0; i < 9; i++) cells.push(i * 9 + (unit - 9));
  } else {
    const box = unit - 18;
    const start = Math.floor(box / 3) * 27 + (box % 3) * 3;
    for (let i = 0; i < 9; i++) cells.push(start + Math.floor(i / 3) * 9 + (i % 3));
  }
  return cells;
}

function canSolveWithSingles(board) {
  const temp = board.slice();
  let changed = true;
  while (changed && temp.indexOf(0) !== -1) {
    changed = false;
    for (let i = 0; i < 81; i++) {
      if (temp[i] === 0) {
        let candidates = [];
        for (let num = 1; num <= 9; num++) {
          if (isValidMove(i, num, temp)) candidates.push(num);
        }
        if (candidates.length === 1) {
          temp[i] = candidates[0];
          changed = true;
        } else if (candidates.length === 0) {
          return false;
        }
      }
    }
  }
  return temp.indexOf(0) === -1;
}

function solve(board) {
  const empty = board.indexOf(0);
  if (empty === -1) return true;
  const nums = Array.from({ length: 9 }, (_, i) => i + 1).sort(() => Math.random() - 0.5);
  for (let num of nums) {
    if (isValidMove(empty, num, board)) {
      board[empty] = num;
      if (solve(board)) return true;
      board[empty] = 0;
    }
  }
  return false;
}

async function thinkingAnimation(finalBoard) {
  console.log('Starting thinking animation with board:', finalBoard);
  if (!finalBoard || finalBoard.every(val => val === 0)) return;
  const cells = document.querySelectorAll('.cell');
  if (!cells.length) return;
  const clueIndices = finalBoard.map((value, index) => value !== 0 ? index : null)
    .filter(index => index !== null)
    .sort(() => Math.random() - 0.5);
  for (let i = 0; i < clueIndices.length; i++) {
    const index = clueIndices[i];
    cells[index].textContent = finalBoard[index];
    cells[index].classList.add('thinking-type');
    console.log(`Animating cell ${index} with value ${finalBoard[index]}`);
    await new Promise(resolve => setTimeout(resolve, 50)); // Increased to 50ms for visibility
    cells[index].classList.remove('thinking-type');
  }
  console.log('Thinking animation completed');
}

function showSuccessAnimation() {
  gameWon = true;
  if (timerInterval) clearInterval(timerInterval);
  const elapsed = startTime ? Math.floor((Date.now() - startTime) / 1000) : 0;
  const minutes = Math.floor(elapsed / 60);
  const seconds = elapsed % 60;
  const timeString = `${minutes}:${seconds < 10 ? '0' + seconds : seconds}`;
  const grid = document.querySelector('.sudoku-grid');
  grid.classList.add('success-animation');
  setTimeout(() => grid.classList.remove('success-animation'), 2000);
  const overlay = document.createElement('div');
  overlay.classList.add('success-overlay');
  overlay.innerHTML = `
    <div class="success-message">
      <h2>Well Done!</h2>
      <p>You solved the puzzle in ${timeString} with ${mistakeCount} mistake${mistakeCount === 1 ? '' : 's'}!</p>
      <button class="cta-button play-again">Play Again</button>
    </div>
  `;
  document.querySelector('.sudoku-container').appendChild(overlay);
  document.querySelector('.play-again').addEventListener('click', newGame);
}

function showGameOver() {
  gameOver = true;
  if (timerInterval) clearInterval(timerInterval);
  const overlay = document.createElement('div');
  overlay.classList.add('game-over-overlay');
  overlay.innerHTML = `
    <div class="game-over-message">
      <h2>Game Over</h2>
      <p>Too many mistakes! Want to try again?</p>
      <button class="cta-button play-again">Play Again</button>
    </div>
  `;
  document.querySelector('.sudoku-container').appendChild(overlay);
  document.querySelector('.play-again').addEventListener('click', newGame);
}

function resetGame() {
  board = initialBoard.slice();
  notes = Array(81).fill().map(() => new Set());
  mistakeCount = 0;
  gameOver = false;
  gameWon = false;
  highlightedNumber = null;
  const overlay = document.querySelector('.game-over-overlay') || document.querySelector('.success-overlay');
  if (overlay) overlay.remove();
  computeAutoCandidates();
  updateGrid();
}

// DOMContentLoaded
document.addEventListener('DOMContentLoaded', () => {
  console.log('DOM loaded, initializing Sudoku');
  if (!grid || !difficultySelect || !mistakeCounter || !numberStatusGrid || !document.getElementById('timer') || !document.getElementById('auto-candidates')) {
    return console.error('Required elements missing');
  }
  initializeGame();
  document.getElementById('start-game').addEventListener('click', newGame);
  difficultySelect.addEventListener('change', () => {
    const overlay = document.querySelector('.start-overlay');
    if (overlay) showStartOverlay();
  });
  document.getElementById('auto-candidates').addEventListener('change', (e) => {
    isAutoCandidatesEnabled = e.target.checked;
    console.log('Auto-candidates toggled:', isAutoCandidatesEnabled);
    updateGrid();
  });
});

function debounce(func, wait) {
  let timeout;
  return (...args) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => func(...args), wait);
  };
}
const debouncedUpdateGrid = debounce(updateGrid, 50);