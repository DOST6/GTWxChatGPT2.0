<?php

namespace App\Controllers;

class Api extends BaseController
{
    protected $word_category = array();
    protected $secret_word;
    protected $word_clues = array();

    protected $categories = [
        ['categoryTitle' => "Animals", 'noun' => "animal"],
        ['categoryTitle' => "Fruits", 'noun' => "fruit"],
        ['categoryTitle' => "Countries", 'noun' => "country"],
        ['categoryTitle' => "Movies", 'noun' => "movie"],
        ['categoryTitle' => "TV Shows", 'noun' => "tv show"],
        ['categoryTitle' => "Celebrities", 'noun' => "celebrity"],
        ['categoryTitle' => "Historical figures", 'noun' => "historical figure"],
        ['categoryTitle' => "Sports", 'noun' => "sport"],
        ['categoryTitle' => "Foods", 'noun' => "food"],
        ['categoryTitle' => "Colors", 'noun' => "color"],
        ['categoryTitle' => "Brands", 'noun' => "brand"],
        ['categoryTitle' => "Famous landmarks", 'noun' => "famous landmark"],
        ['categoryTitle' => "Musical instruments", 'noun' => "musical instrument"],
        ['categoryTitle' => "Mythological creatures", 'noun' => "mythological creature"],
        ['categoryTitle' => "Emotions", 'noun' => "emotion"],
        ['categoryTitle' => "Occupations", 'noun' => "occupation"],
        ['categoryTitle' => "Vehicles", 'noun' => "vehicle"],
        ['categoryTitle' => "Hobbies", 'noun' => "hobby"],
        ['categoryTitle' => "Famous paintings", 'noun' => "famous painting"],
        ['categoryTitle' => "Fictional characters", 'noun' => "fictional character"]
    ];

    public function index()
    { //Garry

      

    }

    public function get_category()
    { //Jean
        //$data = array();
        if($this->request->getMethod() == 'post') {
            $num_games_played = session()->get("num_games_played");

            $category = $this->categories[rand(0,19)];

            session()->set(['num_games_played'=>($num_games_played+1)]);
            session()->set(['category'=>$category]);

            /* $secret_word = "";
            while(strlen($secret_word) <= 1) {
                $secret_word = $this->request_word($category['noun']);
            }
            session()->set(['secret_word'=>$secret_word]); */

            //$clues_arr = $this->request_clues($secret_word);
            //print_r($clues_arr); die();
            //session()->set(['clues'=>$clues_arr]);

            $data = $this->get_game_stats();
            $data['categoryTitle'] = $category['categoryTitle'];
            $data['next_round'] = false;
            //print_r($data); die();
            return $this->response->setJSON($data);
            //return $this->response->setJSON($category);
        }
    }

    public function initialize_clues()
    { //Jean
        if($this->request->getMethod() == 'post') {
            $secret_word = session()->get("secret_word");
            if($secret_word != "") {
                $clues_arr = $this->request_clues(session()->get("secret_word"));
                session()->set(['clues'=>$clues_arr]);
                return $this->response->setJSON($clues_arr);
            }
        }
    }

    public function get_clue()
    { //Joseph

    }

    public function check_answer()
    { //Pao

    }

    public function reset()
    { //Hannah

    }

    public function end_game()
    { //Aldwin

    }

    protected function get_game_stats()
    { //Aldwin
       
    }

    protected function request_word($category)
    { //Pao

    }

    protected function request_clues($word)
    { //Hannah

    }

    private function chatGPT($prompt)
    { // Garry

    }
}