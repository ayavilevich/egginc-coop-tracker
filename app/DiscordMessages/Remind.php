<?php
namespace App\DiscordMessages;

use App\Jobs\RemindCoopStatus;
use Arr;

class Remind extends Base
{
    public function message(): string
    {
        $parts = $this->parts;
        $contract = Arr::get($parts, 1);
        $hours = (int) Arr::get($parts, 2);
        $minutes = (int) Arr::get($parts, 3);

        for ($i = $minutes; $i <= ($hours * 60); $i += $minutes) {
            RemindCoopStatus::dispatch(
                $this->authorId,
                $this->guildId,
                $this->channelId,
                $contract
            )->delay(now()->addMinutes($i));
        }

        return 'Reminders set.';
    }
}
