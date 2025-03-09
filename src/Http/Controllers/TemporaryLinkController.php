<?php

namespace Shahadh\TemporaryLinks\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Shahadh\TemporaryLinks\Services\TemporaryLinkService;

class TemporaryLinkController extends Controller
{
    protected TemporaryLinkService $linkService;

    /**
     * Create a new controller instance.
     */
    public function __construct(TemporaryLinkService $linkService)
    {
        $this->linkService = $linkService;
    }

    /**
     * Access a temporary link.
     */
    public function access(Request $request, $token)
    {
        $result = $this->linkService->validateAndProcess($token, $request);

        if (!$result['valid']) {
            return response()->json([
                'error' => $result['reason']
            ], 403);
        }

        $link = $result['link'];

        // If link is associated with a model
        if ($link->linkable) {
            if (method_exists($link->linkable, 'handleTemporaryAccess')) {
                return $link->linkable->handleTemporaryAccess($link, $request);
            }

            // ✅ Return full document data instead of just ID and type
            return response()->json([
                'document' => $link->linkable,
                'accessGranted' => true
            ]);
        }

        // If link has a custom path
        if ($link->path) {
            return redirect($link->path);
        }

        // Default response
        return response()->json([
            'success' => true,
            'message' => 'Access granted'
        ]);
    }

}
