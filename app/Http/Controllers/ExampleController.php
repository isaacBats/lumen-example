<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExampleController extends Controller
{
    private $twitterService;
    private $meaningCloudService;
    private $mercadoLibreService;
    private $clarifaiService;

    /**
     *
     * @return void
     */
    public function __construct()
    {
        $this->twitterService = app()->make('TwitterService');
        $this->meaningCloudService = app()->make('MeaningCloudService');
        $this->mercadoLibreService = app()->make('MercadoLibreService');
        $this->clarifaiService = app()->make('ClarifaiService');
    }

    public function search( Request $req )
    {
        
        // Obteniendo los datos mandados por GET desde el navegador
        $query = $req->input('q');

        // Buscamos los tweets relacionados con el query
        $tweets = $this->twitterService->searchTweets( $query );

        $text = join(',', $tweets);

        // Palabras clave en los tweets
        $topics = $this->meaningCloudService->getTopicByText( $text );

        $searchQuery = join(',', $topics);
        
        // Buscando productos en mercado libre
        $items = $this->mercadoLibreService->searchItems( $searchQuery );

        return response()->json($items);
    }

    public function searchByImageUrl( Request $req )
    {
        $imageUrl = $req->input('url');

        $words = $this->clarifaiService->searchEntitiesByUrl( $imageUrl );

        $items = $this->mercadoLibreService->searchItems( join(',', $words) );
        
        return response()->json($items);

        // return response()->json( $words );
    }

}
