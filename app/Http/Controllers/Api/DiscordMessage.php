<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DiscordMessage extends Controller
{
    public function receive(Request $request): array
    {
        $message = str_replace($request->get('atBotUser') . ' ', '', $request->get('content'));
        $parts = explode(' ', $message);
        $command = $parts['0'];

        switch ($command) {
            case 'help':
                $message = $this->help();
                break;

            default:
                $message = 'Invalid command: ' . print_r($message, true);
                break;
        }

        return [
            'message' => $message,
        ];
    }

    private function help(): string
    {
        return <<<HELP
```
@EggInc-GroupStatus help Displays list of commands
```
HELP;
    }
}
