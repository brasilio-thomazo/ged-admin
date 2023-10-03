<?php

namespace App\Http\Controllers;

use App\Models\App;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function show(Request $request, string $path): Response
    {
        if (!$request->hasValidSignature()) abort(401);
        $content = Storage::get($path);
        return response($content, 200, ['Content-Type' => 'text/plain']);
    }

    public function create(Request $request, App $app): Response
    {
        $filename = "k8s-" . $app->id . ".yaml";
        $url = Storage::temporaryUrl($filename, now()->addMinutes(1));
        $data = ['url' => $url];
        return response($data, 201);
    }
}
