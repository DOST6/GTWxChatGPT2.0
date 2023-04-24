<?php

namespace App\Controllers;

class Api extends BaseController
{
    protected $word_category = array();
    protected $secret_word;
    protected $word_clues = array();
    
    protected $categories = [
        ['categoryTitle'=>"Animals", 'noun'=>"animal"],
        ['categoryTitle'=>"Fruits", 'noun'=>"fruit"],
        ['categoryTitle'=>"Countries", 'noun'=>"country"],
        ['categoryTitle'=>"Movies", 'noun'=>"movie"],
        ['categoryTitle'=>"TV Shows", 'noun'=>"tv show"],
        ['categoryTitle'=>"Celebrities", 'noun'=>"celebrity"],
        ['categoryTitle'=>"Historical figures", 'noun'=>"historical figure"],
        ['categoryTitle'=>"Sports", 'noun'=>"sport"],
        ['categoryTitle'=>"Foods", 'noun'=>"food"],
        ['categoryTitle'=>"Colors", 'noun'=>"color"],
        ['categoryTitle'=>"Brands", 'noun'=>"brand"],
        ['categoryTitle'=>"Famous landmarks", 'noun'=>"famous landmark"],
        ['categoryTitle'=>"Musical instruments", 'noun'=>"musical instrument"],
        ['categoryTitle'=>"Mythological creatures", 'noun'=>"mythological creature"],
        ['categoryTitle'=>"Emotions", 'noun'=>"emotion"],
        ['categoryTitle'=>"Occupations", 'noun'=>"occupation"],
        ['categoryTitle'=>"Vehicles", 'noun'=>"vehicle"],
        ['categoryTitle'=>"Hobbies", 'noun'=>"hobby"],
        ['categoryTitle'=>"Famous paintings", 'noun'=>"famous painting"],
        ['categoryTitle'=>"Fictional characters", 'noun'=>"fictional character"]
    ];

    public function index() { //Garry

        $data = array();
        
        return view('app_view', $data);

    }

    public function get_category() { //Jean

    }

    public function initialize_clues() { //Jean

    }

    public function get_clue() { //Joseph

    }

    public function check_answer() { //Pao

    }

    public function reset() { //Hannah

    }

    public function end_game() { //Aldwin
    
    }

    protected function get_game_stats() { //Aldwin

    }

    protected function request_word($category) { //Pao

    }
    
    protected function request_clues($word) { //Hannah

    }

    private function chatGPT($prompt) { // Garry

    }
}