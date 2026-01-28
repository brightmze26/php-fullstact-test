<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMyClientRequest;
use App\Http\Requests\UpdateMyClientRequest;
use App\Models\MyClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MyClientController extends Controller
{
    public function index(): JsonResponse
    {
        $clients = MyClient::query()
            ->whereNull('deleted_at')
            ->orderByDesc('id')
            ->get();

        return response()->json($clients);
    }

    public function store(StoreMyClientRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data['client_logo'] = 'no-image.jpg';

        if ($request->hasFile('client_logo')) {
            $path = $request->file('client_logo')->storePublicly('client-logos', 's3');
            $data['client_logo'] = Storage::disk('s3')->url($path);
        }

        $client = MyClient::create($data);

        return response()->json($client->fresh(), 201);
    }

    public function show(MyClient $myClient): JsonResponse
    {
        return response()->json($myClient);
    }

    public function update(UpdateMyClientRequest $request, MyClient $myClient): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('client_logo')) {
            $path = $request->file('client_logo')->storePublicly('client-logos', 's3');
            $data['client_logo'] = Storage::disk('s3')->url($path);
        }

        $myClient->update($data);

        return response()->json($myClient->fresh());
    }

    public function destroy(MyClient $myClient): JsonResponse
    {
        $myClient->delete();

        return response()->json(['deleted' => true]);
    }

    public function bySlug(Request $request, string $slug): JsonResponse
    {
        $slug = trim($slug);

        $redis = \Illuminate\Support\Facades\Redis::connection();
        $cached = $redis->get($slug);

        if ($cached) {
            return response()->json(json_decode($cached, true));
        }

        $client = MyClient::query()
            ->where('slug', $slug)
            ->whereNull('deleted_at')
            ->firstOrFail();

        $redis->set($slug, $client->toJson());

        return response()->json($client);
    }
}
