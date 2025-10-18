<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

interface Props {
    category?: {
        id: number;
        name: string;
        description: string | null;
        is_active: boolean;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Categories',
        href: '/categories',
    },
    {
        title: props.category ? 'Edit Category' : 'Create Category',
        href: props.category
            ? `/categories/${props.category.id}/edit`
            : '/categories/create',
    },
];

const form = useForm({
    name: props.category?.name || '',
    description: props.category?.description || '',
    is_active: props.category?.is_active ?? true,
});

const submit = () => {
    if (props.category) {
        form.put(`/categories/${props.category.id}`);
    } else {
        form.post('/categories');
    }
};
</script>

<template>
    <Head :title="category ? 'Edit Category' : 'Create Category'" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Basic Information -->
                <div class="rounded-xl border bg-card">
                    <div class="border-b p-4">
                        <h2 class="font-semibold">Basic Information</h2>
                    </div>
                    <div class="space-y-4 p-4">
                        <div>
                            <label
                                for="name"
                                class="mb-1 block text-sm font-medium"
                                >Name</label
                            >
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="w-full rounded-md border bg-transparent px-3 py-2"
                                :class="{
                                    'border-destructive': form.errors.name,
                                }"
                                required
                            />
                            <p
                                v-if="form.errors.name"
                                class="mt-1 text-sm text-destructive"
                            >
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <div>
                            <label
                                for="description"
                                class="mb-1 block text-sm font-medium"
                                >Description</label
                            >
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="4"
                                class="w-full rounded-md border bg-transparent px-3 py-2"
                                :class="{
                                    'border-destructive':
                                        form.errors.description,
                                }"
                            ></textarea>
                            <p
                                v-if="form.errors.description"
                                class="mt-1 text-sm text-destructive"
                            >
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <div>
                            <label class="flex items-center gap-2">
                                <input
                                    type="checkbox"
                                    v-model="form.is_active"
                                    class="rounded border-border"
                                />
                                <span class="text-sm">Category is active</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-4">
                    <Button variant="outline" asChild>
                        <Link href="/categories">Cancel</Link>
                    </Button>
                    <Button type="submit" :disabled="form.processing">
                        <span v-if="form.processing" class="mr-2">
                            <span
                                class="h-4 w-4 animate-spin rounded-full border-2 border-current border-r-transparent"
                            />
                        </span>
                        {{ category ? 'Update Category' : 'Create Category' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
