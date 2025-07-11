<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoomFormRequest;
use App\Http\Requests\UpdateRoomFormRequest;
use App\Models\Room;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RoomController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('permission:rooms.index', only: ['index']),
            new Middleware('permission:rooms.show', only: ['show']),
            new Middleware('permission:rooms.create', only: ['create', 'store']),
            new Middleware('permission:rooms.edit', only: ['edit', 'update']),
            new Middleware('permission:rooms.destroy', only: ['destroy']),
        ];        
    }
        
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rooms = Room::all();
        if ($rooms->isEmpty()) {
            return redirect()->route('rooms.create')->with('msg', 'No hay salas creadas aun, por favor crea una.');
        }

        return view('rooms.index', compact('rooms'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Room $room)
    {
        return view('rooms.show', compact('room'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('rooms.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoomFormRequest $request)
    {
        Room::create($request->all());

        return redirect()->route('rooms.index')->with('msg', 'Sala creada satisfactoriamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Room $room)
    {
        return view('rooms.edit', compact('room'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoomFormRequest $request, Room $room)
    {
        $room->update($request->all());

        return redirect()->route('rooms.index')->with('msg', 'Sala actualizada satisfactoriamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room)
    {
        $room->delete();
        return redirect()->route('rooms.index')->with('msg', 'Sala eliminada satisfactoriamente.');
    }

}
