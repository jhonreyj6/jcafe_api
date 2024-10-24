<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Storage;
use Validator;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $games = Game::orderBy('created_at', 'desc')->paginate(10);

        return response()->json($games, 200);
    }

    public function search(Request $request)
    {
        $games = Game::where('name', 'LIKE', '%' . $request->input('query') . '%')->get();

        return response()->json($games, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:games,name',
            'genre' => 'string|required',
            'trailer_link' => 'url|required',
            'image' => 'file|mimes:png,jpg|required',
            'rating' => 'integer|required|min:1|max:5',
            'description' => 'string|max:500|required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->messages()->get('*')], 500);
        }

        Storage::disk('local')->putFileAs('/public/games/images', $request->file('image'), $request->file('image')->hashName(), 'public');

        $temp = Game::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'genre' => $request->input('genre'),
            'trailer_link' => $request->input('trailer_link'),
            'image' => $request->file('image')->hashName(),
            'rating' => 4,
        ]);

        $game = Game::whereId($temp->id)->firstOrFail();
        $game->image_url = Storage::disk('local')->url('/public/games/images/' . $game->image);

        return response()->json($game, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Game $game)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $temp_game_data = Game::whereId($request->input('id'))->firstOrFail();

        $validator = Validator::make($request->all(), [
            'name' => 'string|nullable',
            'genre' => 'string|nullable',
            'trailer_link' => 'url|nullable',
            'image' => 'mimes:png,jpg|nullable',
            'rating' => 'integer|min:1|max:5|nullable',
            'description' => 'string|max:200|required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->messages()->get('*')], 500);
        }

        if ($request->hasFile('image')) {
            Storage::disk('local')->delete('/public/games/images/' . $temp_game_data->image);
            Storage::disk('local')->putFileAs('/public/games/images', $request->file('image'), $request->file('image')->hashName(), 'public');
        }

        Game::whereId($request->input('id'))->update([
            'name' => $request->input('name') ? $request->input('name') : $temp_game_data->name,
            'genre' => $request->input('genre') ? $request->input('genre') : $temp_game_data->genre,
            'trailer_link' => $request->input('trailer_link') ? $request->input('trailer_link') : $temp_game_data->trailer_link,
            'image' => $request->hasFile('image') ? $request->file('image')->hashName() : $temp_game_data->image,
            'rating' => $request->input('rating') ? $request->input('rating') : $temp_game_data->rating,
            'description' => $request->input('description') ? $request->input('description') : $temp_game_data->description,

        ]);

        $game = Game::whereId($request->input('id'))->first();

        return response()->json($game, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $games = Game::whereIn('id', $request->input('id'))->get();

        foreach ($games as $game) {
            Storage::disk('local')->delete('/public/games/images/' . $game->image);
            $game->delete();
        }

        return response()->json(['message' => 'ok'], 200);
    }
}
