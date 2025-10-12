<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Eye, XCircle } from 'lucide-vue-next';
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
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';

interface Order {
  id: number;
  order_number: string;
  items: any[];
  user: {
    name: string;
    email: string;
  };
  created_at: string;
  status: 'pending' | 'processing' | 'completed' | 'cancelled';
  total_amount: number;
}

interface Props {
  orders: {
    data: Order[];
    links: any[];
    last_page: number;
  };
  filters: {
    status: string;
  };
}

const props = withDefaults(defineProps<Props>(), {
  filters: () => ({
    status: ''
  })
});

const status = ref(props.filters?.status || '');

watch(() => props.filters?.status, (newStatus: string | undefined) => {
  if (newStatus !== undefined) {
    status.value = newStatus;
  }
});

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Orders',
    href: '/orders',
  },
];

const filter = () => {
  router.get('/orders', { status: status.value }, {
    preserveState: true,
    preserveScroll: true
  });
};

const cancelOrder = (order: Order) => {
  if (confirm(`Are you sure you want to cancel order #${order.order_number}?`)) {
    router.post(`/orders/${order.id}/cancel`, {}, {
      preserveScroll: true
    });
  }
};

const statusClasses = {
  pending: 'bg-yellow-500/10 text-yellow-500',
  processing: 'bg-blue-500/10 text-blue-500',
  completed: 'bg-green-500/10 text-green-500',
  cancelled: 'bg-red-500/10 text-red-500'
};
</script>

<template>
  <Head title="Orders" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold tracking-tight">Orders</h1>
        <select
          v-model="status"
          class="h-9 rounded-md border bg-background px-3 text-sm w-36"
          @change="filter"
        >
          <option value="">All Status</option>
          <option value="pending">Pending</option>
          <option value="processing">Processing</option>
          <option value="completed">Completed</option>
          <option value="cancelled">Cancelled</option>
        </select>
      </div>

      <!-- Orders Table -->
      <div class="rounded-xl border bg-card">
        <DataTable>
          <DataTableHeader>
            <DataTableRow>
              <DataTableHeaderCell>Order</DataTableHeaderCell>
              <DataTableHeaderCell>Customer</DataTableHeaderCell>
              <DataTableHeaderCell>Date</DataTableHeaderCell>
              <DataTableHeaderCell>Status</DataTableHeaderCell>
              <DataTableHeaderCell>Total</DataTableHeaderCell>
              <DataTableHeaderCell align="right">Actions</DataTableHeaderCell>
            </DataTableRow>
          </DataTableHeader>
          <DataTableBody>
            <DataTableRow v-for="order in orders.data" :key="order.id">
              <DataTableCell>
                <div class="font-medium">#{{ order.order_number }}</div>
                <div class="text-sm text-muted-foreground">{{ order.items.length }} items</div>
              </DataTableCell>
              <DataTableCell>
                <div class="font-medium">{{ order.user.name }}</div>
                <div class="text-sm text-muted-foreground">{{ order.user.email }}</div>
              </DataTableCell>
              <DataTableCell>{{ new Date(order.created_at).toLocaleDateString() }}</DataTableCell>
              <DataTableCell>
                <span
                  class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium"
                  :class="statusClasses[order.status]"
                >
                  {{ order.status }}
                </span>
              </DataTableCell>
              <DataTableCell class="font-medium">${{ order.total_amount }}</DataTableCell>
              <DataTableCell>
                <div class="flex items-center justify-end gap-2">
                  <Link
                    :href="`/orders/${order.id}`"
                    class="rounded-md p-2 hover:bg-accent"
                    title="View Details"
                  >
                    <Eye class="h-4 w-4" />
                  </Link>
                  <Button
                    v-if="order.status === 'pending'"
                    variant="ghost"
                    size="icon"
                    @click="cancelOrder(order)"
                    class="text-destructive"
                    title="Cancel Order"
                  >
                    <XCircle class="h-4 w-4" />
                  </Button>
                </div>
              </DataTableCell>
            </DataTableRow>
          </DataTableBody>
        </DataTable>

        <!-- Pagination -->
        <div v-if="orders.last_page > 1" class="border-t px-4 py-4">
          <Pagination :links="orders.links" />
        </div>
      </div>
    </div>
  </AppLayout>
</template>
