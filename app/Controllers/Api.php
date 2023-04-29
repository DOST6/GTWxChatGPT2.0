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

    public function index()
    { //Garry

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

    public function initialize_word() {
        if($this->request->getMethod() == 'post') {
            $category = session()->get("category");
            if($category != "" ){
                $secret_word = "";
                while(strlen($secret_word) <= 1) {
                    $secret_word = $this->request_word($category['noun']);
                }
                session()->set(['secret_word'=>$secret_word]);
                //return $this->response->setJSON(['secret_word'=>$secret_word]);
                return $this->response->setJSON(['info'=>"Secret word set."]);
            }
        }
    }

    public function get_clue()
    { //Joseph
		$clues_arr = session()->get("clues");                //retrieve the clues array from the session data and get the number of clues in the array.
		$num_clues = count($clues_arr);                      //count number of arrays
		$clue_index = rand(0, $num_clues-1);                 //generate a random index within the bounds of the array
		$clue = $clues_arr[$clue_index];                     //use this index to retrieve a single clue
		return $this->response->setJSON(['clue' => $clue]); //return the clue to the user as a JSON response
    }

    public function check_answer()
    { //Pao
        if($this->request->getMethod() == 'post') {
            $post_data = $this->request->getPost();
            if($post_data['answer'] != "") {
                if(session()->get("guessed") === FALSE) {
                    $secret_word = session()->get("secret_word");
                    if(strcasecmp(trim($secret_word), trim($post_data['answer'])) == 0) {
                        $num_wins = session()->get("num_wins");
                        session()->set(['num_wins'=>($num_wins+1) ]);
                        $data = $this->get_game_stats();
                        $data['message'] = "You guessed it - ".session()->get("secret_word")."! Click Next Round or End Game.";
                        session()->set(['guessed'=>TRUE]);
                        $data['next_round'] = true;
                        return $this->response->setJSON($data);
                    } else {
                        $data['next_round'] = false;
                        $data['message'] = "Sorry, wrong answer. Get another clue.";
                        return $this->response->setJSON($data);
                    }
                }
            }
        }
    }

    public function reset()
    { //Hannah
        //change

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

        $num_attempts = (session()->get('num_attempts') != null) ?session()->get('num_attempts'):0;
        $num_games_played = (session()->get('num_games_played') != null) ?session()->get('num_games_played'):0;
        $num_wins = (session()->get('num_wins') != null) ?session()->get('num_wins'):0;
        
        $data = [
            'num_attempts' => $num_attempts,
            'num_games_played' => $num_games_played,
            'num_wins' => $num_wins
        ];
        return $data;
    }

    protected function request_word($category) { //Pao

    }
    
    protected function request_clues($word) { //Hannah-revision1

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

    private function rand_float($st_num=0,$end_num=1,$mul=1000000) {
        if ($st_num>$end_num) return false;
        return mt_rand($st_num*$mul,$end_num*$mul)/$mul;
    }
}
