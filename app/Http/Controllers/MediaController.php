<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|image|max:5120',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = Str::uuid().'.'.$file->getClientOriginalExtension();

            $path = $file->storeAs('temp', $fileName, 'public');

            return response()->json([
                'url' => Storage::url($path),
                'path' => $path,
            ]);
        }
    }
}
