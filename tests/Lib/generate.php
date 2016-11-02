<?php

use Schakel\Mail\MailUtils;

require_once __DIR__ . '/../bootstrap.php';

$output = [
    'html' => '-emo',
    'plain' => '-plain'
];

$files = [
    'mail-simple',
    'mail-complex'
];

foreach ($files as $file) {
    $source = sprintf('%s/%s.html', __DIR__, $file);
    $destHtml = sprintf('%s/%s-emo.html', __DIR__, $file);
    $destPlain = sprintf('%s/%s-plain.txt', __DIR__, $file);

    printf("Transforming %s... ", basename($source));

    if (!file_exists($source)) {
        echo "404 not found\n";
        continue;
    }

    $sourceText = file_get_contents($source);

    $bodyHtml = MailUtils::createEmailHtml($sourceText);
    $bodyPlain = MailUtils::createEmailPlain($sourceText);

    file_put_contents($destHtml, $bodyHtml);
    file_put_contents($destPlain, $bodyPlain);

    echo "done\n";

}
