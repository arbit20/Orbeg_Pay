<script setup>
defineProps({
    title: { type: String, required: true },
    value: { type: [String, Number], required: true },
    subtitle: { type: String, default: '' },
    icon: { type: String, default: '' },
    color: { type: String, default: 'blue' },
    href: { type: String, default: '' },
});

const colorClasses = {
    blue: 'bg-blue-50 text-blue-600 border-blue-200',
    green: 'bg-emerald-50 text-emerald-600 border-emerald-200',
    amber: 'bg-amber-50 text-amber-600 border-amber-200',
    red: 'bg-red-50 text-red-600 border-red-200',
    purple: 'bg-purple-50 text-purple-600 border-purple-200',
    indigo: 'bg-indigo-50 text-indigo-600 border-indigo-200',
    cyan: 'bg-cyan-50 text-cyan-600 border-cyan-200',
    gray: 'bg-gray-50 text-gray-600 border-gray-200',
};

const iconBgClasses = {
    blue: 'bg-blue-100 text-blue-600',
    green: 'bg-emerald-100 text-emerald-600',
    amber: 'bg-amber-100 text-amber-600',
    red: 'bg-red-100 text-red-600',
    purple: 'bg-purple-100 text-purple-600',
    indigo: 'bg-indigo-100 text-indigo-600',
    cyan: 'bg-cyan-100 text-cyan-600',
    gray: 'bg-gray-100 text-gray-600',
};
</script>

<template>
    <component
        :is="href ? 'a' : 'div'"
        :href="href || undefined"
        class="relative overflow-hidden rounded-xl border bg-white p-5 shadow-sm transition-all duration-200"
        :class="[
            href ? 'cursor-pointer hover:shadow-md hover:-translate-y-0.5' : '',
        ]"
    >
        <div class="flex items-start justify-between">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-500 truncate">{{ title }}</p>
                <p class="mt-1.5 text-2xl font-bold text-gray-900">{{ value }}</p>
                <p v-if="subtitle" class="mt-1 text-xs text-gray-400">{{ subtitle }}</p>
            </div>
            <div
                v-if="icon"
                class="flex-shrink-0 ml-3 flex h-11 w-11 items-center justify-center rounded-lg"
                :class="iconBgClasses[color] || iconBgClasses.blue"
            >
                <span class="text-lg" v-html="icon"></span>
            </div>
        </div>
    </component>
</template>
