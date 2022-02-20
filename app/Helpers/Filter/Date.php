<?php

namespace App\Helpers\Filter;

use App\Models\Post;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class Date extends Filter
{
    public $final;
    public function handle($posts, Request $request)
    {
        if($request->has('date')){
            $datetime = new Carbon($request->date.' 00:00:00');
            $posts = $posts->whereDate('created_at', $datetime);
        }
        $this->final = $posts->get();
        $this->next($posts, $request);
    }
}
