<template>
    <div class="row justify-content-center" style="margin-top:50px;">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Tic-Tac-Toe # Status:<span>{{state}}</span></div>
                <div class="card-body">
                    <div v-if="gameUid && !loading && players">
                        <div class="row" style="margin-top:25px; margin-bottom:25px;">
                            <div class="col-12 text-center">
                                <b>{{players['X'].name}} (X)</b> VS <b>{{players['O'].name}} (O)</b>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <button id="1-1" @click="clickBox" class="col-3 grid-cell d-flex align-items-center justify-content-center"></button>
                            <button id="1-2" @click="clickBox" class="col-3 grid-cell d-flex align-items-center justify-content-center"></button>
                            <button id="1-3" @click="clickBox" class="col-3 grid-cell d-flex align-items-center justify-content-center"></button>
                        </div>
                        <div class="row justify-content-center">
                            <button id="2-1" @click="clickBox" class="col-3 grid-cell d-flex align-items-center justify-content-center"></button>
                            <button id="2-2" @click="clickBox" class="col-3 grid-cell d-flex align-items-center justify-content-center"></button>
                            <button id="2-3" @click="clickBox" class="col-3 grid-cell d-flex align-items-center justify-content-center"></button>
                        </div>
                        <div class="row justify-content-center">
                            <button id="3-1" @click="clickBox" class="col-3 grid-cell d-flex align-items-center justify-content-center"></button>
                            <button id="3-2" @click="clickBox" class="col-3 grid-cell d-flex align-items-center justify-content-center"></button>
                            <button id="3-3" @click="clickBox" class="col-3 grid-cell d-flex align-items-center justify-content-center"></button>
                        </div>
                        <div v-if="!loading">
                            <div class="row" style="margin-top: 25px;">
                                <div class="col-12 text-center">
                                    <p><button @click="checkForCurrentPlayer" class="btn btn-primary">New Game</button></p>
                                    <p><button @click="clearCurrentPlayer" class="btn btn-warning">New Player</button></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else-if="!loading">
                        <form @submit.prevent="newPlayer">
                            <div class="row">
                                <div class="col">
                                    <p>Welcome new user, please enter your name to play Tic-Tac-Toe!</p>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col">
                                    <div class="form-group" >
                                        <input v-model="newPlayerName" :class="{'is-invalid': nameValidationErrors}" type="text" class="form-control" placeholder="New Player Name">
                                        <div v-for="error in nameValidationErrors" class="invalid-feedback">
                                           {{error}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <button class="btn btn-xs btn-success" type="submit">
                                        Go
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">High Scores</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <ul>
                                <li v-for="player in highScores">{{player.name}} -> {{player.wins}}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';
    import $ from 'jquery';

    export default {
        data: function() {
            return {
                loading: true,
                state: false,
                gameUid: null,
                errors: [],
                players: null,
                turn: 'X',
                newPlayerName: null,
                nameValidationErrors: null,
                highScores: null
            }
        },
        mounted: function() {
            this.checkForCurrentPlayer();
            this.getHighScores();
        },
        methods: {
            clearCurrentPlayer() {
                this.loading = true;
                axios.get('/api/clearcurrentplayer')
                    .then(response => {
                        this.state = false;
                        this.gameUid = null;
                        this.players = null;
                        this.turn = 'X';
                        this.newPlayerName = null;
                        this.nameValidationErrors = null
                    })
                    .catch(error => {
                        this.errors.push(error.message);
                    });
            },
            clickBox: function (event) {
                if (this.state !== 'winner' || this.state !== 'draw') {
                    this.setPlayerMove(event.target.id, 'X');
                }
            },
            checkForCurrentPlayer: function () {
                axios.get('/api/checkforcurrentplayer')
                    .then(response => {
                        if (response.data.player) {
                            this.newGame(response.data.player.uid);
                        }
                        else {
                            this.loading = false;
                        }
                    })
                    .catch(error => {
                        this.errors.push(error.message);
                    });
            },
            newPlayer: function () {
                axios.post('/api/newplayer', {
                    name: this.newPlayerName
                    })
                    .then(response => {
                        this.newGame(response.data.player.uid);
                    })
                    .catch(error => {
                        this.errors.push(error.message);
                        if (error.response.status === 422){
                            this.nameValidationErrors = error.response.data.errors['name'];
                        }
                    });
            },
            newGame: function (uid) {
                this.clearBoard();
                this.getHighScores();
                axios.post('/api/newgame', {
                        uid: uid
                    })
                    .then(response => {
                        this.state = response.data.state;
                        this.gameUid = response.data.gameUid;
                        this.players = response.data.players;
                        this.loading = false;
                    })
                    .catch(error => {
                        this.errors.push(error.message);
                    });
            },
            getHighScores() {
                axios.get('/api/gethighscores')
                    .then(response => {
                        this.highScores = response.data;
                    })
                    .catch(error => {
                        this.errors.push(error.message);
                    });
            },
            setPlayerMove(rowcell) {
                axios.post('/api/setplayermove', {
                    rowcell: rowcell,
                    gameuid: this.gameUid,
                    value: 'X'
                    })
                    .then(response => {
                        this.state = response.data.state;
                        if (response.data.moveX) {
                            this.updateBoard(response.data.move, 'X');
                        }
                        if (response.data.moveO) {
                            this.updateBoard(response.data.move, 'O');
                        }
                    })
                    .catch(error => {
                        this.errors.push(error.message);
                    });
            },
            updateBoard(rowcell, value) {
                this.checkIfdone();

                if (rowcell || value) {
                    let $target = $('#' + rowcell);
                    $target.attr("disabled", true);
                    $target.html(value);
                }
            },
            clearBoard()
            {
                $('.grid-cell').attr("disabled", false);
                $('.grid-cell').html('');
            },
            checkIfdone() {
                if (this.state === 'winner' || this.state === 'draw') {
                    $('.grid-cell').attr("disabled", true);
                }
                if (this.state === 'winner') {
                    let player = this.players[this.turn];
                    alert('And the winner is:' + player.name);
                }
                else if (this.state === 'draw') {
                    alert('Oh boi its a draw!');

                }
                if (this.turn === 'X')
                {
                    this.turn = 'O';
                }
                else {
                    this.turn = 'X';
                }
            }
        },
    }
</script>
