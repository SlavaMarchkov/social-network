<template>
    <div class="w-96 mx-auto mb-10">
        <Statistics :statistics="statistics"></Statistics>
        <h1 class="text-xl my-3 border-b border-gray-400">User Posts</h1>
        <div v-if="posts.length > 0">
            <Post v-for="post in posts" :key="post.id" :post="post"></Post>
        </div>
        <div v-else>
            <p>User has no posts yet.</p>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import Post from "../../components/Post.vue";
import Statistics from '../../components/Statistics.vue';

export default {
    name: "Show",

    data() {
        return {
            userId: parseInt(this.$route.params.id),
            posts: [],
            statistics: [],
        };
    },

    mounted() {
        this.getPosts();
        this.getStatistics();
    },

    methods: {

        getStatistics() {
            axios.post('/api/users/statistics', {
                user_id: this.userId,
            }).then((response) => {
                this.statistics = response.data.data;
            }).catch((err) => {
                console.log(err);
            });
        },

        getPosts() {
            axios
                .get(`/api/users/${this.userId}/posts`)
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
        Statistics,
    },
};
</script>

<style scoped></style>
