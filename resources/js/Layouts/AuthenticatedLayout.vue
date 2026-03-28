<script setup>
import { ref, computed } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);

const page = usePage();
const permissions = computed(() => page.props.auth.permissions || []);
const roles = computed(() => page.props.auth.roles || []);

const can = (permission) => permissions.value.includes(permission);
const hasRole = (role) => roles.value.includes(role);
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav class="border-b border-gray-100 bg-white">
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-gray-800"
                                    />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <NavLink
                                    :href="route('dashboard')"
                                    :active="route().current('dashboard')"
                                >
                                    Dashboard
                                </NavLink>
                                <NavLink
                                    v-if="can('clients.view')"
                                    :href="route('clients.index')"
                                    :active="route().current('clients.*')"
                                >
                                    Clientes
                                </NavLink>
                                <NavLink
                                    v-if="can('suppliers.view')"
                                    :href="route('suppliers.index')"
                                    :active="route().current('suppliers.*')"
                                >
                                    Proveedores
                                </NavLink>
                                <NavLink
                                    v-if="can('metals.view')"
                                    :href="route('metals.index')"
                                    :active="route().current('metals.*')"
                                >
                                    Metales
                                </NavLink>
                                <NavLink
                                    v-if="can('purchases.view')"
                                    :href="route('purchases.index')"
                                    :active="route().current('purchases.*')"
                                >
                                    Compras
                                </NavLink>
                                <NavLink
                                    v-if="can('sales.view')"
                                    :href="route('sales.index')"
                                    :active="route().current('sales.*')"
                                >
                                    Ventas
                                </NavLink>
                                <NavLink
                                    v-if="can('purchase_requests.view_own') || can('purchase_requests.view')"
                                    :href="route('purchase-requests.index')"
                                    :active="route().current('purchase-requests.*')"
                                >
                                    Mis Solicitudes
                                </NavLink>
                                <NavLink
                                    v-if="can('users.view')"
                                    :href="route('users.index')"
                                    :active="route().current('users.*')"
                                >
                                    Usuarios
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <!-- Role badge -->
                            <span
                                v-if="roles.length > 0"
                                class="mr-3 inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                :class="{
                                    'bg-indigo-50 text-indigo-700 ring-1 ring-inset ring-indigo-700/10': hasRole('administrador'),
                                    'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-700/10': hasRole('operador'),
                                    'bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-700/10': hasRole('auditor'),
                                    'bg-cyan-50 text-cyan-700 ring-1 ring-inset ring-cyan-700/10': hasRole('cliente'),
                                }"
                            >
                                {{ roles[0]?.charAt(0).toUpperCase() + roles[0]?.slice(1) }}
                            </span>

                            <!-- Settings Dropdown -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="-me-0.5 ms-2 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink
                                            :href="route('profile.edit')"
                                        >
                                            Perfil
                                        </DropdownLink>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                        >
                                            Cerrar Sesión
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                        >
                            Dashboard
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            v-if="can('clients.view')"
                            :href="route('clients.index')"
                            :active="route().current('clients.*')"
                        >
                            Clientes
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            v-if="can('suppliers.view')"
                            :href="route('suppliers.index')"
                            :active="route().current('suppliers.*')"
                        >
                            Proveedores
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            v-if="can('metals.view')"
                            :href="route('metals.index')"
                            :active="route().current('metals.*')"
                        >
                            Metales
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            v-if="can('purchases.view')"
                            :href="route('purchases.index')"
                            :active="route().current('purchases.*')"
                        >
                            Compras
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            v-if="can('sales.view')"
                            :href="route('sales.index')"
                            :active="route().current('sales.*')"
                        >
                            Ventas
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            v-if="can('purchase_requests.view_own') || can('purchase_requests.view')"
                            :href="route('purchase-requests.index')"
                            :active="route().current('purchase-requests.*')"
                        >
                            Mis Solicitudes
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            v-if="can('users.view')"
                            :href="route('users.index')"
                            :active="route().current('users.*')"
                        >
                            Usuarios
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="border-t border-gray-200 pb-1 pt-4">
                        <div class="px-4">
                            <div class="flex items-center gap-2">
                                <div class="text-base font-medium text-gray-800">
                                    {{ $page.props.auth.user.name }}
                                </div>
                                <span
                                    v-if="roles.length > 0"
                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                                    :class="{
                                        'bg-indigo-50 text-indigo-700': hasRole('administrador'),
                                        'bg-emerald-50 text-emerald-700': hasRole('operador'),
                                        'bg-amber-50 text-amber-700': hasRole('auditor'),
                                        'bg-cyan-50 text-cyan-700': hasRole('cliente'),
                                    }"
                                >
                                    {{ roles[0]?.charAt(0).toUpperCase() + roles[0]?.slice(1) }}
                                </span>
                            </div>
                            <div class="text-sm font-medium text-gray-500">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                Perfil
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                Cerrar Sesión
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header
                class="bg-white shadow"
                v-if="$slots.header"
            >
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
