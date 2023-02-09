<?php

namespace App\Http\Controllers;

use App\Exceptions\GeneralApiException;
use App\Http\Requests\NewsArticleRequest;
use App\Http\Resources\NewsArticleCollection;
use App\Http\Resources\NewsArticleRessource;
use App\Models\NewsArticle;
use App\Services\ApiService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NewsArticleController extends Controller
{
    private ApiService $apiService;

    public function __construct(ApiService $apiService) {
        $this->apiService = $apiService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new NewsArticleCollection(NewsArticle::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(NewsArticleRequest $request)
    {
        $newsArticleProperties = $request->validated();
        if ($request->input('publish')) {
            $newsArticleProperties['published_at'] = Carbon::now()->toDateTimeString();
        }
        $created = NewsArticle::create($newsArticleProperties);

        if ($created) {
            return $this->apiService->getSuccessResponse(
                "News Article with ID $created->id created.", 201
            );
        } else {
            throw new GeneralApiException();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $newsArticle = NewsArticle::find($id);
        throw_if(!$newsArticle, new GeneralApiException(
            "News Article with ID $id was not found.", 404
        ));

        return (new NewsArticleRessource($newsArticle))->setSingleRessource();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(NewsArticleRequest $request, $id)
    {
        $newsArticle = NewsArticle::find($id);
        throw_if(!$newsArticle, new GeneralApiException(
            "News Article with ID $id was not found.", 404
        ));

        $newsArticleProperties = $request->validated();
        if ($request->input('publish')) {
            $newsArticleProperties['published_at'] = Carbon::now()->toDateTimeString();
        }

        $newsArticle->update($newsArticleProperties);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $newsArticle = NewsArticle::find($id);
        $newsArticle->delete();
    }
}
