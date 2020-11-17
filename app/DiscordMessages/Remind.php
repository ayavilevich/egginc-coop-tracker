<?php
namespace App\DiscordMessages;

class Remind extends Base
{
    public function message(): string
    {
        $channel = $request->input('channel.id');
        $contract = Arr::get($parts, 1);
        $hours = Arr::get($parts, 2);
        $minutes = Arr::get($parts, 3);

        return '`' . json_encode($request->input('channel')) . '`';
    }
}
