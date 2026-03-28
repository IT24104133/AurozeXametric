<?php

namespace App\Helpers;

/**
 * ✅ Formats question and option text:
 * - Normalizes line breaks (\r\n, \r to \n)
 * - If no existing newlines but has A./B./C. patterns, inserts line breaks
 * - Converts newlines to HTML <br> tags
 * - Escapes HTML for XSS safety (only <br> is kept)
 * - Trims whitespace
 * 
 * Safe for use with {!! ... !!} in Blade
 * Tamil text and all Unicode is preserved
 */
class QuestionFormatter
{
    public static function format(string $text): string
    {
        $text = (string) $text;
        
        // Normalize line endings
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        
        // If there are no newlines but has A./B./C. patterns, add line breaks
        if (strpos($text, "\n") === false) {
            $text = preg_replace('/\s([A-E])\.\s/u', "\n$1. ", $text);
        }
        
        // Escape HTML entities, then convert newlines to <br> tags
        $text = nl2br(e(trim($text)));
        
        return $text;
    }
}
