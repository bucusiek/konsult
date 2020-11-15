<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {
    // Global Settings Object
    $containerBuilder->addDefinitions([
        'settings' => [
            'displayErrorDetails' => true, // Should be set to false in production
            'logger' => [
                'name' => 'slim-app',
                'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                'level' => Logger::DEBUG,
            ],
            'jwt' => [
                'issuer' => 'www.example.com',

                // Max lifetime in seconds
                'lifetime' => 14400,

                // The private key
                'private_key' => '-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQCh20fIFIhyn25u3P1Tzqq+pULjW6dtA+3vGZ7jsL3zPVvYD6fT
4W7+cyDDr68fVJmwLWTpsZgDeTFQNwLXM9R6NniElk+J34Fc8ATAObSfDQBJTrhb
SoHLdDFX08QuC4PHL1rEp8+WXYArP/r1wq4NLdDZtbcOPZb9gQ/8+2ui4wIDAQAB
AoGAOPz/OihYnpsaA/jVTUPQBI4lje3AdnbSuMP5mMurJdCt3NYuTkDqlrasi5n4
+/wKnOhuxoWcM2Thgw/LdUAviEN6uHjJUkVmKo5+pBP3tSezWaAsTWXnQaD6f8EQ
6xbzsbOkFG0IO7tLLKAd8dPfVPdQyTDGYT6xJGIhXK4DxkkCQQDrQWBqZxDYnCGv
XtRerhYjginUkMsVgAGtS5Jk0PGjuZ1Drvgr3VLVmoH2DBrgRuNKvtt1dGCrEYji
qAKirozHAkEAsCECQDDRWfbiBnIJx/fM3vg+s6J7ObyqQWvNzaStAKM/umo991F6
Mo+l/ZZNH73KFLVwcvdK1SkEcRPuD6UFBQJAdDJ8XtG9Xl/vu2EJYCJ4SN2Xr6g8
xsfNDD1Rd35Ee+vII5Aef/v3WA3StybPd4tL5LVUTDVJMfWdOOZnNtckLQJAY2FM
ttGU3xFp+b8Q+887vzgNkSiGJU7qNl3Q008u+uQiSlo2Or2zmKHrREoxnE5nnwW9
vHECvYIWaoOXWSaAzQJBAI2uhmyF8IqEojpVU/q5gajFEdRlA61Y/pCn6zw/M8I8
YcGHm4VT6XKP1PYjX0a6GEpYNF0H+/vuCoJrkzocKck=
-----END RSA PRIVATE KEY-----',
                'public_key' => '-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCh20fIFIhyn25u3P1Tzqq+pULj
W6dtA+3vGZ7jsL3zPVvYD6fT4W7+cyDDr68fVJmwLWTpsZgDeTFQNwLXM9R6NniE
lk+J34Fc8ATAObSfDQBJTrhbSoHLdDFX08QuC4PHL1rEp8+WXYArP/r1wq4NLdDZ
tbcOPZb9gQ/8+2ui4wIDAQAB
-----END PUBLIC KEY-----',
            ],
        ],
    ]);
};
