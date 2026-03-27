<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { getCsrfHeaders } from '@/lib/csrf';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    roles: Array,
    suppliers: Array,
});

const form = useForm({
    name: '',
    username: '',
    birth_date: '',
    email: '',
    password: '',
    password_confirmation: '',
    phone: '',
    role: 'operador',
    supplier_id: '',
    is_active: true,
});

const showSupplier = computed(() => ['operador', 'cliente'].includes(form.role));

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
            suggestError.value = data.message || 'No se pudieron cargar sugerencias. Recarga la página e intenta de nuevo.';
        }
    } catch {
        suggestions.value = [];
        suggestError.value = 'Error de conexión. Comprueba tu conexión e intenta de nuevo.';
    } finally {
        loadingSuggestions.value = false;
    }
};

const selectSuggestion = (username) => {
    form.username = username;
    suggestions.value = [];
};

const submit = () => {
    form.post(route('users.store'), {
        onSuccess: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Nuevo Usuario" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Registrar Nuevo Usuario
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div class="md:col-span-2">
                                <InputLabel for="name" value="Nombre completo" />
                                <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required autofocus />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="birth_date" value="Fecha de nacimiento" />
                                <TextInput id="birth_date" v-model="form.birth_date" type="date" class="mt-1 block w-full" required />
                                <InputError :message="form.errors.birth_date" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="username" value="Nombre de usuario" />
                                <div class="relative">
                                    <TextInput
                                        id="username"
                                        v-model="form.username"
                                        type="text"
                                        class="mt-1 block w-full pr-10"
                                        required
                                        placeholder="Ej: swift_trader42"
                                    />
                                    <div v-if="checkingUsername" class="absolute inset-y-0 right-0 flex items-center pr-3 mt-1">
                                        <svg class="h-5 w-5 animate-spin text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                        </svg>
                                    </div>
                                    <div v-else-if="usernameAvailable === true && form.username.length >= 4" class="absolute inset-y-0 right-0 flex items-center pr-3 mt-1">
                                        <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <div v-else-if="usernameAvailable === false" class="absolute inset-y-0 right-0 flex items-center pr-3 mt-1">
                                        <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </div>
                                </div>
                                <p v-if="usernameAvailable === true && form.username.length >= 4" class="mt-1 text-xs text-green-600">Nombre de usuario disponible.</p>
                                <p v-if="usernameAvailable === false" class="mt-1 text-xs text-red-600">Este nombre de usuario ya está en uso.</p>
                                <p class="mt-1 text-xs text-gray-500">Solo letras, números y guiones bajos. Mínimo 4 caracteres.</p>

                                <div class="mt-2">
                                    <button
                                        type="button"
                                        @click="fetchSuggestions"
                                        :disabled="loadingSuggestions"
                                        class="text-xs text-indigo-600 hover:text-indigo-800 underline"
                                    >
                                        {{ loadingSuggestions ? 'Generando...' : 'Sugerir nombres aleatorios' }}
                                    </button>
                                    <div v-if="suggestions.length" class="mt-2 flex flex-wrap gap-2">
                                        <button
                                            v-for="s in suggestions"
                                            :key="s"
                                            type="button"
                                            @click="selectSuggestion(s)"
                                            class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700 hover:bg-indigo-100 transition"
                                        >
                                            {{ s }}
                                        </button>
                                    </div>
                                    <p v-if="suggestError" class="mt-2 text-xs text-red-600">{{ suggestError }}</p>
                                </div>

                                <InputError :message="form.errors.username" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="email" value="Correo electrónico" />
                                <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" required />
                                <InputError :message="form.errors.email" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="phone" value="Teléfono" />
                                <TextInput id="phone" v-model="form.phone" type="text" class="mt-1 block w-full" />
                                <InputError :message="form.errors.phone" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="password" value="Contraseña" />
                                <TextInput id="password" v-model="form.password" type="password" class="mt-1 block w-full" required />
                                <InputError :message="form.errors.password" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="password_confirmation" value="Confirmar contraseña" />
                                <TextInput id="password_confirmation" v-model="form.password_confirmation" type="password" class="mt-1 block w-full" required />
                                <InputError :message="form.errors.password_confirmation" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="role" value="Rol" />
                                <select
                                    id="role"
                                    v-model="form.role"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option v-for="role in roles" :key="role.value" :value="role.value">
                                        {{ role.label }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.role" class="mt-2" />
                            </div>

                            <div v-if="showSupplier">
                                <InputLabel for="supplier_id" value="Proveedor asociado" />
                                <select
                                    id="supplier_id"
                                    v-model="form.supplier_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">Sin proveedor</option>
                                    <option v-for="supplier in suppliers" :key="supplier.value" :value="supplier.value">
                                        {{ supplier.label }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.supplier_id" class="mt-2" />
                            </div>

                            <div class="md:col-span-2 flex items-center gap-3">
                                <input
                                    id="is_active"
                                    v-model="form.is_active"
                                    type="checkbox"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                    :true-value="true"
                                    :false-value="false"
                                />
                                <InputLabel for="is_active" value="Usuario activo" />
                            </div>

                            <div class="md:col-span-2 flex items-center justify-end gap-4">
                                <Link :href="route('users.index')" class="text-sm text-gray-600 underline hover:text-gray-900">
                                    Cancelar
                                </Link>
                                <PrimaryButton :disabled="form.processing">Guardar Usuario</PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
