<template>
    <div class="mb-6 pb-6 border-b border-gray-400">
        <h1 class="text-xl">{{ post.title }}</h1>
        <router-link
            class="text-sm text-indigo-600"
            :to="{ name: 'user.show', params: { id: post.user.id } }"
            >Author: {{ post.user.name }}</router-link
        >
        <div v-if="post.image_url" class="my-3">
            <img :src="post.image_url" :alt="post.title" />
        </div>
        <p>{{ post.content }}</p>
        <div v-if="post.reposted_post" class="bg-gray-200 py-3 mt-3">
            <h1 class="text-xl">{{ post.reposted_post.title }}</h1>
            <router-link
                class="text-sm text-indigo-600"
                :to="{
                    name: 'user.show',
                    params: { id: post.reposted_post.user.id },
                }"
                >Author: {{ post.reposted_post.user.name }}</router-link
            >
            <div v-if="post.reposted_post.image_url" class="my-3">
                <img
                    :src="post.reposted_post.image_url"
                    :alt="post.reposted_post.title"
                />
            </div>
            <p>{{ post.reposted_post.content }}</p>
        </div>
        <div class="flex justify-between items-center mt-3">
            <div class="flex">
                <svg
                    @click.prevent="toggleLike(post)"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    :class="[
                        'stroke-indigo-600 hover:fill-sky-400 cursor-pointer w-6 h-6 mr-2',
                        post.is_liked ? 'fill-indigo-600' : 'fill-white',
                    ]"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"
                    />
                </svg>
                {{ post.likes_count }}
                <div class="flex ml-3">
                    <svg
                        @click.prevent="openRepost"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        :class="[
                            'stroke-indigo-600 hover:fill-sky-400 cursor-pointer w-6 h-6 mr-2',
                        ]"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z"
                        />
                    </svg>
                    {{ post.reposted_by_posts_count }}
                </div>
            </div>
            <p class="mt-2 text-right text-sm">{{ post.date }}</p>
        </div>
        <div v-if="isRepost" class="mt-5 overflow-hidden">
            <form @submit.prevent="repost(post)">
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
                        <div
                            v-for="error in errors.title"
                            :key="error"
                            class="text-red-600 text-sm"
                        >
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
                            <div
                                v-for="error in errors.content"
                                :key="error"
                                class="text-red-600 text-sm"
                            >
                                {{ error }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="float-right">
                    <button
                        type="submit"
                        class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Repost
                    </button>
                </div>
            </form>
        </div>
        <div v-if="post.comments_count > 0" class="mt-3">
            <p
                v-if="!isCommentBlockShown"
                @click="getComments(post)"
                class="cursor-pointer"
            >
                Show {{ post.comments_count }} comments
            </p>
            <p
                v-else
                @click="isCommentBlockShown = false"
                class="cursor-pointer"
            >
                Close
            </p>
            <div v-if="comments.length > 0 && isCommentBlockShown">
                <div
                    v-for="comment in comments"
                    :key="comment.id"
                    class="mt-3 pt-3 border-t border-gray-200"
                >
                    <div class="flex">
                        <p class="text-sm mr-2">{{ comment.user.name }}</p>
                        <p
                            @click="setParentId(comment)"
                            class="text-sm text-indigo-600 cursor-pointer"
                        >
                            Answer
                        </p>
                    </div>
                    <p>
                        <span
                            v-if="comment.answered_for_user"
                            class="text-indigo-600"
                            >{{ comment.answered_for_user }},
                        </span>
                        {{ comment.content }}
                    </p>
                    <p class="text-right text-sm">{{ comment.date }}</p>
                </div>
            </div>
        </div>
        <div class="mt-5 overflow-hidden">
            <form @submit.prevent="storeComment(post)">
                <div class="flex items-center">
                    <p v-if="comment" class="mr-2">
                        Answered for {{ comment.user.name }}
                    </p>
                    <p
                        v-if="comment"
                        @click="comment = null"
                        class="text-sm text-indigo-600 cursor-pointer"
                    >
                        Cancel
                    </p>
                </div>
                <div class="mb-3">
                    <label
                        for="body"
                        class="block text-sm font-medium text-gray-700"
                        >Comment</label
                    >
                    <div class="mt-1">
                        <textarea
                            v-model="body"
                            id="body"
                            name="body"
                            rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="your comment"
                        />
                    </div>
                </div>
                <div class="float-right">
                    <button
                        type="submit"
                        class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Comment
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        post: {
            type: Object,
            default: {},
        },
    },

    data() {
        return {
            title: "",
            content: "",
            body: "",
            isRepost: false,
            errors: [],
            comments: [],
            isCommentBlockShown: false,
            comment: null,
        };
    },

    computed: {
        isPersonal() {
            return this.$route.name === "user.personal";
        },
    },

    methods: {
        toggleLike(post) {
            axios
                .post(`/api/posts/${post.id}/toggle_like`)
                .then((response) => {
                    post.is_liked = response.data.is_liked;
                    post.likes_count = response.data.likes_count;
                })
                .catch((err) => {
                    console.log(err.message);
                });
        },

        setParentId(comment) {
            this.comment = comment;
        },

        storeComment(post) {
            const commentId = this.comment ? this.comment.id : null;
            axios
                .post(`/api/posts/${post.id}/comment`, {
                    content: this.body,
                    parent_id: commentId,
                })
                .then((response) => {
                    this.body = "";
                    this.comment = null;
                    this.comments.unshift(response.data.data);
                    post.comments_count++;
                    this.isCommentBlockShown = true;
                })
                .catch((err) => {
                    console.log(err.message);
                });
        },

        getComments(post) {
            axios
                .get(`/api/posts/${post.id}/comments`)
                .then((response) => {
                    this.comments = response.data.data;
                    this.isCommentBlockShown = true;
                })
                .catch((err) => {
                    console.log(err.message);
                });
        },

        openRepost() {
            if (this.isPersonal) return;
            this.isRepost = !this.isRepost;
        },

        repost(post) {
            if (this.isPersonal) return;
            axios
                .post(`/api/posts/${post.id}/repost`, {
                    title: this.title,
                    content: this.content,
                })
                .then((response) => {
                    this.title = "";
                    this.content = "";
                })
                .catch((err) => {
                    this.errors = err.response.data.errors;
                });
        },
    },
};
</script>
