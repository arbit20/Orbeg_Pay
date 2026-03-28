<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    metal: Object,
});

const form = useForm({
    name: props.metal.name,
    symbol: props.metal.symbol,
    description: props.metal.description ?? '',
    is_active: props.metal.is_active,
});

const submit = () => {
    form.put(route('metals.update', props.metal.id));
};
</script>

<template>
    <Head :title="`Editar ${metal.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Editar Metal: {{ metal.name }} ({{ metal.symbol }})
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <InputLabel for="name" value="Nombre" />
                                <TextInput
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="symbol" value="Simbolo Quimico" />
                                <TextInput
                                    id="symbol"
                                    v-model="form.symbol"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                    maxlength="5"
                                />
                                <InputError :message="form.errors.symbol" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="description" value="Descripcion (opcional)" />
                                <TextInput
                                    id="description"
                                    v-model="form.description"
                                    type="text"
                                    class="mt-1 block w-full"
                                />
                                <InputError :message="form.errors.description" class="mt-2" />
                            </div>

                            <div class="flex items-center gap-3">
                                <input
                                    id="is_active"
                                    v-model="form.is_active"
                                    type="checkbox"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                    :true-value="true"
                                    :false-value="false"
                                />
                                <InputLabel for="is_active" value="Metal activo" />
                            </div>

                            <div class="flex items-center justify-end gap-4">
                                <Link :href="route('metals.show', metal.id)" class="text-sm text-gray-600 underline hover:text-gray-900">
                                    Cancelar
                                </Link>
                                <PrimaryButton :disabled="form.processing">
                                    Actualizar Metal
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
