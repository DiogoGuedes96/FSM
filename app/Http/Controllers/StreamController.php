<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StreamController extends Controller
{
    public function stream()
    {
        $response = new StreamedResponse(function () {
            while (true) {
                echo "data: " . json_encode(['message' => date('Y-m-d H:i:s')]) . "\n\n";
                ob_flush();
                flush();
                sleep(1);
            }
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');

        return $response;
    }
}


// const eventSource = new EventSource('http://bms.dvl.to/api/sse');

// eventSource.onmessage = function (event) {
//     const data = JSON.parse(event.data);
//     console.log(data.message); // Handle the received data as needed
// };

// eventSource.onerror = function (error) {
//     console.error('Error:', error);
// };
