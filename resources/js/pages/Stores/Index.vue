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

interface Store {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    contact_email: string | null;
    contact_phone: string | null;
    is_active: boolean;
    created_at: string;
}

interface Props {
    stores: {
        data: Store[];
        links: any[];
        last_page: number;
    };
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Stores',
        href: '/stores',
    },
];

const deleteStore = (store: Store) => {
    if (!confirm('Are you sure you want to delete this store?')) {
        return;
    }
    router.delete(`/stores/${store.id}`);
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Stores" />

        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <!-- Header -->
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold tracking-tight">Stores</h1>
                <div class="flex items-center gap-2">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        <Link href="/stores/create">New Store</Link>
                    </Button>
                </div>
            </div>

            <!-- Stores Table -->
            <div class="rounded-xl border bg-card">
                <DataTable>
                    <DataTableHeader>
                        <DataTableRow>
                            <DataTableHeaderCell>Store</DataTableHeaderCell>
                            <DataTableHeaderCell>URL</DataTableHeaderCell>
                            <DataTableHeaderCell>Contact</DataTableHeaderCell>
                            <DataTableHeaderCell>Status</DataTableHeaderCell>
                            <DataTableCell>Preview</DataTableCell>
                            <DataTableHeaderCell>Created</DataTableHeaderCell>
                            <DataTableHeaderCell align="right"
                                >Actions</DataTableHeaderCell
                            >
                        </DataTableRow>
                    </DataTableHeader>
                    <DataTableBody>
                        <DataTableRow
                            v-for="store in stores.data"
                            :key="store.id"
                        >
                            <DataTableCell>
                                <div class="flex items-start gap-3">
                                    <div>
                                        <div class="font-medium">
                                            {{ store.name }}
                                        </div>
                                        <div
                                            class="text-sm text-muted-foreground"
                                        >
                                            {{
                                                store.description
                                                    ? store.description.substring(
                                                          0,
                                                          50,
                                                      ) + '...'
                                                    : 'No description'
                                            }}
                                        </div>
                                    </div>
                                </div>
                            </DataTableCell>
                            <DataTableCell>
                                <span class="text-sm text-muted-foreground"
                                    >/{{ store.slug }}</span
                                >
                            </DataTableCell>
                            <DataTableCell>
                                <div
                                    v-if="
                                        store.contact_email ||
                                        store.contact_phone
                                    "
                                >
                                    <div
                                        v-if="store.contact_email"
                                        class="text-sm"
                                    >
                                        {{ store.contact_email }}
                                    </div>
                                    <div
                                        v-if="store.contact_phone"
                                        class="text-sm text-muted-foreground"
                                    >
                                        {{ store.contact_phone }}
                                    </div>
                                </div>
                                <span
                                    v-else
                                    class="text-sm text-muted-foreground"
                                    >Not provided</span
                                >
                            </DataTableCell>
                            <DataTableCell>
                                <span
                                    class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium"
                                    :class="
                                        store.is_active
                                            ? 'bg-green-500/10 text-green-500'
                                            : 'bg-yellow-500/10 text-yellow-500'
                                    "
                                >
                                    {{
                                        store.is_active ? 'Active' : 'Inactive'
                                    }}
                                </span>
                            </DataTableCell>
                            <DataTableCell>
                                <Link
                                    :href="`/store/${store.slug}`"
                                    class="text-blue-600 hover:underline"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    Visit
                                </Link>
                            </DataTableCell>
                            <DataTableCell>
                                <span class="text-sm text-muted-foreground">
                                    {{
                                        new Date(
                                            store.created_at,
                                        ).toLocaleDateString()
                                    }}
                                </span>
                            </DataTableCell>
                            <DataTableCell>
                                <div
                                    class="flex items-center justify-end gap-2"
                                >
                                    <Link
                                        :href="`/stores/${store.slug}/edit`"
                                        class="rounded-md p-2 hover:bg-accent"
                                        title="Edit"
                                    >
                                        <Edit class="h-4 w-4" />
                                    </Link>
                                    <button
                                        @click="deleteStore(store)"
                                        class="rounded-md p-2 text-destructive hover:bg-accent"
                                        title="Delete"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </button>
                                </div>
                            </DataTableCell>
                        </DataTableRow>
                    </DataTableBody>
                </DataTable>

                <!-- Pagination -->
                <div v-if="stores.last_page > 1" class="border-t px-4 py-4">
                    <Pagination :links="stores.links" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
