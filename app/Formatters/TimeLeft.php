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
            $timeLeft[] = $days . ' day' . ($days > 1 ? 's' : '');
        }

        if ($hours) {
            $timeLeft[] = $hours . ' hour' . ($hours > 1 ? 's' : '');
        }

        if ($minutes && $showMinute) {
            $timeLeft[] = $minutes . ' minute' . ($minutes > 1 ? 's' : '');
        }

        if ($seconds && $showSeconds) {
            $timeLeft[] = $seconds . ' second' . ($seconds > 1 ? 's' : '');
        }

        return implode(' ', $timeLeft);
    }
}
