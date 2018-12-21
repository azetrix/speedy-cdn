<?php

namespace Core;

class DataStream
{

    public static function ByteCounter($handle, int $control_bytes = 100, int $max_bytes = null) {
        $mb_count = 0;
        $stream = fopen('php://temp','ra+');
        while(false === feof($handle))
        {

            $mb_count += 1;
            ErrorHandler::InterruptProcess(
                $max_bytes !== null && ($mb_count*$control_bytes)+$control_bytes >= $max_bytes,
                403,
                [
                    'context' => 'remote-origin',
                    'info' => 'requested resource exceeds maximum allowed byte size ('.$max_bytes.')'
                ]
            );
            
            fwrite($stream, fread($handle, $control_bytes));
        }
        rewind($stream);
        return $stream;
    }

}