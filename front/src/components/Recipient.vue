<template>
    <div class="chat-people flex flex__direction-column flex__align-center">
        <p>Chatting with:</p>
        <p class="margin__all-0">
            <span class="chat-people__username">{{ user_to }}</span>
            <span class="chat-people__status chat-people__status-online" v-if="online">online</span>
            <span class="chat-people__status" v-else>offline</span>
        </p>
    </div>    
</template>

<script>
import { bus } from '../js/event-bus';

export default {
    name: 'Recipient',
    data () {
        return {
            online: false
        };
    },
    props: {
        user_to: {
            type: String,
            required: true
        }
    },
    mounted () {
        bus.$on('enter', (payload) => {
            this.online = payload.username === this.user_to;
            
            if (payload.message) {
                bus.$emit('message', {
                    id: Date.now(),
                    content: '<strong>' + payload.username + '</strong> ' + payload.message
                });
            }
        });

        bus.$on('exit', (payload) => {
            this.online = payload.username !== this.user_to;

            if (payload.message) {
                bus.$emit('message', {
                    id: Date.now(),
                    content: '<strong>' + payload.username + '</strong> ' + payload.message
                });
            }
        })
    }
}
</script>

<style lang="scss" scoped>
    .chat-people {
        padding: 2em;
        font-size: 1em;
        background-color: rgba($color: #ef8938, $alpha: 0.25);

        &__username {
            font-size: 1.125em;
            font-weight: bold;
            margin: 0 0.4em 0 0;
        }

        &__status {
            font-size: 0.875em;
            background-color: rgba($color: #000000, $alpha: 0.1);
            padding: 0 0.25em;
            
            &-online {
                background-color: rgba($color: #8bcc5f, $alpha: 0.4);
                &:after {
                    content: "";
                    border-radius: 50%;
                    background-color: rgba($color: #2a864c, $alpha: 1);
                    width: 8px;
                    height: 8px;
                    display: inline-block;
                    margin: 0 0 0 0.25em;
                }
            }
        }
    }
</style>    