<template>
    <div class="w-96 mx-auto mb-10">
        <h1 class="text-xl my-3 border-b border-gray-400">My Feed</h1>
        <div v-if="posts.length > 0">
            <Post v-for="post in posts" :key="post.id" :post="post"></Post>
        </div>
        <div v-else>
            <p>No posts in feed yet. Go to Users page and click Follow.</p>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import Post from "../../components/Post.vue";

export default {
    name: "Feed",

    data() {
        return {
            posts: [],
        };
    },

    mounted() {
        this.getPosts();
    },

    methods: {
        getPosts() {
            axios
                .get('/api/users/feed')
                .then((response) => {
                    this.posts = response.data.data;
                })
                .catch((err) => {
                    console.log(err.message);
                });
        },
    },

    components: {
        Post,
    },
};
</script>

<style scoped></style>
