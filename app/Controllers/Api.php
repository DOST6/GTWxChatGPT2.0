<?php

namespace App\Controllers;
//use CodeIgniter\API\ResponseTrait;

class Api extends BaseController
{
    /* protected $word_category = array();
    protected $secret_word;
    protected $word_clues = array(); */
    
                
    //use ResponseTrait;

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

        $data=array();

        if(session()->get("started") == true) {
            $data = $this->get_game_stats();
            /* $data['player_name'] = session()->get("player_name");
            $data['round_start'] = session()->get("round_start");
            $data['next_round'] = session()->get("next_round");
            $data['started'] = TRUE; */
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
                'num_wins' => 0,
                'score' => 0,
                'round_start' => false,
                'next_round' => false
            ];
        }
        //$data['next_round'] = session()->get("next_round");
        return view('game_view', $data);
    }

    public function start_game() {
        if($this->request->getMethod() == 'post') {
            $post_data = $this->request->getPost();
            if($post_data['name'] != "") {
                $session_data = [
                    'started' => TRUE,
                    'player_name' => $post_data['name'],
                    /* 'category' => array(),
                    'secret_word' => "",
                    'clues' => array(),
                    'guessed' => FALSE,
                    'num_attempts' => 0,
                    'num_games_played' => 0,
                    'num_wins' => 0,
                    'score' => 0,
                    'next_round' => false */
                ];
                session()->set($session_data);
            }
            
            $data = $this->get_game_stats();
            /* $data['player_name'] = session()->get("player_name");
            $data['num_attempts'] = 0;
            $data['started'] = TRUE;
            $data['next_round'] = false; */
            return $this->response->setJSON($data);
        }
    }

    public function get_category() { //Jean --start round
        
        if($this->request->getMethod() == 'post') {
            $num_games_played = session()->get("num_games_played");

            $category = $this->categories[rand(0,19)];

            session()->set(['num_games_played'=>($num_games_played+1)]);
            //session()->set(['num_attempts'=>0]);
            //session()->set(['guessed'=>false]);
            session()->set(['category'=>$category]);
            session()->set(['round_start' => true]);
            
            $data = $this->get_game_stats();
            //$data['categoryTitle'] = $category['categoryTitle'];
            //$data['next_round'] = false;
            //return $this->response->setJSON($category);
            return $this->response->setJSON($data);
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
                return $this->response->setJSON(['info'=>"Secret word set."]);
            }
        }
    }

    public function initialize_clues() { //Jean
        if($this->request->getMethod() == 'post') {
            $secret_word = session()->get("secret_word");
            if($secret_word != "") {
                $clues_arr = array();
                $clues_arr = $this->request_clues(session()->get("secret_word"));
                if(count($clues_arr) > 0) {
                    session()->set(['clues'=>$clues_arr]);
                }
                return $this->response->setJSON(['info'=>"Clues set."]);
            }
        }
    }

    public function get_clue() { //Joseph
        $data = array();
        if($this->request->getMethod() == 'post') {
            $clues_arr = session()->get("clues");
            $num_attempt = session()->get('num_attempts');// == 0 ? 0 : session()->get('num_attempts');
            if($num_attempt >= count($clues_arr)) {
                session()->set(['next_round'=>true]); //no more clues.
                $data = $this->get_game_stats();
                $data['clue'] = "<span class='w3-text-red'>No more clues. You lose.</span><br>The answer is: <span class='w3-text-orange'><b>".session()->get('secret_word')."</b>.</span><br><span class='w3-medium w3-text-white'>Click <b>Next Round</b> or <b>End Game</b>.</span></span>";
                //$data['next_round'] = true;
                return $this->response->setJSON($data);
            } else {
                session()->set(['num_attempts'=>($num_attempt+1)]);
                $data = $this->get_game_stats();
                //$data['next_round'] = false;
                //$data['secret_word'] = session()->get('secret_word');
                $data['clue'] = $clues_arr[$num_attempt];
                return $this->response->setJSON($data);
                
                //return $this->response->setJSON(['clue'=>$clues_arr[$num_attempt]]);
            }
        }
    }

    public function check_answer() { //Pao
        if($this->request->getMethod() == 'post') {
            $post_data = $this->request->getPost();
            if($post_data['answer'] != "") {
                if(session()->get("guessed") === FALSE) {
                    $secret_word = session()->get("secret_word");
                    if(strcasecmp(trim($secret_word), trim($post_data['answer'])) == 0) {
                        $num_wins = session()->get("num_wins");
                        $prev_score = session()->get("score");
                        $curr_score = number_format($prev_score + (1.1 - (session()->get('num_attempts')*0.1)), 2);
                        session()->set(['score'=>$curr_score]);
                        session()->set(['num_wins'=>($num_wins+1) ]);
                        session()->set(['next_round'=>true]);
                        session()->set(['guessed'=>TRUE]);
                        $data = $this->get_game_stats();
                        $data['icon'] = "success";
                        $data['message'] = "<span class='w3-text-green'>You guessed it - <b>".session()->get("secret_word")."</b>!<br><span class='w3-medium w3-text-gray'>Click <b>Next Round</b> or <b>End Game</b>.</span></span>";
                        //$data['next_round'] = true;
                        return $this->response->setJSON($data);
                    } else {
                        //$data['next_round'] = false;
                        $data = $this->get_game_stats();
                        $data['icon'] = "error";
                        $data['message'] = "<span class='w3-text-red'>Sorry, wrong answer. Get another clue.</span>";
                        return $this->response->setJSON($data);
                    }
                }
            }
        }
    }

    public function reset() { //Hannah --end round
        if($this->request->getMethod() == 'post') {
            if(session()->get("round_start") == true) {
                $session_data = [                
                    'category' => array(),
                    'secret_word' => "",
                    'clues' => array(),
                    'guessed' => FALSE,
                    'num_attempts' => 0,
                    'next_round' => false,
                    'round_start' => false,
                ];
                session()->set($session_data);
            }
            $data = $this->get_game_stats();
            return $this->response->setJSON($data);
        }
    }

    public function end_game() { //Aldwin
        if(session()->get("started") == true) {
            //record score
            $name = session()->get('player_name');
            $score = session()->get('score');
            $player_scores = array();
            $path = 'player_scores.json';
            if($score > 0) {
                if(file_exists($path)) {
                    $data = file_get_contents($path); //data read from json file
                    //print_r($data);
                    if($data != "") {
                        $player_scores = json_decode($data, true);  //decode data to associative array
                        if($score > end($player_scores)) {
                            //print_r($player_scores);
                            if (array_key_exists($name,$player_scores)) {
                                if($score > $player_scores[$name]) {
                                    $player_scores[$name]=$score;
                                }
                            } else {
                                $player_scores[$name]=$score;
                            }
                        } else {
                            if(count($player_scores)<100) {
                                $player_scores[$name]=$score;
                            }
                        }
                        arsort($player_scores);
                    } else {
                        $player_scores[$name]=$score;
                    }

                    //print_r($player_scores);

                    // Convert JSON data from an array to a string
                    $jsonString = json_encode($player_scores, JSON_PRETTY_PRINT);
                    // Write in the file
                    $fp = fopen($path, 'w');
                    fwrite($fp, $jsonString);
                    fclose($fp);
                }
            }

            $session_data = [
                'started' => FALSE,
                'player_name' => "Player",
                'category' => array(),
                'secret_word' => "",
                'clues' => array(),
                'guessed' => FALSE,
                'num_attempts' => 0,
                'num_games_played' => 0,
                'num_wins' => 0,
                'score' => 0,
                'next_round' => false,
                'round_start' => false,
            ];
            session()->set($session_data);
        }
        return redirect()->to("/");
    }

    public function leaderboard() {
        if($this->request->getMethod() == 'post')
        {
            $path = 'player_scores.json';
            $data = file_get_contents($path); //data read from json file
            if($data != "")
            {
                /* $player_scores = json_decode($data, true);  //decode data to associative array
                //return $this->response->setJSON($data);
                $response = "";
                foreach($player_scores as $player => $score) {
                    $response .= '<li><span class="w3-text-white">'.$player.' </span><span class="w3-badge w3-blue">'.$score.'</span></li>';
                } */
                //return $this->respond($response);
                return $this->response->setJSON($data);
            }
        }

    }

    protected function get_game_stats() { //Aldwin
        $data = [
            
            'started' => session()->get('started'),
            'player_name' => session()->get('player_name'),
            'category' => session()->get('category'),
            'secret_word' => session()->get('secret_word'),
            'clues' => session()->get('clues'),
            'guessed' => session()->get('guessed'),
            'num_attempts' =>  session()->get('num_attempts'),
            'num_games_played' =>  session()->get('num_games_played'),
            'num_wins' =>  session()->get('num_wins'),
            'score' =>  session()->get('score'),
            'next_round' =>  session()->get('next_round'),
            'round_start' =>  session()->get('round_start'),
            //'num_attempts' => session()->get('num_attempts'),
            //'num_games_played' => session()->get('num_games_played'),
            //'num_wins' => session()->get('num_wins'),
            //'started' => session()->get('started'),
            //'score' => session()->get('score'),
            //'guessed' => session()->get('guessed'),
        ];
        //print_r($data); die();
        /* $num_attempts = (session()->get('num_attempts') != 0) ?session()->get('num_attempts'):0;
        $num_games_played = (session()->get('num_games_played') != 0) ?session()->get('num_games_played'):0;
        $num_wins = (session()->get('num_wins') != 0) ?session()->get('num_wins'):0;
        $score = (session()->get('score') != 0) ?session()->get('score'):0;
        
        $data = [
            'num_attempts' => $num_attempts,
            'num_games_played' => $num_games_played,
            'num_wins' => $num_wins,
            'score' => $score
        ]; */
        return $data;
    }

    protected function request_word($category) { //Pao
        //$prompt = "Suggest a ".$category.".";
        $prompt = "Suggest a ".$category.", no punctuations, no numbers.";
        $word = $this->chatGPT($prompt, round($this->rand_float(0.01,2.00),2) );
        if(substr($word,-1)==".") {
            $word = substr($word,strlen($word)-1); //remove trailing period
        }
        return $word;
    }
    
    protected function request_clues($word) { //Hannah
        $clues_arr = array();
        if(strlen($word) > 1) {
            $prompt = "Suggest 10 statements that will serve as clue for ".$word." without using the word ".$word.".";
            $clues = trim($this->chatGPT($prompt)); //trim to remove extra line breaks
            $clues_arr = explode("\n", $clues);
        }
        return $clues_arr;
    }

    private function chatGPT($prompt, $temperature=0.8) { // Garry
        
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
            'verify' => getenv('curl_verify_ssl')==0?false:true, //set to false for testing purposes on local machine only
            'headers'=>$headerData,
            'json' => $postData
         ]);

         // Read response
        $code = $response->getStatusCode();
        $reason = $response->getReason();
    
        if($code == 200){ // Success
    
            // Read data 
            $response_obj = json_decode($response->getBody());
            $choices_arr = $response_obj->choices;
            $choices_obj = $choices_arr[0];
            return $choices_obj->text;  
        } else{
           echo "failed";
           die;
        }
    }

    private function rand_float($st_num=0, $end_num=1, $mul=1000000) {
        if ($st_num>$end_num) return false;
        return mt_rand($st_num*$mul,$end_num*$mul)/$mul;
    }
}
