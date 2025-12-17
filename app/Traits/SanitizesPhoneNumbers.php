<?php

namespace App\Traits;

trait SanitizesPhoneNumbers
{
    /**
     * Sanitize phone number for use in channel names
     */
    public function sanitizePhoneForChannel($phoneNumber)
    {
        if (! $phoneNumber) {
            return 'unknown';
        }

        // Remove all non-alphanumeric characters
        $sanitized = preg_replace('/[^a-zA-Z0-9]/', '', $phoneNumber);

        // Ensure it's not empty after sanitization
        return $sanitized ?: 'unknown';
    }

    /**
     * Get original phone number from sanitized version (for display)
     */
    public function getOriginalPhoneNumber($sanitizedPhone, $originalNumbers = [])
    {
        foreach ($originalNumbers as $original) {
            if ($this->sanitizePhoneForChannel($original) === $sanitizedPhone) {
                return $original;
            }
        }

        return $sanitizedPhone;
    }
}
