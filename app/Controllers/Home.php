<?php

namespace App\Controllers;

use App\Models\Post\Post;

class Home extends BaseController
{
    public function index(): string
    {
        $posts= new Post();

        $data = $posts->select('*')->get(2)->getResult();
        $data1 = $posts->select('*')->get(1)->getResult();
        $dataTwoPosts = $posts->select('*')->orderBy('id','DESC')->get(2)->getResult();

        //Business Section
        $businessTwoPosts = $posts->select('*')->where('category','business')
        ->orderBy('id','DESC')->get(2)->getResult();
        $businessThreePosts = $posts->select('*')->where('category','business')
        ->orderBy('title','DESC')->get(3)->getResult();
        $dataFourPosts = $posts->select('*')->orderBy('id','DESC')->get(4)->getResult();

        //Culture Section
        $cultureTwoPosts = $posts->select('*')->where('category','culture')
        ->orderBy('id','DESC')->get(2)->getResult();
        $cultureThreePosts = $posts->select('*')->where('category','culture')
        ->orderBy('title','DESC')->get(3)->getResult();

        //Politics Section
        $politicsThreePosts = $posts->select('*')->where('category','politics')
        ->orderBy('title','DESC')->get(3)->getResult();

        //Travel Section
        $travel = $posts->select('*')->where('category','travel')->get(1)->getResult();
        $travelTwoPosts = $posts->select('*')->where('category','travel')->get(2)->getResult();

        return view('home', compact('data','data1','dataTwoPosts','businessTwoPosts','businessThreePosts'
        ,'dataFourPosts','cultureTwoPosts','cultureThreePosts','politicsThreePosts','travel','travelTwoPosts'));
    }
    public function aboutUs(){

        return view('pages/about-us');
    }

    public function contact(){
        
        return view('pages/contact');
    }
}
