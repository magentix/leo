<?php

declare(strict_types=1);

const ROOT = __DIR__ . '/';

$resource = stream_context_create(
    [
        'ssl' => [
            'local_cert' => ROOT . 'cert.pem',
            'local_pk' => ROOT . 'key.pem',
            'passphrase' => 'gemini',
            'allow_self_signed' => true,
            'verify_peer' => false,
        ]
    ]
);
$socket = stream_socket_server(address: 'tcp://0:1965', context: $resource);
stream_socket_enable_crypto($socket, false);

while (true) {
    $fSocket = stream_socket_accept($socket, -1);

    stream_set_blocking($fSocket, true);
    @stream_socket_enable_crypto($fSocket, true, STREAM_CRYPTO_METHOD_TLSv1_3_SERVER);
    $url = parse_url(trim(fread($fSocket, 1024)));
    stream_set_blocking($fSocket, false);

    fwrite($fSocket, getContent($url ?: []));
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
        return "53 Proxy Request Refused\r\n";
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
