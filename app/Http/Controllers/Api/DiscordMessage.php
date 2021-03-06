<?php

namespace App\Http\Controllers\Api;

use App\DiscordMessages\Add;
use App\DiscordMessages\Contracts;
use App\DiscordMessages\Delete;
use App\DiscordMessages\Help;
use App\DiscordMessages\Hi;
use App\DiscordMessages\Love;
use App\DiscordMessages\Players;
use App\DiscordMessages\Rank;
use App\DiscordMessages\Remind;
use App\DiscordMessages\SetPlayerId;
use App\DiscordMessages\ShortStatus;
use App\DiscordMessages\Status;
use App\Exceptions\DiscordErrorException;
use App\Http\Controllers\Controller;
use Arr;
use Illuminate\Http\Request;

class DiscordMessage extends Controller
{
    private $validCommands = [
        'help'          => ['class' => Help::class],
        'status'        => ['class' => Status::class],
        's'             => ['class' => ShortStatus::class],
        'contracts'     => ['class' => Contracts::class],
        'love'          => ['class' => Love::class],
        'hi'            => ['class' => Hi::class],
        'add'           => ['class' => Add::class],
        'delete'        => ['class' => Delete::class],
        'set-player-id' => ['class' => SetPlayerId::class],
        'players'       => ['class' => Players::class],
        'rank'          => ['class' => Rank::class],
        'remind'        => ['class' => Remind::class],
    ];

    private $guildId;

    public function receive(Request $request): array
    {
        $message = trim(str_replace($request->input('atBotUser'), '', $request->input('content')));
        $parts = explode(' ', $message);
        $command = Arr::get($parts, '0');
        
        try {
            $commandInfo = Arr::get($this->validCommands, $command);

            if (!$commandInfo) {
                throw new DiscordErrorException('Invalid command: ' . $command);
            }

            $class = $commandInfo['class'];
            $object = new $class(
                $request->input('author.id'),
                $request->input('author.username', ''),
                $request->input('channel.guild.id'),
                $request->input('channel.id'),
                $parts
            );
            return ['message' => $object->message()];
        } catch (DiscordErrorException $e) {
            return ['message' => $e->getMessage()];
        }
    }
}
