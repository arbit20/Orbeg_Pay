<template>
    <div class="flex min-h-screen items-center justify-center bg-[#eef2fb] p-4 sm:p-8">
        <div
            class="w-full max-w-6xl overflow-hidden rounded-3xl border border-white/60 bg-white shadow-[0_24px_70px_rgba(15,42,110,0.25)]"
        >
            <div class="grid min-h-[760px] lg:grid-cols-[42%_58%]">
                <section
                    class="relative hidden overflow-hidden bg-gradient-to-br from-[#112a80] via-[#1739a8] to-[#1f4fd6] px-10 py-12 text-white lg:flex lg:flex-col"
                >
                    <div
                        class="pointer-events-none absolute -left-16 -top-16 h-56 w-56 rounded-full bg-blue-300/20 blur-2xl"
                    ></div>
                    <div
                        class="pointer-events-none absolute -bottom-20 right-0 h-64 w-64 rounded-full bg-indigo-200/20 blur-3xl"
                    ></div>

                    <div class="relative">
                        <div class="flex items-center gap-4">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-[#fce38a] via-[#f2c94c] to-[#b8860b] shadow-[0_10px_24px_rgba(212,175,55,0.45)]"
                            >
                                <svg
                                    class="h-7 w-7 text-[#5d4618]"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M12 3L4 7.5V16.5L12 21L20 16.5V7.5L12 3Z"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linejoin="round"
                                    />
                                    <path
                                        d="M8.5 10H15.5M8.5 13H15.5"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                    />
                                </svg>
                            </div>
                            <div>
                                <p class="text-3xl font-semibold tracking-tight">
                                    DataFlow
                                </p>
                                <p class="mt-1 text-sm text-blue-100/90">
                                    AI-powered analytics for high-growth teams
                                </p>
                            </div>
                        </div>

                        <div
                            class="mt-9 flex items-center justify-between text-xs font-medium text-blue-100/90"
                        >
                            <span
                                class="rounded-full border border-blue-200/40 bg-white/10 px-3 py-1"
                                >+24.5%</span
                            >
                            <span
                                class="rounded-full border border-blue-200/40 bg-white/10 px-3 py-1"
                                >50.2K</span
                            >
                        </div>
                    </div>

                    <div class="relative mt-6 grid grid-cols-2 gap-4">
                        <div
                            class="relative h-48 rounded-2xl border border-blue-200/35 bg-white/8 p-5 shadow-[inset_0_1px_0_rgba(255,255,255,0.1)]"
                        >
                            <div
                                class="absolute inset-x-5 bottom-5 top-5 rounded-xl border border-blue-200/20"
                            ></div>
                            <div class="absolute bottom-7 left-8 flex items-end gap-2">
                                <span
                                    class="h-12 w-4 rounded-md bg-[#5bb2ff]/75"
                                ></span>
                                <span
                                    class="h-20 w-4 rounded-md bg-[#6f8fff]/75"
                                ></span>
                                <span
                                    class="h-24 w-4 rounded-md bg-[#9a76ff]/80"
                                ></span>
                                <span
                                    class="h-28 w-4 rounded-md bg-[#ff4ba0]/80"
                                ></span>
                            </div>
                        </div>

                        <div
                            class="relative h-48 rounded-2xl border border-blue-200/35 bg-white/8 p-5 shadow-[inset_0_1px_0_rgba(255,255,255,0.1)]"
                        >
                            <div
                                class="absolute inset-x-5 bottom-5 top-5 rounded-xl border border-blue-200/20"
                            ></div>
                            <svg
                                class="absolute bottom-8 left-8 right-8 h-24 w-auto"
                                viewBox="0 0 160 110"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <polyline
                                    points="0,95 30,80 55,60 86,54 115,38 150,32"
                                    stroke="#9FD3FF"
                                    stroke-width="3"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                                <circle cx="0" cy="95" r="4" fill="#E7F4FF" />
                                <circle cx="30" cy="80" r="4" fill="#E7F4FF" />
                                <circle cx="55" cy="60" r="4" fill="#E7F4FF" />
                                <circle cx="86" cy="54" r="4" fill="#E7F4FF" />
                                <circle cx="115" cy="38" r="4" fill="#E7F4FF" />
                                <circle cx="150" cy="32" r="4" fill="#E7F4FF" />
                            </svg>
                        </div>
                    </div>

                    <div class="mt-auto grid grid-cols-3 gap-4 pt-10 text-center">
                        <div>
                            <p class="text-3xl font-semibold leading-none">99.9%</p>
                            <p class="mt-2 text-sm text-blue-100/80">Uptime</p>
                        </div>
                        <div>
                            <p class="text-3xl font-semibold leading-none">50K+</p>
                            <p class="mt-2 text-sm text-blue-100/80">Teams</p>
                        </div>
                        <div>
                            <p class="text-3xl font-semibold leading-none">24/7</p>
                            <p class="mt-2 text-sm text-blue-100/80">Support</p>
                        </div>
                    </div>
                </section>

                <section class="bg-[#f7f9fd] px-6 py-8 sm:px-10 lg:px-14">
                    <slot />
                </section>
            </div>
        </div>
    </div>
</template>
