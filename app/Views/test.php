<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Test: Guess the Word with ChatGPT</title>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    </head>
    <body>
        <!-- Image loader -->
        <div id='loader' style='display: none; z-index: 10; width: 100%; height: 100%;  margin: auto'>
            <div style="text-align: center"><img src='loading.gif'></div>
        </div>

        <div id="content">
            <h2>Welcome<?= " ".$player_name ?>!</h2>
            
            <div style="display: <?= $started?"none":"block" ?>">
                <h3>How to play:</h3>
                <ul>
                    <li>Enter your name and click Start Game.</li>
                    <li>Click <b>Start Round</b> to let the app choose a random category.</li>
                    <li>App will request a word from ChatGPT for the chosen category, and 10 clues for the word.</li>
                    <li>Click <b>Get Clue</b> and try to guess the word.</li>
                    <li>If you have a guess in mind, enter the word and <b>submit your answer</b>.</li>
                    <li>If you guess the word, you earn 1 point and you can start a next round or end the game.</li>
                    <li>After the 10 clues and you have not guessed the word, the mystery word will be shown. You won't earn a point but you can start a <b>next round</b> or <b>end the game</b>.</li>
                </ul>
                <form method="post" action="<?= base_url()?>">        
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
                <button id="getCategory" <?= $started?"":"disabled" ?> >Start Round</button>
            </div>
            <div>
                <h3>Category:</h3>
                <h2><span id="category" style="color: blue"> <span></h2>  
            </div>

            <div>
                <h3>Clue:</h3><h2><span id="clue" style="color: blue"> </span></h2>
            </div>

            <div>
                <h3>Your Guess: <input type="text" id="guess" name="guess" /></h3>
                <div><button id="checkAnswer" <?= $started?"":"disabled" ?> >Submit Answer</button>
                <button id="getClue" <?= $started?"":"disabled" ?> >Get Clue</button></div>
                
                <h2><span id="message" style="color: red"></span></h2>
            <div>
                <button id="reset" <?= $started?"":"disabled" ?> >Next Round</button> <button id="endGame" <?= $started?"":"disabled" ?> >End Game</button></div>
            </div>
        </div>
        <script>
            $(document).ready(function(){

                $("#getCategory").click(function(){

                    var ajxCategory = $.ajax({
                        url: '<?= base_url() ?>get_category',
                        type: 'post',
                        //data: {search:search},
                        beforeSend: function(){
                            // Show image container
                            $("#getCategory").prop('disabled', true);
                            $("#loader").show();
                            $("#content").hide();
                        },
                        success: function ( data, status, jqXhr ) {
                            console.log(data);
                            if(data.error != undefined) {
                                console.log(data.error);
                            }
                            $("#category").text(data.categoryTitle);
                            $("#num_games_played").text(data.num_games_played);
                            $("#num_attempts").text(data.num_attempts);
                            $("#num_wins").text(data.num_wins);
                            $("#reset").prop('disabled', false);
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
                        error: function (jqXHR, textStatus, errorThrown) {
                            console.log(errorThrown);
                        }
                    }),
                    ajxWord = ajxCategory.then(function(data) {
                        return $.ajax({
                            url: '<?= base_url() ?>initialize_word',
                            type: 'post',
                            success: function( data, status, jqXhr) {
                                console.log(data);
                                if(data.error != undefined) {
                                    console.log(data.error);
                                }
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                console.log(errorThrown);
                            }
                        });
                    }),
                    ajxClues = ajxWord.then(function(data) {
                        return $.ajax({
                            url: '<?= base_url() ?>initialize_clues',
                            type: 'post',
                            success: function( data, status, jqXhr) {
                                console.log(data);
                                if(data.error != undefined) {
                                    console.log(data.error);
                                }
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                console.log(errorThrown);
                            }
                        });

                    });

                    ajxClues.done(function(data) {
                        //console.log(data);
                        $("#loader").hide();
                        $("#content").show();
                    });
                });

                $("#getClue").click(function(){
                    $.ajax({
                        url: '<?= base_url() ?>get_clue',
                        type: 'post',
                        beforeSend: function(){
                            // Show image container
                            $("#loader").show();
                            $("#content").hide();
                        },
                        success: function(data,status,jqXhr){
                            console.log(data);
                            if(data.error != undefined) {
                                console.log(data.error);
                            }
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
                            
                            $("#message").text("");
                        },
                        complete:function(data){
                            // Hide image container
                            $("#loader").hide();
                            $("#content").show();
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            console.log(errorThrown);
                        }
                    });
                });

                $("#checkAnswer").click(function(){
                    let guess = $("#guess").val();
                    console.log(guess);
                    if(guess != "") {
                        $("#checkAnswer").prop('disabled', true);
                        $("#guess").prop('readonly', true);
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
                    } else {
                        alert("Input answer");
                    }
                });


                $("#reset").click(function(){
                    if(confirm("Next Round?")) {
                        $.post("<?= base_url() ?>reset",
                        function(data,status){
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
                        });
                    }
                });

                $('#endGame').click(function(e) {
                    if(confirm("End Game?")) {
                        location.href='<?= base_url() ?>end_game';
                    }
                });
            });
        </script>
    </body>
</html>