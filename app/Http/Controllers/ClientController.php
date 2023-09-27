<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $builder = Client::orderBy('created_at', 'desc');
        return response($builder->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientRequest $request)
    {
        $client = new Client($request->all());
        $client->save();
        return response($client, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        return response($client);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClientRequest $request, Client $client)
    {
        $update = array_filter($request->all(), function ($value, $key) use ($client) {
            return $value != $client->$key;
        }, ARRAY_FILTER_USE_BOTH);
        if (count($update)) {
            $client->update($update);
        }
        return response($client);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        $client->delete();
        return response([], 204);
    }
}
