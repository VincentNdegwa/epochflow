<script setup lang="ts">
import Pagination from '@/components/Pagination.vue';
import { Button } from '@/components/ui/button';
import {
    DataTable,
    DataTableBody,
    DataTableCell,
    DataTableHeader,
    DataTableHeaderCell,
    DataTableRow,
} from '@/components/ui/data-table';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Edit, Plus, Trash2 } from 'lucide-vue-next';

interface Category {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    is_active: boolean;
    products_count: number;
    created_at: string;
}

interface Props {
    categories: {
        data: Category[];
        links: any[];
        last_page: number;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Categories',
        href: '/categories',
    },
];

const deleteCategory = (category: Category) => {
    if (category.products_count > 0) {
        alert('Cannot delete category with associated products.');
        return;
    }

    if (confirm(`Are you sure you want to delete ${category.name}?`)) {
        router.delete(`/categories/${category.id}`);
    }
};
</script>

<template>
    <Head title="Categories" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <!-- Header -->
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold tracking-tight">
                    Categories
                </h1>
                <Button asChild>
                    <Link href="/categories/create">
                        <Plus class="mr-2 h-4 w-4" />
                        Add Category
                    </Link>
                </Button>
            </div>

            <!-- Categories Table -->
            <div class="rounded-xl border bg-card">
                <DataTable>
                    <DataTableHeader>
                        <DataTableRow>
                            <DataTableHeaderCell>Name</DataTableHeaderCell>
                            <DataTableHeaderCell
                                >Description</DataTableHeaderCell
                            >
                            <DataTableHeaderCell>Products</DataTableHeaderCell>
                            <DataTableHeaderCell>Status</DataTableHeaderCell>
                            <DataTableHeaderCell align="right"
                                >Actions</DataTableHeaderCell
                            >
                        </DataTableRow>
                    </DataTableHeader>
                    <DataTableBody>
                        <DataTableRow
                            v-for="category in categories.data"
                            :key="category.id"
                        >
                            <DataTableCell>
                                <div class="font-medium">
                                    {{ category.name }}
                                </div>
                            </DataTableCell>
                            <DataTableCell>
                                <div class="text-sm text-muted-foreground">
                                    {{
                                        category.description || 'No description'
                                    }}
                                </div>
                            </DataTableCell>
                            <DataTableCell
                                >{{
                                    category.products_count
                                }}
                                products</DataTableCell
                            >
                            <DataTableCell>
                                <span
                                    class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium"
                                    :class="
                                        category.is_active
                                            ? 'bg-green-500/10 text-green-500'
                                            : 'bg-yellow-500/10 text-yellow-500'
                                    "
                                >
                                    {{
                                        category.is_active
                                            ? 'Active'
                                            : 'Inactive'
                                    }}
                                </span>
                            </DataTableCell>
                            <DataTableCell>
                                <div
                                    class="flex items-center justify-end gap-2"
                                >
                                    <Link
                                        :href="`/categories/${category.slug}/edit`"
                                        class="rounded-md p-2 hover:bg-accent"
                                        title="Edit"
                                    >
                                        <Edit class="h-4 w-4" />
                                    </Link>
                                    <button
                                        @click="deleteCategory(category)"
                                        class="rounded-md p-2 text-destructive hover:bg-accent"
                                        title="Delete"
                                        :disabled="category.products_count > 0"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </button>
                                </div>
                            </DataTableCell>
                        </DataTableRow>
                    </DataTableBody>
                </DataTable>

                <!-- Pagination -->
                <div v-if="categories.last_page > 1" class="border-t px-4 py-4">
                    <Pagination :links="categories.links" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
