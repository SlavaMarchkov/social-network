<template>
    <div class="w-96 mx-auto mb-10">
        <div v-if="users.length > 0">
            <div v-for="user in users" :key="user.id">
                <div
                    class="flex justify-between items-center mb-6 pb-6 border-b border-gray-400"
                >
                    <router-link
                        :to="{ name: 'user.show', params: { id: user.id } }"
                    >
                        <p>ID: {{ user.id }}</p>
                        <p>Name: {{ user.name }}</p>
                        <p>Email: {{ user.email }}</p>
                    </router-link>
                    <a
                        href="#"
                        :class="[
                            'w-32 p-2 text-sm rounded-md text-center',
                            user.is_followed
                                ? 'bg-white text-indigo-600 border border-indigo-600'
                                : 'bg-indigo-600 text-white',
                        ]"
                        @click.prevent="toggleFollowing(user)"
                        >{{ user.is_followed ? "Unfollow" : "Follow" }}</a
                    >
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Index",

    data() {
        return {
            users: [],
        };
    },

    mounted() {
        this.getUsers();
    },

    methods: {
        getUsers() {
            axios
                .get("/api/users")
                .then((response) => (this.users = response.data.data));
        },
        toggleFollowing(user) {
            axios
                .post(`/api/users/${user.id}/toggle_following`)
                .then((response) => {
                    user.is_followed = response.data.is_followed;
                })
                .catch((err) => {
                    console.log(err.message);
                });
        },
    },
};
</script>

<style scoped></style>
