<?php
define(
    "cellLocation",
    [
        "top left" => [0, 0],
        "top middle" => [0, 1],
        "top right" => [0, 2],
        "middle left" => [1, 0],
        "middle" => [1, 1],
        "middle right" => [1, 2],
        "bottom left" => [2, 0],
        "bottom middle" => [2, 1],
        "bottom right" => [2, 2]
    ],
);

// returns tic tac toe grid
function newBoard() {
    return [
        [null, null, null],
        [null, null, null],
        [null, null, null],
    ];
}

function renderBoard($board) {
    foreach ($board as $row) {
        echo "|";
        foreach ($row as $cell) {
            echo ($cell ?? " ") . "|";
            echo " ";
        }
        echo "\n      \n";
    }
}

function makeMove($board, $move, $player) {
    $board[cellLocation[$move][0]][cellLocation[$move][1]] = $player;
    return $board;
}

function isValidMove($board, $move) {
    return array_key_exists($move, cellLocation) && is_null(
        $board[cellLocation[$move][0]][cellLocation[$move][1]]
    );
}

function findPossibleMoves($board) {
    $possibleMoves = [];
    foreach (array_values($board) as $i => $row) {
        foreach (array_values($row) as $j => $cell) {
            if (is_null($cell)) {
                array_push($possibleMoves, array_search([$i, $j], cellLocation));
            }
        }
    }
    return $possibleMoves;
}

function displayWelcomeMessage() {
    echo "\nHello! Welcome to Tic Tac Toe. To specify which position you want 
to enter an 'X' or 'O' into, please specify one of the following 
options:

top left    | top middle    | top right
middle left | middle middle | middle right
bottom left | bottom middle | bottom right

Enjoy!\n\n";
}

function displayWinnerMessage($winner, $nb) {
    echo "CONGRATS PLAYER $winner ! YOU'VE WON ＼(＾O＾)／ 🎉\n\n";
    renderBoard($nb);
}

function displayDrawMessage($nb) {
    echo "INTENSE MATCH 🔥 ! IT WAS A DRAW ( ဖ‿ဖ)人(စ‿စ )\n\n";
    renderBoard($nb);
}

function determineWinner($board, $player) {
    for($i = 0; $i < 3; $i++) {
        if (
            (// check coloumns
                $board[0][$i] === $player &&
                $board[1][$i] === $player &&
                $board[2][$i] === $player
            ) || (// check rows
                $board[$i][0] === $player &&
                $board[$i][1] === $player &&
                $board[$i][2] === $player
            )
        ) { return $player; }
    }
    if (
        (// check diagonal
            $board[0][0] === $player &&
            $board[1][1] === $player &&
            $board[2][2] === $player
        ) || (// check anti diagonal
            $board[0][2] === $player &&
            $board[1][1] === $player &&
            $board[2][0] === $player
        )
    ) { return $player; }
    // no winner
    return null;
}

function boardFull($board) {
    foreach ($board as $row) {
        foreach ($row as $cell) {
            if (is_null($cell)) {
                return false;
            }
        }
    }
    return true;
}

function getMiniMaxScore($board, $movingPlayer, $playerToHelp) {
    $winner = determineWinner($board, $movingPlayer);
    if (!is_null($winner)) {
        return $winner === $playerToHelp ? 10 : -10;
    } elseif (boardFull($board)) {
        return 0;
    }
    $possibleMoves = findPossibleMoves($board);
    $cellScores = [];
    foreach ($possibleMoves as $move) {
        $possibleBoard = makeMove($board, $move, $movingPlayer);
        $opponentMiniMaxScore = getMiniMaxScore($possibleBoard, $movingPlayer === "X" ? "O" : "X", $playerToHelp);
        array_push($cellScores, $opponentMiniMaxScore);
    }
    return $movingPlayer === $playerToHelp ? max($cellScores) : min($cellScores);
}

function miniMaxAlgo($board, $player) {
    $targetMove = null;
    $maxScore = null;
    foreach (findPossibleMoves($board) as $move) {
        $possibleBoard = makeMove($board, $move, $player);
        $miniMaxScore = getMiniMaxScore($possibleBoard, $player === "X" ? "O" : "X", $player);
        if (is_null($maxScore) || $miniMaxScore > $maxScore) {
            $maxScore = $miniMaxScore;
            $targetMove = $move;
        }
    }
    return $targetMove;
}

function executeUserTurn($gameBoard, $player) {
    while (true) {
        $move = miniMaxAlgo($gameBoard, $player);
        if (isValidMove($gameBoard, $move)) {
            echo "PLAYER $player CHOSE: $move\n";
            echo "Putting '$player' at [" . implode(", ", cellLocation[$move]) . "]\n\n";
            return makeMove($gameBoard, $move, $player);
        } else {
            echo "\nThere is already something at this location or this location doesn't exist. Please try again.\n\n";
        }
    }
}

function startGame() {
    displayWelcomeMessage();
    // starting player
    $player = mt_rand(0, 1) === 0 ? "X" : "O";
    $nb = newBoard();
    while (true) {
        renderBoard($nb);
        echo "Player $player's turn...\n\n";
        $nb = executeUserTurn($nb, $player);
        // check if this player won
        $winner = determineWinner($nb, $player);
        if ($winner) {
            displayWinnerMessage($winner, $nb);
            break;
        }
        // end game if draw
        if (boardFull($nb)) {
            displayDrawMessage($nb);
            break;
        }
        // alernate player turns
        $player = $player === "X" ? "O" : "X";
    }
}

startGame();
?>