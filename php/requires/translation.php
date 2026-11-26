<?php

// Columns of message_resource. Whitelist: the column name is built into SQL.
const TRANSLATION_LANGUAGES = [
    'en' => 'English',
    'zh' => '中文',
    'my' => 'Bahasa Melayu',
    'ne' => 'नेपाली',
];

function loadTranslations($db, $lang)
{
    global $translations;
    $translations = array();

    if (!array_key_exists($lang, TRANSLATION_LANGUAGES)) {
        $lang = 'en';
    }

    $result = $db->query("SELECT message_key_code, en, `$lang` AS msg FROM message_resource WHERE status = '0'");
    if (!$result) {
        return $translations;
    }

    while ($row = $result->fetch_assoc()) {
        $text = trim((string) $row['msg']);
        if ($text === '') {
            $text = $row['en'];
        }
        $translations[$row['message_key_code']] = $text;
    }
    $result->free();

    return $translations;
}

// HTML-escaped text for a key; a missing key prints the key itself so it is easy to spot.
function t($key)
{
    global $translations;

    if (isset($translations[$key])) {
        return htmlspecialchars($translations[$key], ENT_QUOTES, 'UTF-8');
    }

    return htmlspecialchars($key, ENT_QUOTES, 'UTF-8');
}
