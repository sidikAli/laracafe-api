<?php

namespace App\Http\Controllers;

use App\Food;
use Illuminate\Http\Request;
use App\Http\Resources\FoodResource;
use Illuminate\Support\Facades\Validator;
Use File;

class FoodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $foods = Food::all();
        
        if ($request->has('q')) {
            $foods = Food::where('name', 'like', '%'. $request->q .'%')->get();
        } 

        return FoodResource::collection($foods);
        // return response()->json(['foods' => $foods], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //validate data
        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            'price'     => 'required|integer',
            'image'     => 'required|image|max:2048'
        ]);

        if($validator->fails()) {

            return response()->json($validator->errors(), 400);
 
        } else {
            $image      = $request->file('image');
            $fileName   = time() . uniqid() . '.' .$image->getClientOriginalExtension();

            $food = Food::create([
                'name'   => $request->name,
                'price'  => $request->price,
                'image'  => $fileName,
                'is_ready' => true,
            ]);

            $image->move('img/foods', $fileName);
            return response()->json($food, 201);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Food  $food
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $food = Food::find($id);
        if ($food) {
            return response()->json($food, 200);
        } else {
            return response()->json(['message' => 'Data not found'], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Food  $food
     * @return \Illuminate\Http\Response
     */
    public function edit(Food $food)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Food  $food
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $food = Food::find($id);
        
        if ($food) {

        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            'price'     => 'required|integer',
            'image'     => 'required|image|max:2048'
        ]);

        if($validator->fails()) {

            return response()->json($validator->errors(), 400);
 
        } else {

            $data = [
                'name'   => $request->name,
                'price'  => $request->price
            ];

            if ($request->hasFile('image')) {
                $image      = $request->file('image');
                $fileName   = time() . uniqid() . '.' .$image->getClientOriginalExtension();
                $image->move('img/foods', $fileName);
                $data['image'] = $fileName;

                //delete image
                File::delete('img/foods/' . $food->image);
            }

            $food->update($data);

            return response()->json($food, 200);
        }

        } else {
            return response()->json(['message' => 'Data not found'], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Food  $food
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $food = Food::find($id);
        if ($food) {
            File::delete('img/foods/' . $food->image);
            $food->delete();
            return response()->json(['message' => 'Data deleted successfully'], 204);
        } else {
            return response()->json(['message' => 'Data not found'], 404);
        }
    }
}
