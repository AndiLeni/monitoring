<?php

class Pushover
{

    public static function sendMessage(string $name, string $domain): bool
    {
        $pushover_user_key = rex_config::get('monitoring', 'pushover_user_key');
        $pushover_api_key = rex_config::get('monitoring', 'pushover_api_key');

        try {
            $socket = rex_socket::factoryUrl("https://api.pushover.net/1/messages.json");
            $response = $socket->doPost([
                'token' => $pushover_api_key,
                'user' => $pushover_user_key,
                'device' => 'Redaxo - Monitoring',
                'title' => "${name} is down",
                'message' => "Website ${name} (${domain}) is down at " . date('d.m.Y H:i:s'),
                'html' => 0,

            ]);
            return true;
        } catch (rex_socket_exception $e) {
            rex_logger::logException($e);
            return false;
        }
    }
}
