<template>
    <div class="row justify-content-center" style="margin-top:50px;">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Tic-Tac-Toe #<span>{{state}}</span></div>
                <div class="card-body">
                    <div v-if="gameUid">
                        <div class="row justify-content-center">
                            <button id="1-1" @click="clickBox" class="col-2 grid-cell d-flex align-items-center justify-content-center"></button>
                            <button id="1-2" @click="clickBox" class="col-2 grid-cell d-flex align-items-center justify-content-center"></button>
                            <button id="1-3" @click="clickBox" class="col-2 grid-cell d-flex align-items-center justify-content-center"></button>
                        </div>
                        <div class="row justify-content-center">
                            <button id="2-1" @click="clickBox" class="col-2 grid-cell d-flex align-items-center justify-content-center"></button>
                            <button id="2-2" @click="clickBox" class="col-2 grid-cell d-flex align-items-center justify-content-center"></button>
                            <button id="2-3" @click="clickBox" class="col-2 grid-cell d-flex align-items-center justify-content-center"></button>
                        </div>
                        <div class="row justify-content-center">
                            <button id="3-1" @click="clickBox" class="col-2 grid-cell d-flex align-items-center justify-content-center"></button>
                            <button id="3-2" @click="clickBox" class="col-2 grid-cell d-flex align-items-center justify-content-center"></button>
                            <button id="3-3" @click="clickBox" class="col-2 grid-cell d-flex align-items-center justify-content-center"></button>
                        </div>
                    </div>
                    <div v-else>
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
    </div>
</template>

<script>
    import axios from 'axios';
    import $ from 'jquery';

    export default {
        data: function() {
            return {
                state: false,
                gameUid: null,
                errors: [],
                players: [],
                turn: 'X',
                newPlayerName: null,
                nameValidationErrors: null,
            }
        },
        mounted: function() {
            this.checkForCurrentPlayer();
        },
        methods: {
            clickBox: function (event) {
                this.updateBoard(event.target.id, 'X');
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
                    })
                    .catch(error => {
                        this.errors.push(error.message);
                    })
            },
            newPlayer: function () {
                axios.post('/api/newplayer', {
                    name: this.newPlayerName
                    })
                    .then(response => {

                    })
                    .catch(error => {
                        this.errors.push(error.message);
                        if (error.response.status === 422){
                            this.nameValidationErrors = error.response.data.errors['name'];
                        }
                    })
            },
            newGame: function (uid) {
                axios.post('/api/newgame', {
                        uid: uid
                    })
                    .then(response => {
                        this.state = response.data.state;
                        this.gameUid = response.data.gameUid;
                    })
                    .catch(error => {
                        this.errors.push(error.message);
                    })
            },
            setPlayerMove(rowcell) {
                axios.post('/api/setplayermove', {
                    rowcell: rowcell,
                    value: 'X'
                    })
                    .then(response => {
                        this.state = response.data.state;
                        if (response.data.move) {
                            this.updateBoard(response.data.move, 'O');
                        }
                    })
                    .catch(error => {
                        this.errors.push(error.message);
                    })
            },
            updateBoard(rowcell, value) {
                if (rowcell || value) {
                    let $target = $('#' + rowcell);
                    $target.attr("disabled", true);

                    $target.html(value);
                }
                this.checkIfdone();


            },
            checkIfdone()
            {
                if (this.turn === 'X')
                {
                    this.turn = 'O';
                }
                else {
                    this.turn = 'X';
                }

                if (this.state === 'winner' || this.state === 'draw')
                {
                    $('.grid-cell').attr("disabled", true);
                    let player = this.getPlayerBasedOnId(this.turn);
                    alert('And the winner is:' + player.name);
                }
            },
            getPlayerBasedOnId(id) {
                let player = this.players.find(function (search) {
                    return search.id === id;
                });

                return player;
            }
        },
    }
</script>
