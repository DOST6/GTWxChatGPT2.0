<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guess the Word with ChatGPT</title>
    <link rel="icon" type="image/png" href="robot.png">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://kit.fontawesome.com/2741aac496.js" crossorigin="anonymous"></script>
    <style>
        body, h1, h2, h3, h4, h5 {
            font-family: "Poppins", sans-serif
        }

        body {
            font-size: 16px;
        }

        .bgimg-x {
            background-image: url("robot-books.jpg");
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
            min-height: 100%;
        }
        .bgimg { 
            position: relative; 
            height: 100vh;
            width: 100%;
            /* display: flex;
            align-items: center;
            justify-content: center; */
            background-image: url('robot-books.jpg');
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        .bgimg::before {
            content: "";
            position: absolute;
            top: 0px;
            right: 0px;
            bottom: 0px;
            left: 0px;
            height: 100vh;
            /* background-color: rgba(152, 66, 211, 0.635); */
            background-color: rgba(210,105,30, 0.3);
        }
        .main-content {
            position: relative;
        }
        .bg-opacity {
            background-color: rgba(192,192,192,0.4);
        }
        .bg-theme {
            background-color: rgba(205,133,63,1);
        }
        #sidebar {
            height: 100vh;
        }
    </style>
</head>

<body class="bgimg">
    <div class="main-content">
        <!-- Sidebar/menu -->
        <nav class="w3-collapse w3-top w3-large w3-padding w3-text-white bg-theme"
            style="z-index:3; width:300px; font-weight:bold; display: none;" id="sidebar">
            <div><span onclick="w3_close()" class="w3-button w3-red w3-large w3-hover-white w3-right">&times;</span></div>
            <div class="w3-bar-block">
                <a href="#" onclick="w3_close()" class="w3-bar-item w3-button w3-hover-white">Home</a>
                <a href="#instructions" onclick="w3_close(); document.getElementById('instructions_modal').style.display='block'; " class="w3-bar-item w3-button w3-hover-white">Instructions</a>
                <a href="#game" onclick="w3_close()" class="w3-bar-item w3-button w3-hover-white">Game</a>
                <a href="#leaderboard" id="leaderboard" class="w3-bar-item w3-button w3-hover-white">Leaderboard</a>
                <!-- <a href="#contact" onclick="w3_close()" class="w3-bar-item w3-button w3-hover-white">Group Members</a> -->
            </div>
        </nav>
        <!-- Overlay effect when opening sidebar on small screens -->
        <div class="w3-overlay" onclick="w3_close()" style="cursor:pointer" title="close side menu"
            id="myOverlay"></div>
        <!-- Image loader -->
        <div id='loader' class="w3-overlay w3-black " style="z-index: 5; opacity: 0.9">
            <div class="w3-center w3-padding-top-48"><img class="w3-image" src='loader_red.gif'></div>
        </div>
        <!-- Top menu on small screens -->
        <div class="w3-container w3-top-left w3-xlarge">
            <a href="javascript:void(0)" class="w3-button" onclick="w3_open()">☰</a>
        </div>
        <!-- !PAGE CONTENT! -->
        <div class="w3-container" id="main">

            <!-- Header -->
            <div class="w3-container w3-row" style="margin-top:0px">
                <div class="w3-col l1 w3-padding"></div>
                <div class="w3-col l3 w3-padding" style="text-shadow:3px 1px 0 #444">
                    <h1 class="w3-xxlarge w3-text-blue"><b>Guess the word</b></h1>
                    <h1 class="w3-xlarge w3-text-red"><b>with ChatGPT.</b></h1>
                    <!-- <hr style="width:100px;border:5px solid red" class="w3-round"> -->
                    <h3 class="w3-panel w3-text-white" id="welcome" style="display: <?= $started?"block":"none" ?>">Let's Play <span class="w3-xlarge" id="player"><?= $player_name ?>!</span></h3>
                </div>
                <div class="w3-col l8 w3-padding">
                    <!-- Stats -->
                    <div class="w3-padding" style="display: <?= $started?"block":"none" ?>" id="game_stats">
                        <h3><b>Game Stats</b>:</h3>
                        <div class="w3-row-padding">
                            <div class="w3-col m3 s6">
                                <div class="w3-container w3-center">
                                    <span id="num_games_played" class="w3-badge w3-blue w3-xlarge w3-padding"><?= $num_games_played ?></span>
                                    <h4>ROUND</h4>
                                </div>
                            </div>
                            <div class="w3-col m3 s6">
                                <div class="w3-container w3-center">
                                    <span id="num_attempts" class="w3-badge w3-orange w3-xlarge w3-padding"><?= $num_attempts ?></span>
                                    <h4>ATTEMPTS</h4>
                                </div>
                            </div>
                            <div class="w3-col m3 s6">
                                <div class="w3-container w3-center">
                                    <span id="score" class="w3-badge w3-green w3-xlarge w3-padding"><?= $score ?></span>
                                    <h4>SCORE</h4>
                                </div>
                            </div>
                            <div class="w3-col m3 s6">
                                <div class="w3-container w3-center">
                                    <span id="num_wins" class="w3-badge w3-red w3-xlarge w3-padding"><?= $num_wins ?></span>
                                    <h4>WINS</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Instructions-->
            <div class="w3-row-padding w3-margin-top" style="display: <?= $started?"none":"block" ?>" id="game_intro">
            
                
                <div class="w3-col m1 w3-padding"></div>
                <div class="w3-col m6 w3-container w3-card-4 w3-round-large w3-padding bg-opacity ">
                    <h1 class="w3-xlarge w3-text-red"><b>Instructions.</b></h1>
                    <hr style="width:100px;border:3px solid red" class="w3-round">

                    <h3>How to play:</h3>
                    <ul>
                        <li>Enter your name and click <b>Start Game</b>.</li>
                        <li>Click <b>Start Round</b> to let the app choose a random category.</li>
                        <li>App will request a word from ChatGPT for the chosen category, and 10 clues for the word.</li>
                        <li>Click <b>Get Clue</b> and try to guess the word.</li>
                        <li>If you have a guess in mind, click <b>Guess Word</b> and submit your answer.</li>
                        <li>If you guess the word, you earn 1 point and you can start a <b>next round</b> or <b>end the game</b>.</li>
                        <li>After the 10 clues and you have not guessed the word, the mystery word will be shown. You won't earn
                            a point but you can start a <b>next round</b> or <b>end the game</b>.</li>
                    </ul>
                    
                    <!-- <form method="post" action="<?= base_url() ?>" class="w3-row-padding"> -->
                    <div class="w3-row-padding">
                        <div class="w3-col m8">
                            <input class="w3-input w3-border" type="text" name="name" id="playerName" placeholder="Your Name" value="" required />
                        </div>
                        <div class="w3-col m4">
                            <button class="w3-button w3-block w3-padding-large w3-red w3-margin-bottom w3-round w3-hover-green" type="submit"
                            id="startGame" name="startGame">Start Game</button>
                        </div>
                    </div>
                    <!-- </form> -->
                </div>
                <div class="w3-col m4 w3-padding"></div>
            </div>

            <!-- Instructions Modal-->
            <div id="instructions_modal" class="w3-modal">
                <div class="w3-modal-content w3-container w3-padding-large w3-padding-24 w3-card-4 w3-round-large bg-theme" id="instructions">
                    <span onclick="document.getElementById('instructions_modal').style.display='none'" class="w3-button w3-display-topright w3-xxlarge w3-hover-none w3-round-large">&times;</span>
                    <h1 class="w3-xlarge w3-text-red"><b>Instructions.</b></h1>
                    <hr style="width:100px;border:3px solid red" class="w3-round">

                    <h3>How to play:</h3>
                    <ul>
                        <li>Enter your name and click <b>Start Game</b>.</li>
                        <li>Click <b>Start Round</b> to let the app choose a random category.</li>
                        <li>App will request a word from ChatGPT for the chosen category, and 10 clues for the word.</li>
                        <li>Click <b>Get Clue</b> and try to guess the word.</li>
                        <li>If you have a guess in mind, click <b>Guess Word</b> and submit your answer.</li>
                        <li>If you guess the word, you earn 1 point and you can start a <b>next round</b> or <b>end the game</b>.</li>
                        <li>After the 10 clues and you have not guessed the word, the mystery word will be shown. You won't earn
                            a point but you can start a <b>next round</b> or <b>end the game</b>.</li>
                    </ul>
                </div>
            </div>
            
            <!-- Leaderboard Modal-->
            <div id="leaderboard_modal" class="w3-modal">
                <div class="w3-modal-content w3-container w3-padding-large w3-padding-24 w3-card-4 w3-round-large bg-theme" id="instructions">
                    <span onclick="document.getElementById('leaderboard_modal').style.display='none'" class="w3-button w3-display-topright w3-xxlarge w3-hover-none w3-round-large">&times;</span>
                    <h1 class="w3-xlarge w3-text-red"><b>Leaderboard.</b></h1>
                    <hr style="width:100px;border:3px solid red" class="w3-round">

                    <h3>Top Players</h3>
                    <ol class="w3-panel w3-large" id="leaderboard_list">
                    </ol>
                </div>
            </div>

            <!-- Game Play -->
            <div class="w3-container w3-margin-top" id="game" style="display: <?= $started?"block":"none" ?>">
                        
                <!-- <h1 class="w3-xlarge w3-text-red"><b>Game.</b></h1>
                <hr style="width:100px;border:5px solid red" class="w3-round"> -->

                <div class="w3-row" style="display: <?= $round_start?"none":"block" ?>" id="start_round">
                    <div class="w3-col l1 w3-padding"></div>
                    <div class="w3-col l10 w3-padding w3-center">
                        <button href="#game" class="w3-button w3-hover-none" id="getCategory"><img class="w3-image" src="start-round.png" width="400px"></button>
                    </div>
                    <div class="w3-col l1 w3-padding"></div>
                </div>

                <!-- Round -->
                <div class="w3-row" style="display: <?= $round_start?"block":"none" ?>" id="play_round">
                    <div class="w3-col l1 w3-padding"></div>
                    <div class="w3-col l10 w3-padding">
                        <!-- Category and Clue -->
                        <div class="w3-row-padding w3-center" id="cat_clue">
                            <div class="w3-col m3">
                                <span class="w3-tag w3-padding w3-round-large w3-xlarge bg-theme" id="category"><?= isset($category['categoryTitle'])?$category['categoryTitle']:"" ?></span>
                                <h4 class="w3-text-blue">Category</h4>
                            </div>
                            <div class="w3-col m9">
                                <span class="w3-tag w3-padding w3-round-large w3-xlarge bg-theme" id="clue"> </span>
                                <h4 class="w3-text-blue">Clue</h4>
                            </div>
                        </div>
                        
                        <div class="w3-row-padding w3-center">
                            <div class="w3-col m3 w3-panel">
                                <button class="w3-button w3-block w3-blue w3-round-large w3-large" id="reset">Next Round <i class='fas fa-fast-forward'></i></button>
                            </div>
                            <div class="w3-col m3 w3-panel">
                                <button class="w3-button w3-block w3-orange w3-round-large w3-large" id="guessWord" onclick="$('#guess_modal').show()" <?= ($guessed || $next_round)?"disabled":"" ?>>Guess Word <i class='far fa-comment-dots'></i></button>
                            </div>
                            <div class="w3-col m3 w3-panel">
                                <button class="w3-button w3-block w3-green w3-round-large w3-large w3-hover-blue" id="getClue" <?= ($guessed || $next_round)?"disabled":"" ?>>Get Clue <i class='far fa-lightbulb'></i></button>
                            </div>
                            <div class="w3-col m3 w3-panel">
                                <button class="w3-button w3-block w3-red  w3-round-large w3-large" id="endGame">End Game <i class='fas fa-power-off'></i></button>
                            </div>
                        </div>

                        <!-- Game Guess -->
                        <div class="w3-modal" id="guess_modal">
                            <div class="w3-modal-content w3-container w3-padding-large w3-padding-24 w3-card-4 w3-round-large bg-theme" style="width: 500px;">
                                <span onclick="$('#guess_modal').hide()" class="w3-button w3-display-topright w3-xlarge w3-hover-none w3-round-large">&times;</span>
                                <div class="w3-container">
                                    <h3>And the word is...</h3>
                                </div>
                                <div class="w3-white">
                                    <form class="w3-container">
                                        <p>
                                            <input class="w3-input" type="text" name="guess" id="guess" required readonly>
                                        </p>
                                    </form>
                                    <!-- <div class="w3-container">
                                        <h2><span id="message"></span></h2>
                                    </div> -->
                                    <div class="w3-container">
                                        <button class="w3-button w3-green w3-block w3-margin-bottom" id="checkAnswer">Submit Answer</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="w3-col l1 w3-padding"></div>
                </div>
            </div>

        <!-- End page content -->
        </div>
        <div class="w3-panel"></div>
        </div>
    </div>


    <script>
        // Script to open and close sidebar
        function w3_open() {
            document.getElementById("sidebar").style.display = "block";
            document.getElementById("myOverlay").style.display = "block";
            //document.getElementById("main").style.marginLeft = "20%";
            //document.getElementById("sidebar").style.width = "20%";
            //document.getElementById("sidebar").style.display = "block";
            //document.getElementById("openNav").style.display = 'none';
        }

        function w3_close() {
            document.getElementById("sidebar").style.display = "none";
            document.getElementById("myOverlay").style.display = "none";
            //document.getElementById("main").style.marginLeft = "0%";
            //document.getElementById("sidebar").style.display = "none";
            //document.getElementById("openNav").style.display = "inline-block";
        }

        $(document).ready(function () {

            $("#playerName").keypress(function (e) {
                if(e.key == "Enter")
                    e.preventDefault();
            });

            $("#startGame").click(function () {

                let playerName = $("#playerName").val();
                console.log(name);
                if (playerName != "") {
                    $.ajax({
                        url: '<?= base_url('api/start_game') ?>',
                        type: 'post',
                        data: { name: playerName },
                        beforeSend: function () {
                            $("#loader").show();
                            $("#content").hide();
                        },
                        success: function (data, status) {
                            console.log(data);
                            $("#player").html(data.player_name);
                            $("#welcome").show();
                            $("#game").show();
                            $("#game_stats").show();
                            $("#game_intro").hide();                        
                        },
                        complete: function (data) {
                            $("#loader").hide();
                            $("#content").show();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Please type in your name first.',
                    })
                }
            });

            $("#getCategory").click(function () {

                var ajxCategory = $.ajax({
                    //url: '<?= base_url('get_category') ?>',
                    url: '<?= base_url('api/get_category') ?>',
                    type: 'post',
                    //data: {search:search},
                    beforeSend: function () {
                        // Show image container
                        $("#getCategory").prop('disabled', true);
                        $("#loader").show();
                        $("#content").hide();
                    },
                    success: function (data, status, jqXhr) {
                        console.log(data);
                        $("#category").html(data.category.categoryTitle);
                        $("#num_games_played").html(data.num_games_played);
                        $("#num_attempts").html(data.num_attempts);
                        $("#num_wins").html(data.num_wins);
                        $("#score").html(data.score);
                        $("#reset").prop('disabled', false);
                        $("#getClue").prop('disabled', false);
                        $("#guessWord").prop('disabled', false);
                        $("#checkAnswer").prop('disabled', false);
                        $("#play_round").show();
                        $("#start_round").hide();
                        if (data.next_round == true) {
                            $("#checkAnswer").prop('disabled', true);
                            $("#guess").prop('readonly', true);
                        } else {
                            $("#checkAnswer").prop('disabled', false);
                            //$("#getClue").prop('disabled', false);
                            $("#guess").prop('readonly', false);
                        }
                    }
                }),
                    ajxWord = ajxCategory.then(function (data) {
                        return $.ajax({
                            url: '<?= base_url('api/initialize_word') ?>',
                            //url: '<?= base_url('initialize_word') ?>',
                            type: 'post',
                            success: function (data, status, jqXhr) {
                                console.log(data);
                            }
                        });
                    }),
                    ajxClues = ajxWord.then(function (data) {
                        return $.ajax({
                            url: '<?= base_url('api/initialize_clues') ?>',
                            //url: '<?= base_url('initialize_clues') ?>',
                            type: 'post',
                            success: function (data, status, jqXhr) {
                                console.log(data);
                            }
                        });

                    });

                ajxClues.done(function (data) {
                    //console.log(data);
                    $("#loader").hide();
                    $("#content").show();
                    $('html, body').animate({
                        scrollTop: $("#game").offset().top,
                        },
                        500
                    );
                });
            });

            $("#guess").keypress(function (e) {
                if(e.key == "Enter")
                    e.preventDefault();
            });

            $("#getClue").click(function () {
                $.ajax({
                    url: '<?= base_url('api/get_clue') ?>',
                    //url: '<?= base_url('get_clue') ?>',
                    type: 'post',
                    beforeSend: function () {
                        // Show image container
                        $("#loader").show();
                        $("#content").hide();
                    },
                    success: function (data, status) {
                        console.log(data);
                        $("#clue").html(data.clue);
                        $("#num_games_played").html(data.num_games_played);
                        $("#num_attempts").html(data.num_attempts);
                        $("#num_wins").html(data.num_wins);
                        $("#score").html(data.score);
                        $("#checkAnswer").prop('disabled', false);
                        $("#guess").prop('readonly', false);
                        if (data.next_round === true) {
                            $("#checkAnswer").prop('disabled', true);
                            $("#guess").prop('readonly', true);
                            $("#getClue").prop('disabled', true);
                            $("#guessWord").prop('disabled', true);
                            //$("#play_round").hide();
                            //$("#start_round").show();
                        }

                        $("#message").html("");
                    },
                    complete: function (data) {
                        // Hide image container
                        $("#loader").hide();
                        $("#content").show();
                        $('html, body').animate({
                            scrollTop: $("#cat_clue").offset().top,
                            },
                            500
                        );
                    }
                });
            });

            $("#checkAnswer").click(function () {

                let guess = $("#guess").val();
                console.log(guess);
                if (guess != "") {
                    $("#guess_modal").hide();
                    $("#checkAnswer").prop('disabled', true);
                    $("#guess").prop('readonly', true);
                    
                    $('html, body').animate({
                        scrollTop: $("#cat_clue").offset().top,
                        },
                        500
                    );
                    //$.post("<?= base_url('check_answer') ?>",
                    $.post("<?= base_url('api/check_answer') ?>",
                    {
                        answer: guess,
                    },
                    function (data, status) {
                        console.log(data);
                        //$("#message").html(data.message);
                        $("#guess").val("");
                        $("#num_games_played").html(data.num_games_played);
                        $("#num_attempts").html(data.num_attempts);
                        $("#num_wins").html(data.num_wins);
                        $("#score").html(data.score);
                        if (data.next_round === true) {
                            $("#getClue").prop('disabled', true);
                            $("#guessWord").prop('disabled', true);
                        }
                        Swal.fire({
                            icon: data.icon,
                            title: 'Result',
                            html: data.message,
                        });
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Please type in your answer first.',
                    })
                }
            });


            $("#reset").click(function () {
                Swal.fire({
                    title: 'Another Round?',
                    text: "This will end the current round",
                    icon: 'warning',
                    showDenyButton: true,
                    //showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    denyButtonColor: '#d33',
                    confirmButtonText: 'Yes',
                    denyButtonText: `No`,
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post("<?= base_url('api/reset') ?>",
                        //$.post("<?= base_url('reset') ?>",
                            function (data, status) {
                                $("#clue").html("");
                                $("#num_games_played").html(data.num_games_played);
                                $("#num_attempts").html("0");
                                $("#num_wins").html(data.num_wins);
                                $("#score").html(data.score);
                                $("#mystery_word").html("");
                                $("#category").html("");
                                $("#getCategory").prop('disabled', false);
                                $("#reset").prop('disabled', true);
                                $("#revealWord").prop('disabled', true);
                                $("#getClue").prop('disabled', true);
                                $("#guessWord").prop('disabled', true);
                                $("#startGame").prop('disabled', true);
                                $("#checkAnswer").prop('disabled', true);
                                $("#message").html("");
                                $("#play_round").hide();
                                $("#start_round").show();
                            });
                        
                            $('html, body').animate({
                                scrollTop: $("#game").offset().top,
                                },
                                500
                        );
                    }
                })
            });

            $('#leaderboard').click(function(e) {
                $.ajax({
                    url: '<?= base_url('api/leaderboard') ?>',
                    type: 'post',
                    beforeSend: function () {
                        // Show image container
                        w3_close();
                        $("#loader").show();
                        $("#content").hide();
                    },
                    success: function (data, status) {
                        console.log(data);
                        $("#leaderboard_list").empty();
                        for (const key in data) {
                            //console.log(`${key} : ${data[key]}`);
                            $("#leaderboard_list").append(`<li><span class="w3-text-white">${key} </span><span class="w3-badge w3-blue">${data[key]}</span></li>`);
                        }
                        //$("#leaderboard_list").html(data);
                        $('#leaderboard_modal').show();
                    },
                    complete: function (data) {
                        // Hide image container
                        $("#loader").hide();
                        $("#content").show();
                        $('#leaderboard_modal').show();
                    }
                });
            });
            

            $('#endGame').click(function(e) {
                Swal.fire({
                    title: 'End Game?',
                    text: "Your stats will be reset.",
                    icon: 'warning',
                    //showCancelButton: true,
                    confirmButtonColor: '#d33',
                    denyButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, I quit.',
                    showDenyButton: true,
                    //showCancelButton: true,
                    denyButtonText: `No`,
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.href='<?= base_url('api/end_game') ?>';
                    }
                })
            });

        });
    </script>
</body>

</html>