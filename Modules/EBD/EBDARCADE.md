---
name: EBD Arcade Games Upgrade
overview: Complete overhaul of the EBD Arcade system to make all 7 existing games 100% mobile responsive, create 8 new professional biblical games with rich data, and upgrade the game center UI to a premium professional standard.
todos:
  - id: fix-memory
    content: Fix Memory game - responsive grid (3-4 cols), touch support, expand to 24 pairs with difficulty levels
    status: completed
  - id: fix-quiz
    content: Fix Quiz game - larger touch buttons, add 200+ questions with categories via seeder
    status: completed
  - id: fix-versemaster
    content: Fix VerseMaster - improve touch handling, larger drop zones, mobile-optimized verses
    status: completed
  - id: fix-hangman
    content: Fix Hangman - responsive keyboard, scalable word boxes, expand to 100+ words
    status: completed
  - id: fix-timeline
    content: Fix Timeline - implement custom touch reordering, expand to 10 levels/30+ events
    status: completed
  - id: fix-sword
    content: Fix Sword game - mobile aspect ratio, touch-friendly targets, 7 book categories
    status: completed
  - id: fix-whosaidit
    content: Fix Who Said It - expand to 80+ quotes, add character portraits and hints
    status: completed
  - id: create-wordsearch
    content: Create Word Search game - responsive grid, touch selection, 50 word lists
    status: completed
  - id: create-crossword
    content: Create Crossword game - zoomable grid, tap-to-type, 20 puzzles
    status: completed
  - id: create-hero
    content: Create Hero of Faith game - character guessing with clues, 100 characters
    status: completed
  - id: create-parables
    content: Create Parables Match game - drag-drop matching, 40 parables
    status: completed
  - id: create-fillblank
    content: Create Fill the Blank game - verse completion using Bible module
    status: completed
  - id: create-bookchallenge
    content: Create Book Challenge game - categorize/order 66 Bible books
    status: completed
  - id: create-trio
    content: Create Biblical Trio game - match 3 related items, 30+ trios
    status: completed
  - id: create-navigator
    content: Create Bible Navigator game - speed verse finding using Bible module
    status: completed
  - id: upgrade-hub
    content: Upgrade Games Hub index - premium UI, categories, stats, daily challenges
    status: completed
  - id: expand-seeder
    content: Create comprehensive GameDataSeeder with all game content
    status: in_progress
  - id: update-controller
    content: Add routes and controller methods for 8 new games
    status: completed
isProject: false
---

# EBD Arcade Games - Complete Overhaul Plan

## Current State Analysis

The arcade currently has 7 games, but most have significant issues:

- **Responsiveness**: Most games break on mobile screens (fixed grids, hardcoded widths, desktop-only drag-drop)
- **Limited Content**: Most games have only 5-12 hardcoded items
- **No Touch Support**: Drag-and-drop games fail on mobile devices
- **Basic UI**: Missing premium polish, sound feedback, achievements

---

## Phase 1: Fix All 7 Existing Games (100% Responsive)

### 1.1 Memory Game (`memory.blade.php`)

- Change grid from fixed `grid-cols-4` to responsive `grid-cols-3 sm:grid-cols-4`
- Add touch event handlers for card flipping
- Expand card pairs from 6 to 24 (biblical characters, objects, places, events)
- Add difficulty levels: Easy (6 pairs), Medium (12 pairs), Hard (18 pairs)

### 1.2 Quiz Game (`quiz.blade.php`)

- Already mostly responsive, but needs touch-friendly button sizes
- Expand question database from 10 to 200+ questions via seeder
- Add categories: Old Testament, New Testament, Characters, Geography, Prophecy

### 1.3 VerseMaster (`versemaster.blade.php`)

- Touch support exists but needs improvement
- Make drop zones larger for mobile fingers (min-height: 60px)
- Add responsive word bank with flexible wrapping
- Optimize verse selection for shorter mobile-friendly verses

### 1.4 Hangman (`hangman.blade.php`)

- Fix keyboard grid from `grid-cols-7 md:grid-cols-10` to fully responsive
- Make word display boxes scale with screen size using `min-w-8 md:min-w-14`
- Expand word database from 6 to 100+ words across categories
- Add visual hangman SVG animation

### 1.5 Timeline (`timeline.blade.php`)

- Replace HTML5 drag-drop with touch-compatible Sortable.js pattern
- Implement custom touch handlers for mobile reordering
- Expand from 3 levels to 10 levels with 30+ events
- Add visual timeline connector line

### 1.6 Sword Game (`sword.blade.php`)

- Fix game area aspect ratio for mobile screens
- Make falling items touch-friendly with larger tap targets
- Expand categories from 3 to 7 (all Bible book divisions)
- Add progressive difficulty (speed increases over time)

### 1.7 Who Said It (`who-said-it.blade.php`)

- Already mostly responsive
- Expand quotes from 5 to 80+ famous biblical quotes
- Add hint system with verse reference reveal
- Add portrait icons for biblical characters

---

## Phase 2: Create 8 New Professional Games

### 2.1 Caca-Palavras Biblico (Word Search)

- **Type**: Find hidden biblical words in a letter grid
- **Mobile**: Responsive grid that scales, touch-to-drag selection
- **Data**: 50+ word lists by category (Fruits of Spirit, 12 Tribes, Apostles, etc.)
- **Features**: Timer, hints, multiple difficulty levels

### 2.2 Palavras Cruzadas (Crossword)

- **Type**: Biblical crossword puzzles
- **Mobile**: Responsive grid with zoom capability, tap-to-type
- **Data**: 20+ pre-built puzzles from easy to hard
- **Features**: Hints, reveal letter, save progress

