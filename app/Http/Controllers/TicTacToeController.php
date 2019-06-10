<?php

namespace App\Http\Controllers;

use App\Game;
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
        $game->playerX()->attach($player);
        $game->playerO()->attach($computer);
        $game->save();

        Session::put('tic-tac-toe-game-uid', $game->uid);

        return response()->json(
            [
                'state' => 'new',
                'gameUid' => $game->uid
            ]);
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
        $board = $request->input('board');
        $newmove = $request->input('rowcell');

        $cords = explode('-', $newmove);

        $board[$cords[0]][$cords[1]] = 'X';


        return response()->json($this->makeaMove($board));
    }

    private function makeaMove($board)
    {
        $decided = false;


        //check for any O
        foreach ($board as $cord => $value)
        {

            if ($value === 'O')
            {
                $this->findBestOption($board, $cord);
            }
        }

        if (!$decided)
        {
            $row = random_int(1, 3);
            $cell = random_int(1, 3);
        }


        $move = "$row-$cell";
        $board[$row][$cell] = 'O';

        if($this->checkForBingo($board)) {
            return [
                'state' => 'winner',
                'board' => $board,
                'move' => $move
            ];
        }

        return [
            'state' => 'inprogress',
            'board' => $board,
            'move' => $move
        ];
    }

    private function findBestOption($board, $current)
    {
        //dump($board);
        //dump($current);
    }

    private function checkForBingo($board)
    {
        $bingo = false;

        dump($board);

        foreach ($board as $row) {
            //if 1 = 2 and 1 = 3 then 2 most also = 3
            if ($row[1] !== null and $row[1] === $row[2] and $row[1] === $row[3]) {
                $bingo = true;
                break;
            }
        }

        return $bingo;
    }


}
