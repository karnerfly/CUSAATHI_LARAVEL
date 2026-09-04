<?php

namespace App\Http\Controllers\Newsletter;

use App\Http\Controllers\Controller;
use App\Models\Newsletter\Log;
use Dedoc\Scramble\Attributes\ExcludeAllRoutesFromDocs;
use Illuminate\Http\Request;

#[ExcludeAllRoutesFromDocs]
class NewsletterTrackingController extends Controller
{
    private const GIF = '47494638396101000100800000ffffff00000021f90401000000002c00000000010001000002024401003b';

    public function track_open(Request $request, string $log_id)
    {
        $log = Log::find($log_id);

        if ($log && is_null($log->opened_at)) {
            $log->update(['opened_at' => now()]);
        }

        $content = hex2bin(self::GIF);

        return response($content, 200, [
            'Content-Type' => 'image/gif',
            'Content-Length' => strlen($content),
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0',
            'Pragma' => 'no-cache',
            'Expires' => 'Thu, 01 Jan 1970 00:00:00 GMT',
        ]);
    }
}
