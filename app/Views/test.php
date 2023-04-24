<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Test: Guess the Word with ChatGPT</title>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    </head>
    <body>
        <!-- Image loader -->
        <div id='loader' style='display: none; z-index: 10; width: 100%; height: 100%; position: absolute; left: 0; top: 0;'>
            <div style="margin: auto;"><img src='loading.gif'></div>
        </div>

        <div id="content">
            <h2>Welcome<?= " ".$player_name ?>!</h2>
            
            <div style="display: <?= $started?"none":"block" ?>">
                <h3>How to play:</h3>
                <ul>
                    <li>Enter your name and click Start Game.</li>
                    <li>Click Get Category to let the app choose a random category.</li>
                    <li>App will request a word from ChatGPT for the chosen category, and 10 clues for the word.</li>
                    <li>Click Get Clue and try to guess the word.</li>
                    <li>If you have a guess in mind, enter the word and submit your answer.</li>
                    <li>If you guess the word, you earn 1 point and you can start a next round or end the game.</li>
                    <li>After the 10 clues and you have not guessed the word, the mystery word will be shown. You won't earn a point but you can start a next round or end the game.</li>
                </ul>
                <form method="post" action="test">        
                    <div>
                        Your Name: <input type="text" name="name" id="playerName" value="" <?= $started?"readonly":"" ?> required />
                        <input type="submit" id="startGame" name="startGame" value="Start Game" <?= $started?"disabled":"" ?> />
                    </div>
                </form>
            </div>
            
            <div>
                <h3>Game Stats:</h3>
                <p>Wins: <span id="num_wins"><?= $num_wins ?></span></p>
                <p>Rounds: <span id="num_games_played"><?= $num_games_played ?></span></p>
                <p>Attempts this round: <span id="num_attempts"><?= $num_attempts ?></span></p>
            </div>
            <div>
                <h3>Category:</h3>
                <h2><span id="category" style="color: blue"> <span></h2>  
                <button id="getCategory" <?= $started?"":"disabled" ?> >Get Category</button>
            </div>

            <div>
                <h3>Clue:</h3><h2><span id="clue" style="color: blue"> </span></h2>
                <div><button id="getClue" <?= $started?"":"disabled" ?> >Get Clue</button></div>
            </div>

            <div>
                <h3>Your Guess: <input type="text" id="guess" name="guess" /></h3>
                <div><button id="checkAnswer" <?= $started?"":"disabled" ?> >Submit Answer</button></div>
                
                <h2><span id="message" style="color: red"></span></h2>
            <div>
                <!-- <h3>Mystery Word(s): <span id="mystery_word">???<span></h3> -->
                <div><button  style="display: none;" id="revealWord" <?= $started?"":"disabled" ?> >Reveal Word</button>
                <button id="reset" <?= $started?"":"disabled" ?> >Next Round</button> <button id="end" <?= $started?"":"disabled" ?> onclick="location.href='<?= base_url() ?>end_game'" >End Game</button></div>
            </div>
        </div>
        <script>
            $(document).ready(function(){

                $("#getCategory").click(function(){
                    //$("#getCategory").prop('disabled', true);
                    
                    //$.post("get_category",
                    /* {
                        name: "Donald Duck",
                        city: "Duckburg"
                    }, */
                    $.ajax({
                        url: '<?= base_url() ?>get_category',
                        type: 'post',
                        //data: {search:search},
                        beforeSend: function(){
                            // Show image container
                            $("#getCategory").prop('disabled', true);
                            $("#loader").show();
                            $("#content").hide();
                        },
                        success: function(data,status){
                            console.log(data);
                            //const categoryObj = JSON.parse(data);
                            //console.log(categoryObj);
                            $("#category").text(data.categoryTitle);
                            $("#num_games_played").text(data.num_games_played);
                            $("#num_attempts").text(data.num_attempts);
                            $("#num_wins").text(data.num_wins);
                            //alert("Data: " + data + "\nStatus: " + status);
                        
                            $.post("<?= base_url() ?>initialize_clues"); //initialize clues as well 

                            $("#reset").prop('disabled', false);
                            $("#revealWord").prop('disabled', false);
                            $("#getClue").prop('disabled', false);
                            $("#checkAnswer").prop('disabled', false);
                            if(data.next_round == true) {
                                $("#checkAnswer").prop('disabled', true);
                                $("#guess").prop('readonly', true);
                            } else {
                                
                                $("#getClue").prop('disabled', false);
                                $("#checkAnswer").prop('disabled', false);
                            }
                        },
                        complete:function(data){
                            // Hide image container
                            $("#loader").hide();
                            $("#content").show();
                        }
                    });
                    /* function(data,status){
                        console.log(data);
                        //const categoryObj = JSON.parse(data);
                        //console.log(categoryObj);
                        $("#category").text(data.categoryTitle);
                        $("#num_games_played").text(data.num_games_played);
                        $("#num_attempts").text(data.num_attempts);
                        $("#num_wins").text(data.num_wins);
                        //alert("Data: " + data + "\nStatus: " + status);
                    }); */
                    
                    /* $.post("<= base_url() ?>initialize_clues"); //initialize clues as well 

                    $("#reset").prop('disabled', false);
                    $("#revealWord").prop('disabled', false);
                    $("#getClue").prop('disabled', false);
                        $("#checkAnswer").prop('disabled', false); */
                });

                $("#getClue").click(function(){
                    //$.post("<= base_url() ?>get_clue",
                    /* {
                        name: "Donald Duck",
                        city: "Duckburg"
                    }, */
                    $.ajax({
                        url: '<?= base_url() ?>get_clue',
                        type: 'post',
                        beforeSend: function(){
                            // Show image container
                            $("#loader").show();
                            $("#content").hide();
                        },
                        success: function(data,status){
                            //console.log(data);
                            //const clueObj = jQuery.parseJSON(data);
                            $("#clue").text(data.clue);
                            $("#num_games_played").text(data.num_games_played);
                            $("#num_attempts").text(data.num_attempts);
                            $("#num_wins").text(data.num_wins);
                            $("#checkAnswer").prop('disabled', false);
                            $("#guess").prop('readonly', false);
                            if(data.next_round === true) {
                                $("#checkAnswer").prop('disabled', true);
                                $("#guess").prop('readonly', true);
                                $("#getClue").prop('disabled', true);
                            }
                            //alert("Data: " + data + "\nStatus: " + status);
                            
                            $("#message").text("");
                        },
                        complete:function(data){
                            // Hide image container
                            $("#loader").hide();
                            $("#content").show();
                        }
                    });
                });

                $("#revealWord").click(function(){
                    $.post("<?= base_url() ?>reveal_word",
                    /* {
                        name: "Donald Duck",
                        city: "Duckburg"
                    }, */
                    function(data,status){
                        console.log(data);
                        //const wordObj = jQuery.parseJSON(data);
                        /* $("#mystery_word").text(data.secret_word);
                        $("#num_games_played").text(data.num_games_played);
                        $("#num_attempts").text(data.num_attempts);
                        $("#num_wins").text(data.num_wins); */
                        //alert("Data: " + data + "\nStatus: " + status);

                        $("#reset").click();
                    });
                });

                $("#checkAnswer").click(function(){
                    $("#checkAnswer").prop('disabled', true);
                    $("#guess").prop('readonly', true);
                    let guess = $("#guess").val();
                    console.log(guess);
                    if(guess != "") {
                        $.post("<?= base_url() ?>check_answer",
                        {
                            answer: guess,
                        },
                        function(data,status){
                            //console.log(data);
                            $("#message").text(data.message);
                            $("#guess").val("");
                            $("#num_games_played").text(data.num_games_played);
                            $("#num_attempts").text(data.num_attempts);
                            $("#num_wins").text(data.num_wins);
                            if(data.next_round===true) {
                                $("#getClue").prop('disabled', true);
                            }
                        });
                    }
                });


                $("#reset").click(function(){
                    $.post("<?= base_url() ?>reset",
                    /* {
                        name: "Donald Duck",
                        city: "Duckburg"
                    }, */
                    function(data,status){
                        //console.log(data);
                        //const clueObj = jQuery.parseJSON(data);
                        $("#clue").text("");
                        $("#num_games_played").text(data.num_games_played);
                        $("#num_attempts").text("0");
                        $("#num_wins").text(data.num_wins);
                        $("#mystery_word").text("");
                        $("#category").text("");
                        $("#getCategory").prop('disabled', false);
                        $("#reset").prop('disabled', true);
                        $("#revealWord").prop('disabled', true);
                        $("#getClue").prop('disabled', true);
                        $("#startGame").prop('disabled', true);
                        $("#checkAnswer").prop('disabled', true);
                        $("#message").text("");
                        //$("#playerName").prop('readonly', false);
                        //alert("Data: " + data + "\nStatus: " + status);
                    });
                });

            });
        </script>
    </body>
</html>