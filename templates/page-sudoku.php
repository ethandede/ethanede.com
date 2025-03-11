<?php
/**
 * Template Name: Sudoku
 * Template Post Type: page
 * Description: Template for Ethan Ede's Sudoku page, styled to match the site
 */
get_header();
?>

<main id="sudoku" class="sudoku-page">
  <section class="sudoku-container">
    <div class="container">
      <h2>Sudoku Know!</h2>
      <p class="supporting-text">A little puzzle-solving fun built with JavaScript—test your skills with only 4 mistakes allowed!</p>
      <div class="sudoku-layout">
        <!-- Status Bar -->
        <div class="status-bar">
          <span id="timer" class="timer">0:00</span>
          <h3 id="current-difficulty" class="current-difficulty">Easy</h3>
          <span id="mistake-counter" class="mistake-counter"></span>
        </div>
        
        <!-- Grid -->
        <div id="sudoku-grid" class="sudoku-grid"></div>
        
        <!-- Controls -->
        <div class="controls-column">
          <!-- Number Status Grid with Mode Toggle -->
          <div id="number-status-grid" class="number-status-grid"></div>
          
          <!-- Game Controls -->
          <div class="game-controls">
            <button class="cta-button start-game" id="start-game">Start Game</button>
            <div class="control-row setup-row">
              <select id="difficulty" class="sudoku-select">
                <option value="quick">Quick</option>
                <option value="easy">Easy</option>
                <option value="not easy">Not Easy</option>
                <option value="hard">Hard</option>
                <option value="expert">Expert</option>
                <option value="mental">Mental</option>
              </select>
              <button class="cta-button" onclick="newGame()">New Game</button>
            </div>
            <div class="control-row toggle-row">
              <label for="auto-candidates">Auto-Candidates:</label>
              <input type="checkbox" id="auto-candidates" class="auto-candidates-checkbox">
            </div>
            <div class="control-row action-row">
              <button class="cta-button" id="solve-puzzle">Solve Puzzle</button>
              <button class="cta-button" id="check-solutions">Check Solutions</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<?php get_footer(); ?>