<template>
    <form action="#" class="chat-form flex flex__direction-column">
        <textarea 
            class="chat-form__input" 
            placeholder="Type your message" 
            v-model="content" 
            @keydown="handleInput">
        </textarea>
        <pre class="text__right">Press Shift + Enter to add a new line.</pre>
    </form>
</template>

<script>
import { bus } from '../js/event-bus';
import { socket } from '../js/socket';

export default {
    name: 'MessageForm',
    data () {
        return {
            content: ''
        }
    },
    props: {
        user_from: {
            type: String,
            required: true                        
        }
    },
    methods: {
        handleInput (e) {
            if (e.keyCode === 13 && e.shiftKey === false) {
                e.preventDefault();
                e.stopPropagation();

                this.send();
            }
        },
        send () {
            let payload = {
                event: 'message',
                data: {
                    id: Date.now(),
                    content: this.content,
                    user_from: this.user_from
                }
            }

            bus.$emit('message', payload.data);

            socket.send(JSON.stringify(payload));

            this.content = null;
        }
    }
}
</script>

<style lang="scss" scoped>
    .chat-form {
        flex: 1;
        padding: 0.5em;
        font-size: 1.125em;

        textarea {
            flex: 1;
            background: transparent;
            resize: none;
            margin: 0;
            font-size: 1.125em;
            width: 100%;
            height: 3em;
            padding: 0.25em 0.5em;
            outline: none;
            border: none;
        }

        pre {
            font-size: 0.75em;
            margin: 0;
        }
    }
</style>