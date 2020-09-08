<?php
namespace App\Formatters;

class TimeLeft
{
    public function format(int $seconds, bool $showSeconds = false, bool $showMinute = false, bool $roundMinute = true): string
    {
        $days = floor($seconds / (60 * 60 * 24));

        $seconds -= $days * 60 * 60 * 24;

        $hours = floor($seconds / (60 * 60));
        $seconds -= $hours * 60 * 60;

        $minutes = floor($seconds / 60);
        $seconds -= $minutes * 60;

        if ($roundMinute && $minutes > 0) {
            $hours++;
        }

        $timeLeft = [];
        if ($days) {
            $timeLeft[] = $days . 'd';
        }

        if ($hours) {
            $timeLeft[] = $hours . 'h';
        }

        if ($minutes && $showMinute) {
            $timeLeft[] = $minutes . 'm';
        }

        if ($seconds && $showSeconds) {
            $timeLeft[] = $seconds . 's';
        }

        return implode(' ', $timeLeft);
    }
}
