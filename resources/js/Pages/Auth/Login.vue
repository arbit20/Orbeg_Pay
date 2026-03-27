<script setup>
import InputError from '@/Components/InputError.vue';
import AuthSplitLayout from '@/Layouts/AuthSplitLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <AuthSplitLayout>
        <Head title="Sign in" />

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
                v-if="status"
                class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-700"
            >
                {{ status }}
            </div>

            <form class="space-y-4" @submit.prevent="submit">
                <div>
                    <label for="email" class="text-sm font-medium text-slate-700"
                        >Email</label
                    >
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        required
                        autofocus
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
                        autocomplete="current-password"
                        class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 focus:border-[#3d5ad8] focus:ring-[#3d5ad8]"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="flex items-center justify-between gap-3">
                    <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                        <input
                            v-model="form.remember"
                            type="checkbox"
                            class="rounded border-slate-300 text-[#3150cf] focus:ring-[#3150cf]"
                        />
                        Remember me
                    </label>

                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-sm text-[#3150cf] transition hover:text-[#2743b6]"
                    >
                        Forgot your password?
                    </Link>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full rounded-lg bg-[#3150cf] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#2743b6] focus:outline-none focus:ring-2 focus:ring-[#3150cf] focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-70"
                >
                    Sign in
                </button>
            </form>
        </div>
    </AuthSplitLayout>
</template>
