<?php

namespace App\Application\Middleware;
use App\Auth\JwtAuth;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\ResponseFactory;


final class JwtMiddleware implements MiddlewareInterface
{
    /**
     * @var JwtAuth
     */
    private $jwtAuth;

    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    public function __construct()
    {
        $issuer = 'www.example.com';
        $lifetime = 14400;
        $privateKey = '-----BEGIN RSA PRIVATE KEY-----
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
-----END RSA PRIVATE KEY-----';
        $publicKey = '-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCh20fIFIhyn25u3P1Tzqq+pULj
W6dtA+3vGZ7jsL3zPVvYD6fT4W7+cyDDr68fVJmwLWTpsZgDeTFQNwLXM9R6NniE
lk+J34Fc8ATAObSfDQBJTrhbSoHLdDFX08QuC4PHL1rEp8+WXYArP/r1wq4NLdDZ
tbcOPZb9gQ/8+2ui4wIDAQAB
-----END PUBLIC KEY-----';

        $this->jwtAuth = new JwtAuth($issuer, $lifetime, $privateKey, $publicKey);
        $this->responseFactory = new ResponseFactory();
    }

    /**
     * Invoke middleware.
     *
     * @param ServerRequestInterface $request The request
     * @param RequestHandlerInterface $handler The handler
     *
     * @return ResponseInterface The response
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $authorization = explode(' ', (string)$request->getHeaderLine('Authorization'));
        $token = $authorization[1] ?? '';

        if (!$token || !$this->jwtAuth->validateToken($token)) {
            return $this->responseFactory->createResponse()
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401, 'Unauthorized');
        }

        // Append valid token
        $parsedToken = $this->jwtAuth->createParsedToken($token);
        $request = $request->withAttribute('token', $parsedToken);

        // Append the user id as request attribute
        $request = $request->withAttribute('uid', $parsedToken->getClaim('uid'));

        return $handler->handle($request);
    }
}