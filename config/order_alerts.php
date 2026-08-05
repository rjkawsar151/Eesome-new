<?php

// Admin emails to notify on new orders and status changes.
// Set ADMIN_EMAILS in .env as a comma-separated list, e.g.:
//   ADMIN_EMAILS=admin@example.com,manager@example.com
//
// Legacy key ADMIN_ORDER_ALERT_EMAILS is also supported for backward compatibility.

$fromNew    = (string) env('ADMIN_EMAILS', '');
$fromLegacy = (string) env('ADMIN_ORDER_ALERT_EMAILS', '');
$combined   = trim($fromNew . ',' . $fromLegacy, ',');

return [
    'emails' => array_values(
        array_filter(
            array_map('trim', explode(',', $combined))
        )
    ),
];
