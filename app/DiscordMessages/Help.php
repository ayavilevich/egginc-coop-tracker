<?php
namespace App\DiscordMessages;

class Help extends Base
{
    public function message(): string
    {
        return <<<HELP
```
eb!help - Displays list of commands
eb!contracts - Display current contracts with IDs
eb!status {Contract ID} - Display coop info for contract
eb!s {Contract ID} - Short version of status
eb!add {Contract ID} {Coop} {?Coop} - Add coop to tracking, multiple can be added by this command. When multiple is added, the position of the coops is set.
eb!delete {contractID} {Coop} - Remove coop from tracking

eb!set-player-id {@Discord Name} {Egg Inc Player ID}
```
HELP;
    }    
}
