<?php

$root = __DIR__ . '/';

$socket = stream_socket_server(
    'tcp://127.0.0.1:1965',
    $ec,
    $em,
    STREAM_SERVER_BIND|STREAM_SERVER_LISTEN,
    stream_context_create(
        [
            'ssl' => [
                'local_cert' => $root . 'cert.pem',
                'local_pk' => $root . 'key.pem',
                'passphrase' => 'gemini',
                'allow_self_signed' => true,
                'verify_peer' => false,
            ]
        ]
    )
);

stream_socket_enable_crypto($socket, false);

while (true) {
    $fSocket = stream_socket_accept($socket, '-1', $remote);

    stream_set_blocking($fSocket, true);
    stream_socket_enable_crypto($fSocket, true, STREAM_CRYPTO_METHOD_TLSv1_3_SERVER);
    $url = parse_url(trim(fread($fSocket, 1024)));
    stream_set_blocking($fSocket, false);

    $file = $root . 'capsule' . str_replace('../', '', ($url['path'] ?? '/index.gmi'));

    fwrite($fSocket, is_file($file) ? "20 text/gemini\r\n" . file_get_contents($file) : "51 Not found\r\n");
    fclose($fSocket);
}
