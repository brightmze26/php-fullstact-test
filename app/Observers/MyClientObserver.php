<?php

namespace App\Observers;

use App\Models\MyClient;

class MyClientObserver
{
    private function redis(): \Illuminate\Redis\Connections\Connection
    {
        return \Illuminate\Support\Facades\Redis::connection();
    }

    private function key(string $slug): string
    {
        return trim($slug);
    }

    private function payload(MyClient $client): string
    {
        $fresh = $client->fresh();
        return $fresh ? $fresh->toJson() : $client->toJson();
    }

    public function created(MyClient $client): void
    {
        $this->redis()->set($this->key($client->slug), $this->payload($client));
    }

    public function updated(MyClient $client): void
    {
        $oldSlug = trim((string)$client->getOriginal('slug'));
        $newSlug = trim((string)$client->slug);

        if ($oldSlug !== '' && $oldSlug !== $newSlug) {
            $this->redis()->del($this->key($oldSlug));
        }

        $this->redis()->set($this->key($newSlug), $this->payload($client));
    }

    public function deleted(MyClient $client): void
    {
        $this->redis()->del($this->key($client->slug));
    }
}