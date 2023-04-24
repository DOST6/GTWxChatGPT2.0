<?php

namespace App\Controllers;

class App extends BaseController
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

    public function index() {

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
                    //'num_wins' => 0
                ];
                //session()->start();
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
        //$data = $this->get_game_stats();
        //$player_name = session()->get("player_name")==""?"Player":session()->get("player_name");
        //$data['player_name'] = $player_name;
        //$data['started'] = session()->get("started");
        $data['next_round'] = false;
            //'started' => FALSE,
            //'num_attempts' => 0,
            //'num_games_played' => 0,
            //'num_wins' => 0,
            //'guessed' => FALSE
        //];
        return view('test', $data);
    }

    public function reset() {
        if($this->request->getMethod() == 'post') {
            $num_games_played = session()->get("num_games_played");
            $data = [
                //'started' => TRUE,
                //'player_name' => $post_data['name'],
                'category' => array(),
                'secret_word' => "",
                'clues' => array(),
                'guessed' => FALSE,
                'num_attempts' => 0,
                //'num_games_played' => $num_games_played+1,
                //'num_wins' => 0
                'next_round' => false,
            ];
            //session()->start();
            session()->set($data);
        }
    }

    public function end_game() {
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
        //session()->start();
        session()->set($session_data);
        return redirect()->to("/");
    }

    public function get_category() {
        //$data = array();
        if($this->request->getMethod() == 'post') {
            $num_games_played = session()->get("num_games_played");
            $category = $this->categories[rand(0,19)];
            //$secret_word = $this->request_word($category['noun']);
            //$word_clues = $this->request_clues($secret_word);
            //$data['category'] = $category;
            //$data['secret_word'] = $secret_word;
            //$data['clues'] = $word_clues;
            session()->set(['num_games_played'=>($num_games_played+1)]);
            session()->set(['category'=>$category]);

            $secret_word = $this->request_word($category['noun']);
            session()->set(['secret_word'=>$secret_word]);

            $clues_arr = $this->request_clues($secret_word);
            //print_r($clues_arr); die();
            session()->set(['clues'=>$clues_arr]);

            $data = $this->get_game_stats();
            $data['categoryTitle'] = $category['categoryTitle'];
            $data['next_round'] = false;
            //print_r($data); die();
            return $this->response->setJSON($data);
            //return $this->response->setJSON($category);
        }
    }

    public function reveal_word() {
        if($this->request->getMethod() == 'post') {
            $secret_word = session()->get("secret_word");
            if($secret_word != "") {
                return $this->response->setJSON(['secret_word'=>$secret_word]);
            }
        }
    }

    public function initialize_clues() {
        if($this->request->getMethod() == 'post') {
            $secret_word = session()->get("secret_word");
            if($secret_word != "") {
                $clues_arr = $this->request_clues(session()->get("secret_word"));
                session()->set(['clues'=>$clues_arr]);
            }
        }
    }

    public function check_answer() {
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
                        //return $this->response->setJSON(['secret_word'=>$secret_word]);
                    } else {
                        $data['next_round'] = false;
                        $data['message'] = "Sorry, wrong answer. Get another clue.";
                        return $this->response->setJSON($data);
                    }
                }
            }
        }
    }

    public function get_clue() {
        $data = array();
        if($this->request->getMethod() == 'post') {
            $clues_arr = session()->get("clues");
            if(count($clues_arr) == 0) { //clues not retrieved yet
                $clues_arr = $this->request_clues(session()->get("secret_word"));
                $num_attempt = 0;
                session()->set(['num_attempts'=>($num_attempt+1)]);
                session()->set(['clues'=>$clues_arr]);
                $data = $this->get_game_stats();
                $data['clue'] = $clues_arr[$num_attempt];
                return $this->response->setJSON($data);
                //return $this->response->setJSON(['clue'=>$clues_arr[$num_attempt]]);
            } else {
                //$clues_arr = session()->get("clues");
                $num_attempt = session()->get('num_attempts');
                if($num_attempt >= count($clues_arr)) {
                    $data = $this->get_game_stats();
                    $data['clue'] = "No more clues. You lose. The answer is: ".session()->get('secret_word').". Click Next Round or End Game. ";
                    $data['next_round'] = true;
                    //$data['message'] = "Click Next Round or End Game";
                    return $this->response->setJSON($data);
                    //return $this->response->setJSON(['clue'=>'No more clues. You lose.']);
                } else {
                    session()->set(['num_attempts'=>($num_attempt+1)]);
                    $data = $this->get_game_stats();
                    $data['clue'] = $clues_arr[$num_attempt];
                    $data['next_round'] = false;
                    return $this->response->setJSON($data);
                    //return $this->response->setJSON(['clue'=>$clues_arr[$num_attempt]]);
                }
            }
        }
    }

    protected function get_game_stats() {
        
        $data = [
            ///'started' => TRUE,
            //'player_name' => $post_data['name'],
            //'category' => array(),
            //'secret_word' => "",
            //'clues' => array(),
            //'guessed' => FALSE,
            'num_attempts' => session()->get('num_attempts'),
            'num_games_played' => session()->get('num_games_played'),
            'num_wins' => session()->get('num_wins')
        ];
        return $data;
    }

    protected function request_word($category) {
        $prompt = "Suggest one ".$category.".";
        //echo $prompt; die();
        $word = $this->chatGPT($prompt);
        if(substr($word,-1)==".") {
            $word = substr($word,strlen($word)-1); //remove trailing period
        }
        return $word;
    }
    
    protected function request_clues($word) {
        $prompt = "Suggest 10 statements that will serve as clue for ".$word." without using the word ".$word.".";
        $clues = trim($this->chatGPT($prompt)); //trim to remove extra line breaks
        $clues_arr = explode("\n", $clues);
        //echo "<pre>";
        //var_dump($clues);
        //echo "</pre><pre>";
        //print_r(trim($clues)); die();

        return $clues_arr;
    }

    public function test() {
        //echo $this->chatGPT();
        $data = array();
        $data['category'] = "category";
        $data['secret_word'] = "secret_word";
        $data['clues'] = "word_clues";
        $category = $this->categories[rand(0,19)];
        //$category = ["categoryTile"=>"Countries", "noun"=>"country"];
        //echo "<pre>";
        //echo $category['categoryTitle']."<br>";
        //echo $category['noun'];
        //die();
        $secret_word = $this->request_word($category['noun']);
        //echo $secret_word; die();
        //$secret_word = "Philippines";
        $word_clues = $this->request_clues($secret_word);
        $data['category'] = $category;
        $data['word'] = $secret_word;
        $data['clues'] = $word_clues;
        return view('test', $data);
    }

    public function chat($prompt = "" ) {
        if($prompt != "") {
            echo $this->chatGPT($prompt);
        }
    }

    private function chatGPT($prompt="Say this is a test") {

        //echo $prompt; die();

        $OPENAI_API_KEY = getenv('OPENAI_API_KEY');

        //echo $OPENAI_API_KEY; die();

        /* curl https://api.openai.com/v1/completions \
        -H "Content-Type: application/json" \
        -H "Authorization: Bearer $OPENAI_API_KEY" \
        -d '{
        "model": "text-davinci-003",
        "prompt": "Say this is a test",
        "max_tokens": 7,
        "temperature": 0
        }' */
        $options = [
            'CURLOPT_SSL_VERIFYHOST' => false,
            'CURLOPT_SSL_VERIFYPEER' => false,
        ];

        //'cert' => ['/path/server.pem', 'password']
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
            'temperature' => 1
         );

        // Send request
        $response = $client->post($apiURL,[
            'debug' => true,
            'verify' => false, //for testing purposes only
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
