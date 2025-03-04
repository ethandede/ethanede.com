// script.js
const grid = document.getElementById('sudoku-grid');
let board = [];

// Initialize the grid
function createGrid() {
    grid.innerHTML = '';
    for (let i = 0; i < 81; i++) {
        const cell = document.createElement('div');
        cell.classList.add('cell');
        cell.addEventListener('click', () => editCell(i));
        grid.appendChild(cell);
    }
}

// Generate a simple solvable board (basic example)
function generateBoard() {
    // For simplicity, this is a hardcoded valid Sudoku puzzle
    // In a real app, you'd use an algorithm to generate random puzzles
    const puzzle = [
        5,3,0,0,7,0,0,0,0,
        6,0,0,1,9,5,0,0,0,
        0,9,8,0,0,0,0,6,0,
        8,0,0,0,6,0,0,0,3,
        4,0,0,8,0,3,0,0,1,
        7,0,0,0,2,0,0,0,6,
        0,6,0,0,0,0,2,8,0,
        0,0,0,4,1,9,0,0,5,
        0,0,0,0,8,0,0,7,9
    ];
    board = puzzle.slice();
    updateGrid();
}

// Update the UI with the board values
function updateGrid() {
    const cells = document.querySelectorAll('.cell');
    cells.forEach((cell, index) => {
        cell.textContent = board[index] === 0 ? '' : board[index];
        cell.style.color = board[index] === 0 ? '#000' : '#555'; // Pre-filled numbers in gray
    });
}

// Edit a cell when clicked
function editCell(index) {
    if (board[index] !== 0 && !isEditable(index)) return; // Prevent editing pre-filled cells
    const value = prompt('Enter a number (1-9):');
    if (value && !isNaN(value) && value >= 1 && value <= 9) {
        board[index] = parseInt(value);
        if (isValidMove(index, board[index])) {
            updateGrid();
        } else {
            alert('Invalid move!');
            board[index] = 0;
            updateGrid();
        }
    }
}

// Check if a cell is editable (not pre-filled)
function isEditable(index) {
    const initialPuzzle = [
        5,3,0,0,7,0,0,0,0,
        6,0,0,1,9,5,0,0,0,
        0,9,8,0,0,0,0,6,0,
        8,0,0,0,6,0,0,0,3,
        4,0,0,8,0,3,0,0,1,
        7,0,0,0,2,0,0,0,6,
        0,6,0,0,0,0,2,8,0,
        0,0,0,4,1,9,0,0,5,
        0,0,0,0,8,0,0,7,9
    ];
    return initialPuzzle[index] === 0;
}

// Basic validation for Sudoku rules
function isValidMove(index, value) {
    const row = Math.floor(index / 9);
    const col = index % 9;

    // Check row
    for (let i = 0; i < 9; i++) {
        if (board[row * 9 + i] === value && i !== col) return false;
    }

    // Check column
    for (let i = 0; i < 9; i++) {
        if (board[i * 9 + col] === value && i !== row) return false;
    }

    // Check 3x3 box
    const startRow = Math.floor(row / 3) * 3;
    const startCol = Math.floor(col / 3) * 3;
    for (let i = 0; i < 3; i++) {
        for (let j = 0; j < 3; j++) {
            if (board[(startRow + i) * 9 + (startCol + j)] === value &&
                (startRow + i) !== row && (startCol + j) !== col) {
                return false;
            }
        }
    }
    return true;
}

// Start a new game
function newGame() {
    generateBoard();
}

// Initialize on page load
createGrid();
newGame();