<?php

/**
 * Get human-readable label for a stream
 */
function streamLabel($stream): string
{
    $labels = [
        'ol' => 'O/L',
        'al' => 'A/L',
        'grade5' => 'Grade 5 Scholarship',
    ];

    return $labels[$stream] ?? $stream;
}

/**
 * Get stream color for badges
 */
function streamColor($stream): string
{
    $colors = [
        'ol' => 'blue',
        'al' => 'purple',
        'grade5' => 'green',
    ];

    return $colors[$stream] ?? 'gray';
}

/**
 * Get all valid streams
 */
function getStreams(): array
{
    return ['ol', 'al', 'grade5'];
}
