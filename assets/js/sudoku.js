const grid = document.getElementById('sudoku-grid');
const difficultySelect = document.getElementById('difficulty');
const mistakeCounter = document.getElementById('mistake-counter');
const numberStatusGrid = document.getElementById('number-status-grid');
let board = Array(81).fill(0);
let notes = Array(81).fill().map(() => new Set());
let initialBoard = [];
let solution = [];
let mistakeCount = 0;
let gameOver = false;
let gameWon = false;
let highlightedNumber = null;
let timerInterval = null;
let startTime = null;
let autoCandidates = Array(81).fill().map(() => new Set());
let isAutoCandidatesEnabled = false;
let userSolved = Array(81).fill(false);
let selectedCell = null;
let inputMode = 'guess'; // Added global declaration
let isLoading = false;
let secondsElapsed = 0;

function initializeGame() {
  createGrid();
  createNumberStatusGrid();
  updateMistakeCounter();
  const timerElement = document.getElementById('timer');
  if (timerElement) timerElement.textContent = '0:00';
  showStartOverlay();
}

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

function newGame() {
  // Generate new puzzle (your existing logic)
  const puzzle = generatePuzzle(difficultySelect.value); // Assuming this exists
  initialBoard = puzzle.slice(); // Store initial state
  board = puzzle.slice();
  notes = Array(81).fill().map(() => new Set());
  mistakeCount = 0;
  secondsElapsed = 0;

  if (timerInterval) clearInterval(timerInterval);
  timerInterval = setInterval(() => {
    secondsElapsed++;
    const minutes = Math.floor(secondsElapsed / 60);
    const seconds = secondsElapsed % 60;
    const timeStr = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
    document.getElementById('timer').textContent = timeStr;
    if (window.innerWidth <= 991) {
      document.getElementById('timer-mobile').textContent = timeStr;
    }
  }, 1000);

  updateGrid();
  updateNumberStatusGrid();
  document.getElementById('mistake-counter').innerHTML = ''; // Reset dots
  if (window.innerWidth <= 991) {
    document.getElementById('mistake-counter-mobile').innerHTML = '';
  }
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
    cell.dataset.index = i;
    grid.appendChild(cell);
  }
}

function editCell(index, event) {
  console.log('editCell running for index:', index);
  if (gameOver || gameWon) return;
  const cell = event.target.closest('.cell');
  const isMobile = window.innerWidth <= 991;

  console.log('editCell called for index:', index, 'cell:', cell);

  // Clear all previous highlights and overlays
  document.querySelectorAll('.cell').forEach(c => {
    if (c !== cell) {
      c.classList.remove('highlighted', 'highlight-subgrid', 'highlight-row', 'highlight-column');
      const overlay = c.querySelector('.notes-overlay');
      if (overlay) overlay.remove();
    }
    c.classList.remove('highlight'); // Clear number highlights universally
  });

  selectedCell = cell;
  cell.classList.add('highlighted');
  highlightRelatedCells(index);

  console.log('Highlights applied for index:', index);

  grid.offsetHeight;

  if (board[index] !== 0) {
    toggleHighlight(board[index]); // Only apply number highlights for filled cells
  } else if (!isMobile) {
    cell.contentEditable = true;
    cell.focus();
    cell.textContent = '';
    cell.addEventListener('keydown', (e) => handleKeydown(e, index), { once: true });
    cell.addEventListener('blur', () => {
      selectedCell = null;
      updateGrid();
    }, { once: true });
    showNotesOverlay(index, cell);
  }

  updateGrid();
}

function createNumberStatusGrid() {
  if (!numberStatusGrid) return console.error('Number status grid not found');
  numberStatusGrid.innerHTML = ''; // Clear existing content
  const isMobile = window.innerWidth <= 991;

  if (isMobile) {
    // Mode row
    const modeRow = document.createElement('div');
    modeRow.classList.add('mode-row');

    const guessBtn = document.createElement('div');
    guessBtn.classList.add('mode-option', 'guess-option');
    guessBtn.dataset.mode = 'guess';
    guessBtn.textContent = 'Guess';
    guessBtn.addEventListener('click', () => {
      inputMode = 'guess';
      updateNumberStatusGrid();
    });

    const candidateBtn = document.createElement('div');
    candidateBtn.classList.add('mode-option', 'candidate-option');
    candidateBtn.dataset.mode = 'notes';
    candidateBtn.textContent = 'Candidate';
    candidateBtn.addEventListener('click', () => {
      inputMode = 'notes';
      updateNumberStatusGrid();
    });

    modeRow.appendChild(guessBtn);
    modeRow.appendChild(candidateBtn);
    numberStatusGrid.appendChild(modeRow);

    // Number row
    const numberRow = document.createElement('div');
    numberRow.classList.add('number-row');
    for (let num = 1; num <= 9; num++) {
      const cell = document.createElement('div');
      cell.classList.add('number-cell');
      cell.dataset.number = num;
      cell.textContent = num;
      cell.addEventListener('click', () => handleNumberInput(num));
      numberRow.appendChild(cell);
    }
    numberStatusGrid.appendChild(numberRow);
  } else {
    // Desktop logic unchanged
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
      cell.addEventListener('click', () => handleNumberInput(num));
      numberStatusGrid.appendChild(cell);
    }
  }
  updateNumberStatusGrid(); // Call to update state after creation
}

