<?php

namespace App\Http\Controllers;

use App\Game;
use App\Move;
use App\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

class TicTacToeController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function getHighScores()
    {
        return response()->json(Player::orderBy('wins', 'DESC')->get());
    }

    public function checkForCurrentGame()
    {
        return response()->json([
            'game-uid' => Session::get('tic-tac-toe-game-uid')
        ]);
    }

    public function checkForCurrentPlayer()
    {
        $player = Player::where('uid', '=', Cookie::get('tic-tac-toe-player-uid'))->first();
        return response()->json(
            [
                'player' => $player
            ]);
    }

    public function newGame(Request $request)
    {
        $player = Player::where('uid', '=', $request->input('uid'))->first();
        $computer= Player::where('name', '=', 'Computer')->first();

        $game = new Game();
        $game->board = $this->createNewBoard();
        $game->playerX()->associate($player);
        $game->playerO()->associate($computer);
        $game->save();

        Session::put('tic-tac-toe-game-uid', $game->uid);

        return response()->json(
            [
                'state' => 'new',
                'gameUid' => $game->uid,
                'players' => ['X' => $player, 'O' => $computer]
            ]);
    }

    public function clearCurrentPlayer()
    {
        return response()->json(['message' => 'ok'])->withCookie(Cookie::forget('tic-tac-toe-player-uid'));
    }

    public function newPlayer(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:players|max:255'
        ]);

        $player = Player::firstOrCreate(['name' => $request->input('name')]);

        return response()->json(
            [
                'player' => $player
            ])
            ->cookie('tic-tac-toe-player-uid', $player->uid, 60*14*365);
    }

    private function createNewBoard()
    {
        $board = [];
        $row = 1;
        while ($row <= 3)
        {
            $cell = 1;
            while ($cell <= 3) {
                $board[$row][$cell] = null;
                $cell++;
            }
            $row++;
        }

        return $board;
    }

    public function setPlayerMove(Request $request)
    {
        $winner = null;
        $returnMove = null;
        $game = Game::where('uid', '=', $request->input('gameuid'))->firstOrFail();
        $board = $game->board;
        $newmove = $request->input('rowcell');

        $move = new Move();
        $move->move = $newmove;
        $move->player()->associate($game->playerX);
        $move->save();
        $game->moves()->attach($move->id);

        $cords = explode('-', $newmove);

        $board[$cords[0]][$cords[1]] = 'X';
        $game->board = $board;
        $game->save();

        if($this->checkForBingo($board)) {
            $human = $game->playerX;
            $human->increment('wins');
            $human->save();

            $state = 'winner';
            $winner = 'X';
        }
        else if ($computerMove = $this->computerMove($board)) {
            $game->board = $computerMove['board'];

            $move = new Move();
            $move->move = $computerMove['move'];
            $move->player()->associate($game->playerO);
            $move->save();
            $game->moves()->attach($move->id);
            $game->save();

            $returnMove = $computerMove['move'];

            if($this->checkForBingo($computerMove['board'])) {
                $computer = $game->playerO;
                $computer->increment('wins');
                $computer->save();

                $state = 'winner';
                $winner = 'O';
            }
            else {
                $state = 'inprogress';
            }
        }
        else {
            $state = 'draw';
        }

        return response()->json([
            'state' => $state,
            'move' => $returnMove,
            'winner' => $winner
        ]);
    }

    private function computerMove($board)
    {
        $tries = 0;

        while ($tries < 27) {
            $tries++;
            $row = random_int(1, 3);
            $cell = random_int(1, 3);

            if (empty($board[$row][$cell]))
            {
                $move = "$row-$cell";
                $board[$row][$cell] = 'O';
                return ['move' => $move, 'board' => $board];
            }
        }

        return false;
    }
    private function checkForBingo($board)
    {
        $bingo = false;
        $i = 1;

        while ($i <= 3) {
            if ($board[$i][1] !== null and $board[$i][1] === $board[$i][2] and $board[$i][1] === $board[$i][3]) {
                $bingo = true;
                break;
            }
            if ($board[1][$i] !== null and $board[1][$i] === $board[2][$i] and $board[1][$i] === $board[3][$i]) {
                $bingo = true;
                break;
            }
            if ($board[1][1] !== null and $board[1][1] === $board[2][2] and $board[2][2] === $board[3][3]) {
                $bingo = true;
                break;
            }
            if ($board[3][1] !== null and $board[3][1] === $board[2][2] and $board[2][2] === $board[1][3]) {
                $bingo = true;
                break;
            }

            $i++;
        }

        dump($board);

        return $bingo;
    }


}
