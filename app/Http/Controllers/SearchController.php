<?php

namespace App\Http\Controllers;

use App\Actions\Search\ExecuteCompanySearchAction;
use App\Http\Requests\SearchRequest;
use App\Models\MessageTemplate;
use App\Models\SearchHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $recentSearches = SearchHistory::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        $templates = MessageTemplate::where('user_id', $user->id)->get();

        return view('search.index', compact('recentSearches', 'templates'));
    }

    public function search(SearchRequest $request, ExecuteCompanySearchAction $action): JsonResponse
    {
        $result = $action->execute($request->user(), $request->validated());

        return response()->json($result);
    }

    public function history(Request $request): JsonResponse
    {
        $history = SearchHistory::where('user_id', $request->user()->id)
            ->latest()
            ->take(20)
            ->get();

        return response()->json($history);
    }
}
