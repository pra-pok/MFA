<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Dtos\ResponseDTO;
use App\Utils;
use OpenApi\Annotations as OA;

class NewsRestController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/news/event",
     *     summary="Get list of news events",
     *     tags={"News Event"},
     *     description="Fetch a paginated list of news events. Supports pagination and offset-based fetching.",
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page (used for pagination)",
     *         required=false,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of items to retrieve (used for offset-based fetching)",
     *         required=false,
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *     @OA\Parameter(
     *         name="offset",
     *         in="query",
     *         description="Number of items to skip (used with limit)",
     *         required=false,
     *         @OA\Schema(type="integer", example=0)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example=""),
     *             @OA\Property(property="timestamp", type="string", format="date-time", example="2025-03-09T12:00:00Z"),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer", example=100),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=10),
     *                 @OA\Property(property="next_page_url", type="string", nullable=true, example="https://example.com/api/v1/news/event?page=2"),
     *                 @OA\Property(property="prev_page_url", type="string", nullable=true, example=null)
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Exciting News Event"),
     *                     @OA\Property(property="description", type="string", example="This is a detailed description of the news event."),
     *                     @OA\Property(property="thumbnail", type="string", example="https://example.com/file/news_event/image.jpg"),
     *                     @OA\Property(property="file", type="string", example="https://example.com/file/news_event_pdf/document.pdf")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="No news events found")
     * )
     */
    public function getNews(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $limit = $request->input('limit');
        $offset = $request->input('offset', 0);
        $query = NewEvent::where('status', 1)->orderBy('id', 'desc');
        if ($limit) {
            $total = $query->count();
            $news = $query->limit($limit)->offset($offset)->get();
            $meta = [
                'total' => $total,
                'per_page' => (int) $limit,
                'current_page' => (int) ceil(($offset + 1) / $limit),
                'last_page' => (int) ceil($total / $limit),
                'next_page_url' => ($offset + $limit < $total) ? url()->current() . "?limit=$limit&offset=" . ($offset + $limit) : null,
                'prev_page_url' => ($offset - $limit >= 0) ? url()->current() . "?limit=$limit&offset=" . ($offset - $limit) : null,
            ];
        } else {
            $paginatedNews = $query->paginate($perPage);
            $meta = [
                'total' => $paginatedNews->total(),
                'per_page' => $paginatedNews->perPage(),
                'current_page' => $paginatedNews->currentPage(),
                'last_page' => $paginatedNews->lastPage(),
                'next_page_url' => $paginatedNews->nextPageUrl(),
                'prev_page_url' => $paginatedNews->previousPageUrl(),
            ];
            $news = collect($paginatedNews->items());
        }
        $news->each(function ($event) {
            $event->makeHidden([
                'rank', 'status', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by',
                'meta_title', 'meta_keywords', 'meta_description',
            ]);
                $event->thumbnail = $event->thumbnail ? url('/file/news_event/' . $event->thumbnail) : '';
                $event->file = $event->file ? url('/file/news_event_pdf/' . $event->file) : '';
        });

        return response()->json([
            'data' => $news,
            'meta' => $meta,
            'message' => '',
            'status' => true,
            'timestamp' => now()->toISOString(),
        ]);
    }
}
