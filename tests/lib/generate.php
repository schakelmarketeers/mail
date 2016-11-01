<?php

use Schakel\Mail\MailUtils;

$dir = __DIR__ . '/';

$source = $dir . '/mail.html';
$destHtml = $dir . '/mail-emo.html';
$destPlain = $dir . '/mail-plain.txt';

require_once __DIR__ . '/../bootstrap.php';

if (!file_exists($source)) {
    echo "Source file not found!\n";
    exit(1);
}

$sourceText = file_get_contents($source);

$bodyHtml = MailUtils::createEmailHtml($sourceText);
$bodyPlain = MailUtils::createEmailPlain($sourceText);

file_put_contents($destHtml, $bodyHtml);
file_put_contents($destPlain, $bodyPlain);

echo "Done!\n";
exit(0);
