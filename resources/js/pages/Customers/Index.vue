<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Eye, User } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import Pagination from '@/components/Pagination.vue';
import {
  DataTable,
  DataTableHeader,
  DataTableBody,
  DataTableRow,
  DataTableCell,
  DataTableHeaderCell,
} from '@/components/ui/data-table';
import { type BreadcrumbItem } from '@/types';

interface Customer {
  id: number;
  phone: string;
  city: string;
  state: string;
  country: string;
  user: {
    name: string;
    email: string;
    orders_count: number;
  };
}

interface Props {
  customers: {
    data: Customer[];
    links: any[];
    last_page: number;
  };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Customers',
    href: '/customers',
  },
];
</script>

<template>
  <Head title="Customers" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold tracking-tight">Customers</h1>
      </div>

      <!-- Customers Table -->
      <div class="rounded-xl border bg-card">
        <DataTable>
          <DataTableHeader>
            <DataTableRow>
              <DataTableHeaderCell>Customer</DataTableHeaderCell>
              <DataTableHeaderCell>Contact</DataTableHeaderCell>
              <DataTableHeaderCell>Location</DataTableHeaderCell>
              <DataTableHeaderCell>Orders</DataTableHeaderCell>
              <DataTableHeaderCell align="right">Actions</DataTableHeaderCell>
            </DataTableRow>
          </DataTableHeader>
          <DataTableBody>
            <DataTableRow v-for="customer in customers.data" :key="customer.id">
              <DataTableCell>
                <div class="flex items-center gap-3">
                  <div class="h-10 w-10 flex-shrink-0 rounded-full bg-muted flex items-center justify-center">
                    <User class="h-5 w-5 text-muted-foreground" />
                  </div>
                  <div>
                    <div class="font-medium">{{ customer.user.name }}</div>
                    <div class="text-sm text-muted-foreground">{{ customer.user.email }}</div>
                  </div>
                </div>
              </DataTableCell>
              <DataTableCell>{{ customer.phone }}</DataTableCell>
              <DataTableCell>
                <div>{{ customer.city }}, {{ customer.state }}</div>
                <div class="text-muted-foreground">{{ customer.country }}</div>
              </DataTableCell>
              <DataTableCell>{{ customer.user.orders_count ?? 0 }} orders</DataTableCell>
              <DataTableCell>
                <div class="flex items-center justify-end gap-2">
                  <Link
                    :href="`/customers/${customer.id}`"
                    class="rounded-md p-2 hover:bg-accent"
                    title="View Details"
                  >
                    <Eye class="h-4 w-4" />
                  </Link>
                </div>
              </DataTableCell>
            </DataTableRow>
          </DataTableBody>
        </DataTable>

        <!-- Pagination -->
        <div v-if="customers.last_page > 1" class="border-t px-4 py-4">
          <Pagination :links="customers.links" />
        </div>
      </div>
    </div>
  </AppLayout>
</template>
