<template>

<div
        class="flex min-h-full items-center justify-center py-12 px-4 sm:px-6 lg:px-8"
    >
        <div class="w-full max-w-md space-y-8">
            <div>
                <img
                    class="mx-auto h-12 w-auto"
                    src="https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=600"
                    alt="Your Company"
                />
                <h2
                    class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900"
                >
                    Register in the Network
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Or
                    <router-link
                        :to="{ name: 'user.login' }"
                        class="font-medium text-indigo-600 hover:text-indigo-500"
                    >sign in</router-link>
                </p>
            </div>
            <form class="mt-8 space-y-6">
                <input type="hidden" name="remember" value="true" />
                <div class="-space-y-px rounded-md shadow-sm">
                    <div>
                        <label for="name" class="sr-only"
                            >Name</label
                        >
                        <input
                        v-model="name"
                            id="name"
                            type="text"
                            required=""
                            class="relative block w-full appearance-none rounded-none rounded-t-md border border-gray-300 px-3 py-2 text-gray-900 placeholder-gray-500 focus:z-10 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
                            placeholder="Name"
                        />
                    </div>
                    <div>
                        <label for="email-address" class="sr-only"
                            >Email address</label
                        >
                        <input
                            v-model="email"
                            id="email-address"
                            type="email"
                            autocomplete="email"
                            required=""
                            class="relative block w-full appearance-none rounded-none border border-gray-300 px-3 py-2 text-gray-900 placeholder-gray-500 focus:z-10 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
                            placeholder="Email address"
                        />
                    </div>
                    <div>
                        <label for="password" class="sr-only">Password</label>
                        <input
                            v-model="password"
                            id="password"
                            type="password"
                            required=""
                            class="relative block w-full appearance-none rounded-none border border-gray-300 px-3 py-2 text-gray-900 placeholder-gray-500 focus:z-10 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
                            placeholder="Password"
                        />
                    </div>
                    <div>
                        <label for="password_confirmation" class="sr-only">Password</label>
                        <input
                            v-model="password_confirmation"
                            id="password_confirmation"
                            type="password"
                            required=""
                            class="relative block w-full appearance-none rounded-none rounded-b-md border border-gray-300 px-3 py-2 text-gray-900 placeholder-gray-500 focus:z-10 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
                            placeholder="Confirm Password"
                        />
                    </div>
                </div>
                <div>
                    <button
                        @click.prevent="register"
                        type="submit"
                        class="group relative flex w-full justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        <span
                            class="absolute inset-y-0 left-0 flex items-center pl-3"
                        >
                            <LockClosedIcon
                                class="h-5 w-5 text-indigo-500 group-hover:text-indigo-400"
                                aria-hidden="true"
                            />
                        </span>
                        Register
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import { LockClosedIcon } from "@heroicons/vue/20/solid";

export default {
    name: "Registration",

    data() {
        return {
            name: null,
            email: null,
            password: null,
            password_confirmation: null,
        };
    },

    methods: {
        register() {
            axios.get("/sanctum/csrf-cookie").then((response) => {
                axios
                    .post("/register", {
                        name: this.name,
                        email: this.email,
                        password: this.password,
                        password_confirmation: this.password_confirmation,
                    })
                    .then((res) => {
                        localStorage.setItem(
                            "x_xsrf_token",
                            res.config.headers["X-XSRF-TOKEN"]
                        );
                        this.$router.push({ name: "user.personal" });
                    });
            });
        },
    },

    components: {
        LockClosedIcon,
    },
};
</script>

<style scoped></style>
