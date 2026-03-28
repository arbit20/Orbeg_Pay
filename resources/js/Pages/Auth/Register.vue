<script setup>
import InputError from '@/Components/InputError.vue';
import AuthSplitLayout from '@/Layouts/AuthSplitLayout.vue';
import { getCsrfHeaders } from '@/lib/csrf';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const form = useForm({
    first_name: '',
    last_name: '',
    name: '',
    username: '',
    birth_date: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const usernameAvailable = ref(null);
const checkingUsername = ref(false);
const suggestions = ref([]);
const loadingSuggestions = ref(false);
const suggestError = ref('');

let checkTimeout = null;

watch(() => form.username, (val) => {
    usernameAvailable.value = null;
    if (checkTimeout) clearTimeout(checkTimeout);

    if (val.length >= 4) {
        checkingUsername.value = true;
        checkTimeout = setTimeout(() => checkUsername(), 500);
    }
});

const checkUsername = async () => {
    try {
        const response = await fetch(route('username.check'), {
            method: 'POST',
            headers: getCsrfHeaders(),
            body: JSON.stringify({ username: form.username }),
        });
        const data = await response.json();
        usernameAvailable.value = response.ok ? data.available : null;
    } catch {
        usernameAvailable.value = null;
    } finally {
        checkingUsername.value = false;
    }
};

const fetchSuggestions = async () => {
    suggestError.value = '';
    loadingSuggestions.value = true;
    try {
        const response = await fetch(route('username.suggest'), {
            method: 'POST',
            headers: getCsrfHeaders(),
        });
        const data = await response.json();
        if (response.ok) {
            suggestions.value = Array.isArray(data.suggestions) ? data.suggestions : [];
        } else {
            suggestions.value = [];
            suggestError.value = data.message || 'Could not load suggestions. Reload the page and try again.';
        }
    } catch {
        suggestions.value = [];
        suggestError.value = 'Connection error. Check your connection and try again.';
    } finally {
        loadingSuggestions.value = false;
    }
};

const selectSuggestion = (username) => {
    form.username = username;
    suggestions.value = [];
};

const submit = () => {
    form.name = `${form.first_name} ${form.last_name}`.replace(/\s+/g, ' ').trim();
    form.password_confirmation = form.password;

    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <AuthSplitLayout>
        <Head title="Sign up" />

        <div class="mx-auto w-full max-w-md">
            <div class="mb-6 flex items-center justify-between gap-4">
                <h1 class="text-4xl font-semibold tracking-tight text-slate-900">
                    Get Started
                </h1>
                <span
                    class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-[#fce38a] via-[#f2c94c] to-[#b8860b] shadow-[0_8px_18px_rgba(184,134,11,0.35)]"
                >
                    <svg
                        class="h-5 w-5 text-[#5d4618]"
                        viewBox="0 0 24 24"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <circle
                            cx="12"
                            cy="12"
                            r="8.5"
                            stroke="currentColor"
                            stroke-width="1.8"
                        />
                        <path
                            d="M9 12H15M10.5 8.5H13.5M10.5 15.5H13.5"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                        />
                    </svg>
                </span>
            </div>

            <div
                class="grid grid-cols-2 rounded-xl bg-[#edf1f7] p-1 text-sm font-medium text-slate-600"
            >
                <Link
                    :href="route('register')"
                    class="rounded-lg bg-white px-4 py-2 text-center text-slate-900 shadow-sm"
                >
                    Sign up
                </Link>
                <Link
                    :href="route('login')"
                    class="rounded-lg px-4 py-2 text-center transition hover:text-slate-900"
                >
                    Sign in
                </Link>
            </div>

            <form class="space-y-4" @submit.prevent="submit">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label
                            for="first_name"
                            class="text-sm font-medium text-slate-700"
                            >First name</label
                        >
                        <input
                            id="first_name"
                            v-model="form.first_name"
                            type="text"
                            required
                            autofocus
                            autocomplete="given-name"
                            class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 focus:border-[#3d5ad8] focus:ring-[#3d5ad8]"
                        />
                    </div>

                    <div>
                        <label
                            for="last_name"
                            class="text-sm font-medium text-slate-700"
                            >Last name</label
                        >
                        <input
                            id="last_name"
                            v-model="form.last_name"
                            type="text"
                            required
                            autocomplete="family-name"
                            class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 focus:border-[#3d5ad8] focus:ring-[#3d5ad8]"
                        />
                    </div>
                </div>
                <InputError
                    class="mt-2"
                    :message="
                        form.errors.name ||
                        form.errors.first_name ||
                        form.errors.last_name
                    "
                />

                <div>
                    <label for="birth_date" class="text-sm font-medium text-slate-700"
                        >Date of birth</label
                    >
                    <input
                        id="birth_date"
                        v-model="form.birth_date"
                        type="date"
                        required
                        class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 focus:border-[#3d5ad8] focus:ring-[#3d5ad8]"
                    />
                    <InputError class="mt-2" :message="form.errors.birth_date" />
                </div>

                <div>
                    <label for="username" class="text-sm font-medium text-slate-700"
                        >Username</label
                    >
                    <div class="relative">
                        <input
                            id="username"
                            v-model="form.username"
                            type="text"
                            required
                            placeholder="e.g. swift_trader42"
                            class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-3.5 py-2.5 pr-10 text-sm text-slate-800 placeholder:text-slate-400 focus:border-[#3d5ad8] focus:ring-[#3d5ad8]"
                        />
                        <div v-if="checkingUsername" class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="h-5 w-5 animate-spin text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                            </svg>
                        </div>
                        <div v-else-if="usernameAvailable === true && form.username.length >= 4" class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div v-else-if="usernameAvailable === false" class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                    </div>
                    <p v-if="usernameAvailable === true && form.username.length >= 4" class="mt-1 text-xs text-green-600">Username available</p>
                    <p v-if="usernameAvailable === false" class="mt-1 text-xs text-red-500">Username already taken</p>

                    <div class="mt-1">
                        <button
                            type="button"
                            @click="fetchSuggestions"
                            :disabled="loadingSuggestions"
                            class="text-xs text-[#3150cf] hover:text-[#2743b6] underline"
                        >
                            {{ loadingSuggestions ? 'Generating...' : 'Suggest random usernames' }}
                        </button>
                        <div v-if="suggestions.length" class="mt-2 flex flex-wrap gap-2">
                            <button
                                v-for="s in suggestions"
                                :key="s"
                                type="button"
                                @click="selectSuggestion(s)"
                                class="rounded-full bg-[#edf1f7] px-3 py-1 text-xs font-medium text-[#3150cf] hover:bg-[#dde3ef] transition"
                            >
                                {{ s }}
                            </button>
                        </div>
                        <p v-if="suggestError" class="mt-2 text-xs text-red-600">{{ suggestError }}</p>
                    </div>

                    <InputError class="mt-2" :message="form.errors.username" />
                </div>

                <div>
                    <label for="email" class="text-sm font-medium text-slate-700"
                        >Email</label
                    >
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        required
                        autocomplete="username"
                        class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 focus:border-[#3d5ad8] focus:ring-[#3d5ad8]"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div>
                    <label
                        for="password"
                        class="text-sm font-medium text-slate-700"
                        >Password</label
                    >
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        required
                        autocomplete="new-password"
                        class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 focus:border-[#3d5ad8] focus:ring-[#3d5ad8]"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                    <div
                        class="mt-2 grid grid-cols-3 gap-2 text-center text-[11px] text-slate-500"
                    >
                        <span class="rounded-md bg-slate-100 px-2 py-1"
                            >Min 8 chars</span
                        >
                        <span class="rounded-md bg-slate-100 px-2 py-1"
                            >1 number</span
                        >
                        <span class="rounded-md bg-slate-100 px-2 py-1"
                            >Mixed case</span
                        >
                    </div>
                </div>

                <p class="text-xs text-slate-500">
                    By signing up, you agree to our
                    <span class="text-[#3150cf]">Terms</span>
                    and
                    <span class="text-[#3150cf]">Privacy Policy</span>.
                </p>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full rounded-lg bg-[#3150cf] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#2743b6] focus:outline-none focus:ring-2 focus:ring-[#3150cf] focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-70"
                >
                    Sign up
                </button>
            </form>
        </div>
    </AuthSplitLayout>
</template>
