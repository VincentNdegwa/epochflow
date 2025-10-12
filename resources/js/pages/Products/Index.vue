<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Eye, Edit, Trash2, Plus, Tags, FolderPlus } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import Pagination from '@/components/Pagination.vue';
import { Button } from '@/components/ui/button';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { type BreadcrumbItem } from '@/types';
import {
  DataTable,
  DataTableHeader,
  DataTableBody,
  DataTableRow,
  DataTableCell,
  DataTableHeaderCell,
} from '@/components/ui/data-table';

interface Product {
  id: number;
  name: string;
  description: string;
  price: number;
  stock: number;
  is_active: boolean;
  primary_image: string;
  slug: string;
  category: {
    name: string;
  };
}

interface Props {
  products: {
    data: Product[];
    links: any[];
    last_page: number;
  };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Products',
    href: '/products',
  },
];

const deleteProduct = (product: Product) => {
  if (confirm(`Are you sure you want to delete ${product.name}?`)) {
    router.delete(`/products/${product.id}`);
  }
};
</script>

<template>
  <Head title="Products" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold tracking-tight">Products</h1>
        <div class="flex items-center gap-2">
          <Button variant="outline" asChild>
            <Link href="/categories">
              <Tags class="mr-2 h-4 w-4" />
              Categories
            </Link>
          </Button>
          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <Button>
                <Plus class="mr-2 h-4 w-4" />
                Add New
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent>
              <DropdownMenuItem asChild>
                <Link href="/products/create" class="flex items-center">
                  <Plus class="mr-2 h-4 w-4" />
                  New Product
                </Link>
              </DropdownMenuItem>
              <DropdownMenuItem asChild>
                <Link href="/categories/create" class="flex items-center">
                  <FolderPlus class="mr-2 h-4 w-4" />
                  New Category
                </Link>
              </DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>
        </div>
      </div>

      <!-- Products Table -->
      <div class="rounded-xl border bg-card">
        <DataTable>
          <DataTableHeader>
            <DataTableRow>
              <DataTableHeaderCell>Product</DataTableHeaderCell>
              <DataTableHeaderCell>Category</DataTableHeaderCell>
              <DataTableHeaderCell>Price</DataTableHeaderCell>
              <DataTableHeaderCell>Stock</DataTableHeaderCell>
              <DataTableHeaderCell>Status</DataTableHeaderCell>
              <DataTableHeaderCell align="right">Actions</DataTableHeaderCell>
            </DataTableRow>
          </DataTableHeader>
          <DataTableBody>
            <DataTableRow v-for="product in products.data" :key="product.id">
              <DataTableCell>
                <div class="flex items-center gap-3">
                  <div class="h-12 w-12 flex-shrink-0 overflow-hidden rounded bg-muted">
                    <img
                      :src="product.primary_image"
                      :alt="product.name"
                      class="h-full w-full object-cover object-center"
                    />
                  </div>
                  <div>
                    <div class="font-medium">{{ product.name }}</div>
                    <div class="text-sm text-muted-foreground">
                      {{ product.description.substring(0, 50) }}...
                    </div>
                  </div>
                </div>
              </DataTableCell>
              <DataTableCell>{{ product.category.name }}</DataTableCell>
              <DataTableCell>${{ product.price }}</DataTableCell>
              <DataTableCell>{{ product.stock }}</DataTableCell>
              <DataTableCell>
                <span
                  class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium"
                  :class="product.is_active ? 'bg-green-500/10 text-green-500' : 'bg-yellow-500/10 text-yellow-500'"
                >
                  {{ product.is_active ? 'Active' : 'Draft' }}
                </span>
              </DataTableCell>
              <DataTableCell>
                <div class="flex items-center justify-end gap-2">
                  <Link
                    :href="`/shop/products/${product.slug}`"
                    class="rounded-md p-2 hover:bg-accent"
                    title="View"
                  >
                    <Eye class="h-4 w-4" />
                  </Link>
                  <Link
                    :href="`/products/${product.id}/edit`"
                    class="rounded-md p-2 hover:bg-accent"
                    title="Edit"
                  >
                    <Edit class="h-4 w-4" />
                  </Link>
                  <button
                    @click="deleteProduct(product)"
                    class="rounded-md p-2 hover:bg-accent text-destructive"
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
        <div v-if="products.last_page > 1" class="border-t px-4 py-4">
          <Pagination :links="products.links" />
        </div>
      </div>
    </div>
  </AppLayout>
</template>


