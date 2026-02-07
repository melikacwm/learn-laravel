<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NoteController extends Controller
{
    public function index(Request $request){
        //variabel query untuk search
        $q = $request->string('q')->toString();

        //variable untuk menyimpan list notes
        $notes = Note::query()
            ->when($q, function ($query) use ($q) {
                //untuk filter title
                $query->where('title', 'like', "%{$q}%");
            })
            ->latest()
            //bikin pagination
            ->paginate(10)
            //memastikan q ikut kebawa saat pindah halaman pagination
            ->withQueryString();
        //method untuk render tampilan pada Vue (nyambung sama bagian resources.js/Pages/Notes/Index)    
        return Inertia::render('Notes/Index', [
            'notes' => $notes,
            'filters' => [
                'q' => $q,
            ],
        ]);
    }
    /*
    //Method tanpa pagination
    public function index()
    {
        return Inertia::render('Notes/Index', [
            'notes' => Note::latest()->get(['id', 'title', 'created_at']),
        ]);
    }
        */

    //method untuk create
    public function store(Request $request)
    {
        //mendefinisikan data dari Note
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255']
        ]);

        Note::create($validated);

        return redirect()->route('notes.index');
    }

    //method untuk delete
    public function destroy(Note $note){
        //jalankan dulu dhapusnya
        $note->delete();
        //terus balik lagi ke halaman index
        return redirect()->route('notes.index');
    }

    // untuk update Note
    public function update(Request $request, Note $note){
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $note->update($validated);

        return redirect()->route('notes.index');
    }
}
