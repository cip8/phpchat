<template>
    <div id="app" class="flex flex__direction-column">

        <template v-if="!entered">
            <div 
                v-if="error"
                class="error-message text__center">
                {{ this.error }}
            </div>
            
            <form class="login-form" action="#" @submit.prevent="enterChat">
                <fieldset>
                    <label for="user_from">Your username:</label>
                    <input type="text" id="user_from" v-model.trim="user_from">
                </fieldset>
                <fieldset>
                    <label for="user_to">Chat with username:</label>
                    <input type="text" id="user_to" v-model.trim="user_to">
                </fieldset>
                <div class="text__center">
                    <button type="submit">Join chat</button>
                </div>                          
            </form>
        </template>

        <template v-else>
            <message-list />
            <div class="chat-controls flex margin__all-1 margin__left-3 margin__right-3">
                <message-form :user_from="user_from" />
                <recipient :user_to="user_to" />
            </div>
        </template>

    </div> 
</template>

<script>
import { bus } from '../js/event-bus';
import { socket } from '../js/socket';

import MessageForm from './MessageForm';
import MessageList from './MessageList';
import Recipient from './Recipient';

export default {
    name: 'App',
    components: {
        MessageForm,
        MessageList,
        Recipient
    },
    data () {
        return this.initialState();
    },
    methods: {
        isOpen(ws) {
            if (!ws) {
                return false;
            }
            return ws.readyState === ws.OPEN;
        },
        initialState () {
            return {
                user_from: null,
                user_to: null,
                entered: false,
                error: null,
                needsRefresh: false
            }
        },
        enterChat () {
            if (this.needsRefresh) {
                window.location.reload();
            }

            if (!this.user_from || !this.user_to) {
                this.error = 'Please fill in the usernames!';
                return;
            }

            if (!this.isOpen(socket)) {
                this.error = 'Chat server offline - please start it and refresh the page!';
                this.needsRefresh = true;
                return;
            }

            socket.send(JSON.stringify({
                event: 'enter',
                data: {
                    user_from: this.user_from,
                    user_to: this.user_to
                }
            }));

            this.error = null;
            this.entered = true;

            bus.user_from = this.user_from;
        },
        resetData () {
            Object.assign(this.$data, this.initialState());
        }
    },
    mounted () {
        socket.onmessage = (e) => {
            let data = JSON.parse(e.data)
            bus.$emit(data.event, data.data)
        }

        bus.$on('error', (payload) => {
            this.resetData();
            this.error = payload.error;
        });
    }
  
}
</script>

<style lang="scss">
    * {
        box-sizing: border-box;
    }
    body {
        font-size: 16px;
        line-height: 1.25em;
        margin: 0;
    }

    p {
        margin: 0 0 1em 0;
    }

    #app {
        font-family: 'Avenir', Helvetica, Arial, sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        color: #2c3e50;
        height: 100vh;
    }

    .text {
        &__left {
            text-align: left;
        }
        &__center {
            text-align: center;
        }
        &__right {
            text-align: right;
        }
    }

    .flex {
        display: flex;

        &__direction { 
            &-column {
                flex-direction: column;
            }
        }

        &__align {
            &-center {
                align-items: center;
            }
        }

        &__justify {
            &-start {
                justify-content: flex-start;
            }

            &-center {
                justify-content: center;
            }

            &-end {
                justify-content: flex-end;
            }
        }
    }

    .margin {
        &__all {
            &-0 {
                margin: 0 !important;
            }
            &-1 {
                margin: 1em !important;
            }
        }

        &__left {
            &-3 {
                margin-left: 3rem !important;
            }
        }

        &__right {
            &-3 {
                margin-right: 3rem !important;
            }
        }
    }


    .login-form {
        background-color: rgba($color: #46b8ad, $alpha: 0.2);
        width: 20em;
        margin: 3em auto;
        padding: 2em;
        font-size: 1.125em;
        border-radius: 0.25em;
    }

    .error-message {
        background-color: #dd3136;
        color: #FFFFFF;
        padding: 1em;
        font-size: 1.25em;
    }

    fieldset {
        border: none;
        margin: 0 0 1.5em;
        padding: 0;
    }
    label {
        display: block;
        font-weight: bold;
        margin: 0 0 0.25em 0;
    }
    input {
        width: 100%;
        border: 1px solid #3c93d3;
        border-radius: 5px;
        font-size: 1em;
        padding: 0.5em 0.75em;
    }
    button {
        cursor: pointer;
        border: none;
        background-color: #46b8ad;
        color: #FFFFFF;
        font-size: 1.25em;
        padding: 0.25em 0.75em;
        border-radius: 5px;

        &:hover {
            background-color: #2a864c;
        }
    }

    .chat-controls {
        background-color: rgba($color: #ef8938, $alpha: 0.05);
        border: 2px solid #ef8938;
        border-radius: 5px;
    }

</style>