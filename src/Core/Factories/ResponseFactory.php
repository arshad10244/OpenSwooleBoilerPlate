<?php

namespace Shahzaib\Framework\Core\Factories;

use OpenSwoole\Core\Psr\Response;
use Psr\Http\Message\ResponseInterface;

final class ResponseFactory
{

    const NOT_FOUND_RESPONSE = 404;

    const UNAUTHORIZED_RESPONSE = 401;

    const FORBIDDEN_RESPONSE = 403;

    const INTERNAL_SERVER_ERROR_RESPONSE = 500;

    const NOT_IMPLEMENTED_RESPONSE = 501;

    const BAD_REQUEST_RESPONSE = 400;

    const UNPROCESSABLE_ENTITY_RESPONSE = 422;

    const SUCCESS_RESPONSE = 200;

   public static function createResponse(array|object $data, int $code = 200, array $headers = []): ResponseInterface
   {
       if(!key_exists('Content-Type', $headers))
           $headers['Content-Type'] = 'application/json';


       $status = match ($code) {
           self::NOT_FOUND_RESPONSE => 'Resource Not Found',
           self::UNAUTHORIZED_RESPONSE => 'Unauthorized',
           self::FORBIDDEN_RESPONSE => 'Forbidden',
           self::INTERNAL_SERVER_ERROR_RESPONSE => 'Internal Server Error',
           self::NOT_IMPLEMENTED_RESPONSE => 'Not Implemented',
           self::BAD_REQUEST_RESPONSE => 'Bad Request',
           self::UNPROCESSABLE_ENTITY_RESPONSE => 'Unprocessable Entity',
           self::SUCCESS_RESPONSE => 'Success',
           default => $code,
       };

       $data = [
           'status' => $status,
           'data' => $data
       ];

       if(DEBUG)
           $data['debug'] = [

               'memory' => [
                   'mem_usage' => round(memory_get_usage()/1000),
                   'mem_peak_usage' => round(memory_get_peak_usage()/1000)
               ],

           ];

      return new Response(json_encode($data),$code,"",$headers);
   }


}