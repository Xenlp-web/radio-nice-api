<?php


namespace App\Services;

use Illuminate\Support\Facades\Http;

class RadioService
{
    protected $radioUrl = 'https://radio-nice.ru:8080';

    public function checkServerAvailability($serverId) {
        $response = Http::get($this->radioUrl . '/api/v2/channels/?server=' . $serverId);
        return count($response->json()) > 0;
    }

    public function getStreamUrl($serverId, $user) {
        $channels = Http::get($this->radioUrl . '/api/v2/channels/?server=' . $serverId)->json();

        if ($this->checkServerAvailability($serverId)) {
            $neededBitrate = 'min';
            if ($user != null && $user->premium) $neededBitrate = 'max';

            $channelIndex = $this->getMinOrMaxBitrateChannelIndex($channels, $neededBitrate);

            if ($channelIndex !== false) return $channels[$channelIndex]['secure_stream_url'];
        }

        return false;
    }

    public function getCurrentTrack($serverId) {
        if ($this->checkServerAvailability($serverId)) {
            return Http::get($this->radioUrl . '/api/v2/history/?server=' . $serverId . '&limit=1')->json()['results'][0];
        }
        return false;
    }

    public function getLastTracks($serverId, $limit) {
        if ($this->checkServerAvailability($serverId)) {
            return Http::get($this->radioUrl . '/api/v2/history/?server=' . $serverId . '&limit=' . $limit)->json()['results'];
        }
        return false;
    }

    public function voteUp($trackId) {
        return Http::post($this->radioUrl . "/api/v2/music/{$trackId}/like/")->json();
    }

    public function voteDown($trackId) {
        return Http::post($this->radioUrl . "/api/v2/music/{$trackId}/dislike/")->json();
    }



    private function getMinOrMaxBitrateChannelIndex($channels, $neededBitrate = 'min') {
        $bitrates = [];

        foreach ($channels as $channel) {
            if ($channel['active']) {
                $bitrates[] = (int)$channel['bitrate'];
            }
        }

        if (count($bitrates) < 1) return false;

        $bitrate = min($bitrates);

        if ($neededBitrate != 'min') $bitrate = max($bitrates);

        return array_search($bitrate, array_column($channels, 'bitrate'));
    }
}