function updateNumberStatusGrid() {
  const numberCells = document.querySelectorAll('.number-row .number-cell'); // Scope to .number-row
  const isMobile = window.innerWidth <= 991;

  if (isMobile) {
    const guessBtn = document.querySelector('.mode-row .guess-option');
    const candidateBtn = document.querySelector('.mode-row .candidate-option');

    if (guessBtn && candidateBtn) {
      // Ensure only one tab is active
      guessBtn.classList.remove('active');
      candidateBtn.classList.remove('active');
      if (inputMode === 'guess') {
        guessBtn.classList.add('active');
      } else if (inputMode === 'notes') {
        candidateBtn.classList.add('active');
      }

      // Debug logging
      console.log('inputMode:', inputMode);
      console.log('Guess classes:', guessBtn.classList.toString());
      console.log('Candidate classes:', candidateBtn.classList.toString());
    } else {
      console.warn('Mode buttons not found:', { guessBtn, candidateBtn });
    }

    // Update number cells
    numberCells.forEach(cell => {
      const num = parseInt(cell.dataset.number);
      if (num) { // Only number cells
        cell.classList.remove('guess-mode', 'notes-mode');
        cell.classList.add(inputMode === 'guess' ? 'guess-mode' : 'notes-mode');
      }
    });
  } else {
    // Desktop logic
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
}

function handleNumberInput(num) {
  const isMobile = window.innerWidth <= 991;
  if (gameOver || gameWon) return;

  if (!selectedCell) {
    toggleHighlight(num);
    return;
  }

  const index = parseInt(selectedCell.dataset.index);
  if (initialBoard[index] !== 0) return;

  if (isMobile) {
    if (inputMode === 'guess') {
      handleGuess(index, num);
    } else {
      console.log('Mobile toggling note:', num, 'at index:', index);
      toggleNote(index, num);
    }
  } else {
    handleGuess(index, num);
  }
  computeAutoCandidates();
  updateGrid();
}

function handleGuess(index, num) {
  if (isValidMove(index, num, board)) {
    board[index] = num;
    userSolved[index] = true;
    notes[index].clear();
    clearNotesInSection(index, num);
  } else {
    mistakeCount++;
    board[index] = num;
    selectedCell.classList.add('invalid');
    setTimeout(() => {
      board[index] = 0;
      selectedCell.classList.remove('invalid');
      updateGrid();
    }, 1000);
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
  console.log('updateGrid called, selectedCell:', selectedCell ? selectedCell.dataset.index : 'none');
  const cells = document.querySelectorAll('.cell');
  cells.forEach((cell, index) => {
    // Preserve notes-overlay by not clearing innerHTML fully
    const overlay = cell.querySelector('.notes-overlay');
    cell.innerHTML = '';
    if (overlay) cell.appendChild(overlay); // Re-append if present
    cell.classList.remove('notes', 'highlight', 'user-solved', 'button-solved', 'initial', 'invalid');

    if (board[index] !== 0) {
      cell.textContent = board[index];
      if (initialBoard[index] !== 0) cell.classList.add('initial');
      else if (userSolved[index]) cell.classList.add('user-solved');
      else cell.classList.add('button-solved');
    } else {
      const displayNotes = new Set(notes[index]);
      if (isAutoCandidatesEnabled) autoCandidates[index].forEach(n => displayNotes.add(n));
      if (displayNotes.size > 0 && !overlay) { // Only add notes if no overlay
        cell.classList.add('notes');
        for (let num = 1; num <= 9; num++) {
          const span = document.createElement('span');
          span.textContent = displayNotes.has(num) ? num : '';
          cell.appendChild(span);
        }
      }
    }
    if (highlightedNumber && board[index] === highlightedNumber) cell.classList.add('highlight');
  });
  console.log('Post-updateGrid cell 44 innerHTML:', document.querySelector('[data-index="44"]').innerHTML);
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

function toggleHighlight(num) {
  if (gameOver || gameWon) return;
  highlightedNumber = highlightedNumber === num ? null : num;
  console.log('Toggling highlight for number:', num, 'highlightedNumber:', highlightedNumber);
  updateGrid();
}

function highlightRelatedCells(index) {
  const row = Math.floor(index / 9);
  const col = index % 9;
  const subgridStartRow = Math.floor(row / 3) * 3;
  const subgridStartCol = Math.floor(col / 3) * 3;

  const cells = document.querySelectorAll('.cell');

  for (let i = row * 9; i < row * 9 + 9; i++) {
    cells[i].classList.add('highlight-row');
  }
  for (let i = col; i < 81; i += 9) {
    cells[i].classList.add('highlight-column');
  }
  for (let r = subgridStartRow; r < subgridStartRow + 3; r++) {
    for (let c = subgridStartCol; c < subgridStartCol + 3; c++) {
      const subgridIndex = r * 9 + c;
      cells[subgridIndex].classList.add('highlight-subgrid');
    }
  }
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
      userSolved[index] = true; // Mark as user-solved
      notes[index].clear();
      clearNotesInSection(index, value);
      cell.textContent = value;
      cell.classList.remove('button-solved', 'initial', 'invalid');
      cell.classList.add('user-solved'); // White (#fff)
      cell.contentEditable = false;
      computeAutoCandidates();
      updateGrid(index);
    } else {
      mistakeCount++;
      cell.textContent = value;
      cell.classList.add('invalid'); // Pink (#BF3978)
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
    userSolved[index] = false; // Clear user-solved status
    notes[index].clear();
    cell.textContent = '';
    cell.classList.remove('user-solved', 'button-solved', 'initial', 'invalid');
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
      userSolved[index] = true; // Mark as user-solved
      notes[index].clear();
      clearNotesInSection(index, num);
      cell.classList.remove('button-solved', 'initial', 'invalid');
      cell.classList.add('user-solved'); // White (#fff)
    } else {
      mistakeCount++;
      cell.classList.add('invalid'); // Pink (#BF3978)
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
  const difficultyTargets = {
    quick: { minClues: 40, maxClues: 45, minTechnique: 0 },
    easy: { minClues: 36, maxClues: 40, minTechnique: 0 },
    'not easy': { minClues: 30, maxClues: 35, minTechnique: 1 },
    hard: { minClues: 27, maxClues: 31, minTechnique: 2 },
    expert: { minClues: 22, maxClues: 26, minTechnique: 3 },
    mental: { minClues: 20, maxClues: 24, minTechnique: 4 }
  };
  const target = difficultyTargets[difficulty] || difficultyTargets.easy;
  const targetClues = Math.floor(Math.random() * (target.maxClues - target.minClues + 1)) + target.minClues;

  // Generate a full solution
  let fullGrid = Array(81).fill(0);
  if (!generateFullGrid(fullGrid)) {
    console.error('Failed to generate a full grid');
    return fullGrid;
  }
  solution = fullGrid.slice(); // Store globally

  // Create puzzle by removing numbers
  let puzzle = fullGrid.slice();
  let indices = Array.from({ length: 81 }, (_, i) => i);
  shuffleArray(indices);
  let remainingClues = 81;
  let removalAttempts = 0;
  const maxAttempts = 500; // Prevent infinite loops

  while (remainingClues > target.maxClues && removalAttempts < maxAttempts) {
    let removed = false;
    for (let i = 0; i < indices.length && remainingClues > target.maxClues; i++) {
      const pos = indices[i];
      if (puzzle[pos] === 0) continue; // Skip already removed cells

      const temp = puzzle[pos];
      puzzle[pos] = 0;
      remainingClues--;

      const tempPuzzle = puzzle.slice();
      const numSolutions = countSolutions(tempPuzzle);
      if (numSolutions !== 1) {
        puzzle[pos] = temp; // Revert
        remainingClues++;
        console.log(`Reverted cell ${pos}, solutions: ${numSolutions}`);
      } else {
        console.log(`Removed cell ${pos}, clues remaining: ${remainingClues}`);
        removed = true;
      }
    }

    if (!removed) {
      // No removals this pass; reshuffle remaining indices and try again
      indices = indices.filter(i => puzzle[i] !== 0); // Keep only filled cells
      shuffleArray(indices);
      if (indices.length === 0) break; // No more cells to remove
    }
    removalAttempts++;
  }

  // Final check
  remainingClues = puzzle.filter(x => x !== 0).length;
  const finalSolutions = countSolutions(puzzle.slice());
  console.log(`Generated puzzle: clues=${remainingClues}, solutions=${finalSolutions}`);
  if (finalSolutions !== 1) {
    console.warn('Puzzle has multiple solutions, generation failed');
  }
  if (remainingClues < target.minClues || remainingClues > target.maxClues) {
    console.warn(`Clue count ${remainingClues} outside target range ${target.minClues}-${target.maxClues}`);
  }

  return puzzle;
}

// Helper: Generate a full, valid Sudoku grid
function generateFullGrid(board) {
  const empty = board.indexOf(0);
  if (empty === -1) return true;

  const nums = Array.from({ length: 9 }, (_, i) => i + 1);
  shuffleArray(nums); // Randomize for variety
  for (let num of nums) {
    if (isValidMove(empty, num, board)) {
      board[empty] = num;
      if (generateFullGrid(board)) return true;
      board[empty] = 0;
    }
  }
  return false;
}

// Helper: Shuffle an array (Fisher-Yates)
function shuffleArray(array) {
  for (let i = array.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [array[i], array[j]] = [array[j], array[i]];
  }
}

function hasUniqueSolution(board, solution) {
  let solutions = 0;
  const maxSolutions = 2;
  function countSolutions(b) {
    const empty = b.indexOf(0);
    if (empty === -1) {
      solutions++;
      return solutions > 1;
    }
    for (let num = 1; num <= 9 && solutions < maxSolutions; num++) {
      if (isValidMove(empty, num, b)) {
        b[empty] = num;
        if (countSolutions(b)) return true;
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

  if (level === 0) {
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
  } else if (level === 1) {
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
  } else if (level === 2) {
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
  } else if (level === 3) {
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
  } else if (level === 4) {
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

  return progress;
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

function solvePuzzle() {
  if (gameOver || gameWon) return;
  board = solution.slice();
  gameWon = true;
  if (timerInterval) clearInterval(timerInterval);
  updateGrid(); // Let updateGrid handle class assignment
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
    await new Promise(resolve => setTimeout(resolve, 50));
    cells[index].classList.remove('thinking-type');
  }
  console.log('Thinking animation completed');
}

function showNotesOverlay(index, cell) {
  const existingOverlay = cell.querySelector('.notes-overlay');
  if (existingOverlay) existingOverlay.remove();

  const overlay = document.createElement('div');
  overlay.classList.add('notes-overlay');
  overlay.style.display = 'grid';
  overlay.style.position = 'absolute';
  overlay.style.top = '0';
  overlay.style.left = '0';
  overlay.style.width = '100%';
  overlay.style.height = '100%';
  overlay.style.background = 'rgba(224, 224, 224, 0.9)'; // Inline for JS (matches $notes-overlay-background)
  overlay.style.zIndex = '10';

  function updateOverlay() {
    overlay.innerHTML = '';
    for (let num = 1; num <= 9; num++) {
      const option = document.createElement('div');
      option.classList.add('note-option');
      option.textContent = num;
      if (notes[index].has(num)) option.classList.add('selected');
      option.addEventListener('click', (e) => {
        e.preventDefault();
        console.log('Note overlay clicked, toggling:', num, 'at index:', index);
        toggleNote(index, num);
        updateOverlay();
        updateGrid();
      });
      overlay.appendChild(option);
    }
  }

  updateOverlay();
  cell.appendChild(overlay);
  console.log('Notes overlay appended to cell:', cell);
  console.log('Cell innerHTML after append:', cell.innerHTML);
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
  // Stop the timer if running
  if (timerInterval) {
    clearInterval(timerInterval);
    timerInterval = null;
  }

  // Reset game state
  board = initialBoard.slice(); // Restore initial puzzle
  notes = Array(81).fill().map(() => new Set()); // Clear notes
  mistakeCount = 0;
  secondsElapsed = 0;

  // Update UI
  updateGrid();
  updateNumberStatusGrid();
  document.getElementById('mistake-counter').innerHTML = ''; // Clear dots
  document.getElementById('timer').textContent = '0:00'; // Reset timer
  if (window.innerWidth <= 991) {
    document.getElementById('mistake-counter-mobile').innerHTML = ''; // Mobile reset
    document.getElementById('timer-mobile').textContent = '0:00';
  }
}

// Count the total number of solutions for a given puzzle
function countSolutions(board) {
  let solutions = 0;
  const maxSolutions = 2; // Stop after 2
  function solveAll(b) {
    if (solutions >= maxSolutions) return;
    const empty = b.indexOf(0);
    if (empty === -1) {
      solutions++;
      return;
    }
    for (let num = 1; num <= 9 && solutions < maxSolutions; num++) {
      if (isValidMove(empty, num, b)) {
        b[empty] = num;
        solveAll(b);
        b[empty] = 0;
      }
    }
  }
  const temp = board.slice();
  solveAll(temp);
  return solutions;
}

function updateDifficultyDisplay() {
  const difficultyText = document.getElementById('current-difficulty');
  if (difficultyText) {
    const difficulty = difficultySelect.value;
    difficultyText.textContent = difficulty.charAt(0).toUpperCase() + difficulty.slice(1).replace('-', ' ');
  }
}

document.getElementById('difficulty').addEventListener('change', updateDifficultyDisplay);
document.addEventListener('DOMContentLoaded', () => {
  updateDifficultyDisplay(); // Initial set
});

document.addEventListener('DOMContentLoaded', () => {
  console.log('DOM loaded, initializing Sudoku');
  if (!grid || !difficultySelect || !mistakeCounter || !numberStatusGrid || !document.getElementById('timer') || !document.getElementById('auto-candidates')) {
    return console.error('Required elements missing');
  }
  initializeGame();
  inputMode = 'guess'; // Set default mode
  updateNumberStatusGrid();

  // Desktop listeners
  document.getElementById('start-game').addEventListener('click', newGame);
  document.getElementById('difficulty').addEventListener('change', () => {
    const overlay = document.querySelector('.start-overlay');
    if (overlay) showStartOverlay();
    updateDifficultyDisplay();
  });
  document.getElementById('auto-candidates').addEventListener('click', (e) => {
    isAutoCandidatesEnabled = e.target.checked;
    updateGrid();
  });
  document.getElementById('solve-puzzle').addEventListener('click', solvePuzzle);
  document.getElementById('check-solutions').addEventListener('click', () => {
    const numSolutions = countSolutions(board);
    alert(`This puzzle has ${numSolutions} solution${numSolutions === 1 ? '' : 's'}.`);
  });

  // Mobile listeners
  document.getElementById('start-game-mobile').addEventListener('click', newGame);
  document.getElementById('difficulty-mobile').addEventListener('change', () => {
    const overlay = document.querySelector('.start-overlay');
    if (overlay) showStartOverlay();
    updateDifficultyDisplay();
  });
  document.getElementById('auto-candidates-mobile').addEventListener('click', (e) => {
    isAutoCandidatesEnabled = e.target.checked;
    updateGrid();
  });
  document.getElementById('solve-puzzle-mobile').addEventListener('click', solvePuzzle);
  document.getElementById('check-solutions-mobile').addEventListener('click', () => {
    const numSolutions = countSolutions(board);
    alert(`This puzzle has ${numSolutions} solution${numSolutions === 1 ? '' : 's'}.`);
  });

  // Sync mobile and desktop difficulty
  const syncDifficulty = () => {
    const desktopDiff = document.getElementById('difficulty');
    const mobileDiff = document.getElementById('difficulty-mobile');
    desktopDiff.value = mobileDiff.value = difficultySelect.value;
  };
  document.getElementById('difficulty').addEventListener('change', syncDifficulty);
  document.getElementById('difficulty-mobile').addEventListener('change', syncDifficulty);

  // Grid click handler
  grid.addEventListener('click', (e) => {
    const cell = e.target.closest('.cell');
    console.log('Grid click detected, target:', e.target, 'cell:', cell);
    if (cell) {
      const index = parseInt(cell.dataset.index);
      console.log('Calling editCell for index:', index);
      editCell(index, e);
    } else {
      console.log('No cell found for click');
    }
  });

  // Outside click to clear selection
  document.addEventListener('click', (e) => {
    const controls = document.getElementById('sudoku-controls');
    if (!grid.contains(e.target) && selectedCell && !controls.contains(e.target)) {
      console.log('Outside click detected, clearing selection');
      selectedCell = null;
      updateGrid();
    }
  });

  // Resize handler
  window.addEventListener('resize', () => {
    createNumberStatusGrid(); // Rebuild number status grid on resize
    if (window.innerWidth > 991 && controls) controls.classList.remove('visible');
  });

  // Update difficulty display initially
  updateDifficultyDisplay();
});

function debounce(func, wait) {
  let timeout;
  return (...args) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => func(...args), wait);
  };
}
const debouncedUpdateGrid = debounce(updateGrid, 50);