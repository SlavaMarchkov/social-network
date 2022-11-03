<template>
    <div class="w-96 mx-auto mb-10 overflow-hidden">
        <Statistics :statistics="statistics"></Statistics>
        <form @submit.prevent="store">
            <div class="mb-3">
                <label
                    for="title"
                    class="block text-sm font-medium text-gray-700"
                    >Title</label
                >
                <input
                    v-model="title"
                    type="text"
                    name="title"
                    id="title"
                    autocomplete="title"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                />
                <div v-if="errors.title">
                    <div v-for="error in errors.title" :key="error" class="text-red-600 text-sm">
                        {{ error }}
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label
                    for="content"
                    class="block text-sm font-medium text-gray-700"
                    >Content</label
                >
                <div class="mt-1">
                    <textarea
                        v-model="content"
                        id="content"
                        name="content"
                        rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        placeholder="you@example.com"
                    />
                    <div v-if="errors.content">
                    <div v-for="error in errors.content" :key="error" class="text-red-600 text-sm">
                        {{ error }}
                    </div>
                </div>
                </div>
            </div>
            <div class="flex justify-between mb-3">
                <div>
                    <input
                        @change="storeImage"
                        ref="file"
                        type="file"
                        class="hidden"
                    />
                    <a
                        href="#"
                        class="w-32 p-2 bg-sky-400 text-white text-sm rounded-md"
                        @click.prevent="selectFile()"
                        >Choose image</a
                    >
                </div>
                <div v-if="image">
                    <a
                        @click.prevent="image = null"
                        href="#"
                        class="w-32 p-2 bg-slate-200 text-sm rounded-md"
                        >Cancel</a
                    >
                </div>
            </div>
            <div v-if="image" class="mb-3">
                <img :src="image.url" alt="#" />
            </div>
            <div class="float-right">
                <button
                    type="submit"
                    class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Save
                </button>
            </div>
        </form>
    </div>
    <div v-if="posts" class="w-96 mx-auto mb-10">
        <h1 class="text-xl my-3 border-b border-gray-400">My Own Posts</h1>
        <Post v-for="post in posts" :key="post.id" :post="post"></Post>
    </div>
</template>

<script>
import axios from "axios";
import Post from "../../components/Post.vue";
import Statistics from "../../components/Statistics.vue";

export default {
    name: "Personal",

    data() {
        return {
            title: "",
            content: "",
            image: null,
            posts: [],
            errors: [],
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
                id: null,
            }).then((response) => {
                this.statistics = response.data.data;
            }).catch((err) => {
                console.log(err);
            });
        },

        getPosts() {
            axios.get("/api/posts").then((response) => {
                this.posts = response.data.data;
            });
        },

        store() {
            const id = this.image ? this.image.id : null;
            axios
                .post("/api/posts", {
                    title: this.title,
                    content: this.content,
                    image_id: id,
                })
                .then((response) => {
                    this.title = "";
                    this.content = "";
                    this.image = null;
                    this.posts.unshift(response.data.data);
                })
                .catch((err) => {
                    this.errors = err.response.data.errors;
                });
        },

        selectFile() {
            this.fileInput = this.$refs.file;
            this.fileInput.click();
        },

        storeImage(evt) {
            const file = evt.target.files[0];
            const formdata = new FormData();
            formdata.append("image", file);
            axios
                .post("/api/postImages", formdata, {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                })
                .then((response) => {
                    this.image = response.data.data;
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
