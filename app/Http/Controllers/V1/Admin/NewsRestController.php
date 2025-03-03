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
     *     path="/api/v1/news-event",
     *     summary="Get news Event list",
     *     tags={"News Event"},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of items to get",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="offset",
     *         in="query",
     *         description="Number of items to skip",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="News Title")
     *             ))
     *         )
     *     ),
     *     @OA\Response(response=404, description="No news found")
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
                'id', 'rank', 'status', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by',
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
