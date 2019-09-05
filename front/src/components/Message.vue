<template>
    <div 
        class="chat-messages__item flex"
        :class="fromThisUser">
        <div class="chat-wrapper">
            <div class="chat-wrapper__username" v-if="message.user_from">
                @{{ message.user_from }}:
            </div>
            <div v-html="message.content" />
        </div>
    </div>   
</template>

<script>
import { bus } from '../js/event-bus';

export default {
    name: 'Message',
    props: {
        message: {
            type: Object,
            required: true
        }
    },
    computed: {
        fromThisUser () {
            if (this.message.user_from === bus.user_from) {
                return 'user-1 flex__justify-end';
            } else if (this.message.user_from) {
                return 'user-2 flex__justify-start';
            } else {
                return 'admin-notification text__center'
            }
        }
    }
}
</script>

<style lang="scss" scoped>
    .chat-messages__item {
        margin: 0 0 1em 0;
    }

    .chat-wrapper {
        padding: 0.5em 0.875em;
        border-radius: 5px;

        &__username {
            font-weight: bold;
            margin: 0 0 0.25em 0;
        }
    }

    .user-1 {
        .chat-wrapper {
            background-color: rgba($color: #65b659, $alpha: 0.2);
        }
    }

    .user-2 {
        .chat-wrapper {
            background-color: rgba($color: #f3c94d, $alpha: 0.2);
        }
    }

    .admin-notification {
        .chat-wrapper {
            width: 100%;
            background-color: rgba($color: #000000, $alpha: 0.07);
            color: rgba($color: #000000, $alpha: 0.5);
        }
    }
</style>