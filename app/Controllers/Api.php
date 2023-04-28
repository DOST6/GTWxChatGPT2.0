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
        ['categoryTitle'=>"Songs", 'noun'=>"famous song"],
        ['categoryTitle'=>"Sports", 'noun'=>"sport"],
        ['categoryTitle'=>"Foods", 'noun'=>"food"],
        ['categoryTitle'=>"Colors", 'noun'=>"color"],
        ['categoryTitle'=>"Brands", 'noun'=>"brand"],
        ['categoryTitle'=>"Famous Scientists", 'noun'=>"scientist"],
        ['categoryTitle'=>"Musical Instruments", 'noun'=>"musical instrument"],
        ['categoryTitle'=>"Mythological creatures", 'noun'=>"mythological creature"],
        ['categoryTitle'=>"Emotions", 'noun'=>"emotion"],
        ['categoryTitle'=>"Occupations", 'noun'=>"occupation"],
        ['categoryTitle'=>"Planets", 'noun'=>"planet"],
        ['categoryTitle'=>"Hobbies", 'noun'=>"hobby"],
        ['categoryTitle'=>"Singer", 'noun'=>"famous singer"],
        ['categoryTitle'=>"Fictional Characters", 'noun'=>"fictional character"]
    ];

    public function index() { //Garry

        if($this->request->getMethod() == 'post') {
            $post_data = $this->request->getPost();
            if($post_data['name'] != "") {
                $session_data = [
                    'started' => TRUE,
                    'player_name' => $post_data['name'],
                    'category' => array(),
                    'secret_word' => "",
                    'clues' => array(),
                    'guessed' => FALSE,
                    'num_attempts' => 0,
                    'num_games_played' => 0,
                    'num_wins' => 0
                ];
                session()->set($session_data);
            }
        }

        if(session()->get("started") == true) {
            $data = $this->get_game_stats();
            $data['player_name'] = session()->get("player_name");
            $data['started'] = TRUE;
        } else {
            $data = [
                'started' => FALSE,
                'player_name' => "Player",
                'category' => array(),
                'secret_word' => "",
                'clues' => array(),
                'guessed' => FALSE,
                'num_attempts' => 0,
                'num_games_played' => 0,
                'num_wins' => 0
            ];
        }
        $data['next_round'] = false;
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
        $session_data = [
            'started' => FALSE,
            'player_name' => "Player",
            'category' => array(),
            'secret_word' => "",
            'clues' => array(),
            'guessed' => FALSE,
            'num_attempts' => 0,
            'num_games_played' => 0,
            'num_wins' => 0
        ];
        session()->set($session_data);
        return redirect()->to("/");
    }

    protected function get_game_stats() { //Aldwin

    }

    protected function request_word($category) { //Pao

    }
    
    protected function request_clues($word) { //Hannah

    }

    private function chatGPT($prompt) { // Garry
        
        $OPENAI_API_KEY = getenv('OPENAI_API_KEY');

        $client = \Config\Services::curlrequest();

        $apiURL = "https://api.openai.com/v1/completions";
        
        $headerData = array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$OPENAI_API_KEY,
         );

        $postData = array(
            'model' => "text-davinci-003",
            'prompt' => $prompt,
            'max_tokens' => 2048, //default is 16
            'temperature' => $temperature
         );

        // Send request
        $response = $client->post($apiURL,[
            'debug' => true,
            'verify' => true, //set to false for testing purposes on local machine only
            'headers'=>$headerData,
            'json' => $postData
         ]);

         // Read response
        $code = $response->getStatusCode();
        $reason = $response->getReason();
    
        if($code == 200){ // Success
    
            // Read data 
            //$body = json_decode($response->getBody());
            $response_obj = json_decode($response->getBody());
            //var_dump($response_obj); die();
            $choices_arr = $response_obj->choices;
            $choices_obj = $choices_arr[0];
            //return $choices_obj->message->content;  
            return $choices_obj->text;  
        }else{
           echo "failed";
           die;
        }

    }
}