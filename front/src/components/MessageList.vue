<template>
    <div class="message-list margin__all-1 margin__left-3 margin__right-3">
        <message 
            v-for="message in messages" 
            :key="message.id" 
            :message="message" />
    </div>   
</template>

<script>
import { bus } from '../js/event-bus.js';

import Message from './Message';

export default {
    name: 'MessageList',
    components: {
        Message
    },
    data () {
        return {
            messages: []
        }
    },
    mounted () {
        // fetch chat messages
        bus.$on('history', (payload) => {
            this.messages = payload;
        });

        bus.$on('message', (payload) => {
            this.messages.unshift(payload);
        });

    }    
}
</script>

<style lang="scss" scoped>
    .message-list {
        flex-grow: 1;
        overflow-y: scroll;
        border: 2px solid #CCCCCC;
        padding: 1.5em;
        font-size: 1.125em;
        border-radius: 5px;
    }
</style>