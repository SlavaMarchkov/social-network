<template>
    <div class="flex justify-between text-center text-sm mb-8">
        <div>
            Subcribers: <div>{{ statistics.subscribers_count }}</div>
        </div>
        <div>
            Likes: <div>{{ statistics.likes_count }}</div>
        </div>
        <div>
            Posts: <div>{{ statistics.posts_count }}</div>
        </div>
        <div>
            Followings: <div>{{ statistics.followings_count }}</div>
        </div>
    </div>
</template>

<script>
export default {
    props: [
        'statistics',
    ],

    data() {
        return {

        };
    },

    methods: {

    },
}
</script>