### 2.3 Heroi da Fe (Hero of Faith)

- **Type**: Guess the biblical character from progressive clues
- **Mobile**: Card-based UI, swipe gestures
- **Data**: 100+ characters with 5 clues each
- **Features**: Streak system, XP bonus for fewer clues

### 2.4 Parabolas em Acao (Parables Match)

- **Type**: Match parables to their meanings/teachings
- **Mobile**: Drag-drop with touch support
- **Data**: 40+ parables with explanations
- **Features**: Multiple match types, educational mode

### 2.5 Complete o Versiculo (Fill the Blank)

- **Type**: Fill missing words in famous verses
- **Mobile**: Word bank with tap-to-fill
- **Data**: Uses Bible module verses with smart word selection
- **Features**: Multiple blanks per verse, progressive difficulty

### 2.6 Desafio dos Livros (Book Challenge)

- **Type**: Categorize and order Bible books
- **Mobile**: Drag-drop sorting with touch support
- **Data**: All 66 books with categories, order, and facts
- **Features**: Speed mode, category mode, facts mode

### 2.7 Trio Biblico (Biblical Trio)

- **Type**: Match 3 related biblical items (like memory but with triplets)
- **Mobile**: Card grid with touch flip
- **Data**: 30+ trios (Father/Son/Spirit, 3 Wise Men, etc.)
- **Features**: Time attack, combo multipliers

### 2.8 Navegador Biblico (Bible Navigator)

- **Type**: Speed-find verses by reference
- **Mobile**: Quick-tap navigation UI
- **Data**: Uses Bible module for live verse lookup
- **Features**: Timed challenges, accuracy scoring

---

## Phase 3: Upgrade Game Center Hub

### 3.1 New Premium Index Layout

- Hero section with animated background
- Category tabs: Quiz, Memory, Puzzle, Word, Speed
- Game cards with preview screenshots
- Progress indicators per game
- Daily challenge highlight card

### 3.2 Statistics Dashboard

- Total games played
- Favorite games chart
- Weekly/monthly progress
- Achievement showcase

### 3.3 Achievement System

- 30+ achievements (First Win, 100 Games, Perfect Score, etc.)
- Badge display integration with existing gamification
- Achievement unlock notifications

---

## Phase 4: Database & Seeder Updates

### 4.1 Expand GameSeeder.php

- Add 200+ quiz questions across 6 categories
- Add 100+ hangman words
- Add 80+ "Who Said It" quotes
- Add 30+ timeline levels
- Add all new game registrations

### 4.2 New Data Seeders

- `WordSearchSeeder.php` - 50 word lists
- `CrosswordSeeder.php` - 20 puzzles
- `BiblicalHeroSeeder.php` - 100 characters with clues
- `ParablesSeeder.php` - 40 parables with meanings
- `BibleBooksSeeder.php` - 66 books with metadata

---

## Technical Implementation Details

### Mobile Responsiveness Patterns

```css
/* Touch-friendly minimum sizes */
.game-btn {
  min-height: 48px;
  min-width: 48px;
}
.drop-zone {
  min-height: 60px;
}
.game-card {
  touch-action: manipulation;
}
```

### Touch Event Pattern for All Drag-Drop Games

```javascript
// Unified touch handler
handleTouchStart(e, item) { /* capture touch */ }
handleTouchMove(e) { /* update visual clone position */ }
handleTouchEnd(e) { /* find drop target, execute drop */ }
```

### Key Files to Create/Modify

**Modify (7 files):**

- `Modules/EBD/resources/views/memberpanel/arcade/memory.blade.php`
- `Modules/EBD/resources/views/memberpanel/arcade/quiz.blade.php`
- `Modules/EBD/resources/views/memberpanel/arcade/versemaster.blade.php`
- `Modules/EBD/resources/views/memberpanel/arcade/hangman.blade.php`
- `Modules/EBD/resources/views/memberpanel/arcade/timeline.blade.php`
- `Modules/EBD/resources/views/memberpanel/arcade/sword.blade.php`
- `Modules/EBD/resources/views/memberpanel/arcade/who-said-it.blade.php`

**Create (8 new game views):**

- `Modules/EBD/resources/views/memberpanel/arcade/wordsearch.blade.php`
- `Modules/EBD/resources/views/memberpanel/arcade/crossword.blade.php`
- `Modules/EBD/resources/views/memberpanel/arcade/hero.blade.php`
- `Modules/EBD/resources/views/memberpanel/arcade/parables.blade.php`
- `Modules/EBD/resources/views/memberpanel/arcade/fillblank.blade.php`
- `Modules/EBD/resources/views/memberpanel/arcade/bookchallenge.blade.php`
- `Modules/EBD/resources/views/memberpanel/arcade/trio.blade.php`
- `Modules/EBD/resources/views/memberpanel/arcade/navigator.blade.php`

**Modify (Controller + Routes):**

- `Modules/EBD/app/Http/Controllers/MemberPanel/ArcadeController.php`
- `routes/member.php`

**Create/Modify (Seeders):**

- `Modules/EBD/database/seeders/GameSeeder.php` (expand)
- `Modules/EBD/database/seeders/GameDataSeeder.php` (new comprehensive)

---

## Integration Points

- **Bible Module**: VerseMaster, Fill the Blank, Navigator use `Modules\Bible\App\Models\Verse`
- **Gamification**: All games submit scores via `ArcadeController::submitScore()` and award XP
- **Leaderboard**: All games contribute to unified ranking system
- **User Dashboard**: Display favorite games, recent activity, achievements
