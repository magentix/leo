<?php

declare(strict_types=1);

error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

const ROOT = __DIR__ . '/';

$resource = stream_context_create(
    [
        'ssl' => [
            'local_cert' => ROOT . 'cert.pem',
            'local_pk' => ROOT . 'key.pem',
            'allow_self_signed' => true,
            'verify_peer' => false,
        ]
    ]
);
$socket = stream_socket_server(address: 'tlsv1.3://0:1965', context: $resource);

while (true) {
    if (!($fSocket = stream_socket_accept($socket, -1))) {
        continue;
    }
    fwrite($fSocket, getContent(parse_url(trim(fread($fSocket, 1024) ?: '')) ?: []));
    fclose($fSocket);
}

/**
 * Retrieve the response content
 *
 * @param string[] $url
 * $url = [
 *     'scheme' => (string) scheme name (gemini)
 *     'host'   => (string) host name (gemini.circumlunar.space)
 *     'path'   => (string) requested page (/about.gmi)
 * ]
 *
 * @return string
 */
function getContent(array $url): string
{
    if (($url['scheme'] ?? '') !== 'gemini') {
        return "59 bad request\r\n";
    }

    $path = $url['path'] ?? '/';
    if (str_ends_with($path, '/')) {
        $path .= 'index.gmi';
    }
    $file = ROOT . 'capsule' . str_replace('../', '', $path);

    if (!str_ends_with($file, 'gmi') || !file_exists($file)) {
        return "51 Not found\r\n";
    }

    return "20 text/gemini\r\n" . file_get_contents($file);
}
