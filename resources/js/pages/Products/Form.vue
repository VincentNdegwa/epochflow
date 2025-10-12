<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Upload, Trash2 } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';

interface Props {
  product?: {
    id: number;
    category_id: number;
    name: string;
    description: string;
    price: number;
    stock: number;
    is_active: boolean;
    images: Array<{
      id: number;
      path: string;
    }>;
  };
  categories: Array<{
    id: number;
    name: string;
  }>;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Products',
    href: '/products',
  },
  {
    title: props.product ? 'Edit Product' : 'Create Product',
    href: props.product ? `/products/${props.product.id}/edit` : '/products/create',
  },
];

const existingImages = ref(props.product?.images || []);
const imagePreviews = ref<string[]>([]);
const newImages = ref<File[]>([]);

const form = useForm({
  category_id: props.product?.category_id || '',
  name: props.product?.name || '',
  description: props.product?.description || '',
  price: props.product?.price || '',
  stock: props.product?.stock || 0,
  is_active: props.product?.is_active ?? true,
  images: [] as File[],
  images_to_delete: [] as number[]
});

const addImages = (e: Event) => {
  const files = Array.from((e.target as HTMLInputElement).files || []);
  
  files.forEach(file => {
    if (file.type.startsWith('image/')) {
      newImages.value.push(file);
      const reader = new FileReader();
      reader.onload = e => e.target?.result && imagePreviews.value.push(e.target.result as string);
      reader.readAsDataURL(file);
    }
  });
};

const removeNewImage = (index: number) => {
  imagePreviews.value.splice(index, 1);
  newImages.value.splice(index, 1);
};

const removeExistingImage = (image: { id: number }) => {
  if (confirm('Are you sure you want to remove this image?')) {
    existingImages.value = existingImages.value.filter((img: { id: number }) => img.id !== image.id);
    form.images_to_delete = [...form.images_to_delete, image.id];
  }
};

const submit = () => {
  form.images = newImages.value;

  if (props.product) {
    form.put(`/products/${props.product.id}`);
  } else {
    form.post('/products');
  }
};
</script>

<template>
  <Head :title="product ? 'Edit Product' : 'Create Product'" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <div class="space-y-6">
        <!-- Basic Information -->
        <div class="rounded-xl border bg-card">
          <div class="border-b p-4">
            <h2 class="font-semibold">Basic Information</h2>
          </div>
          <div class="p-4 space-y-4">
            <div>
              <label for="name" class="block text-sm font-medium mb-1">Product Name</label>
              <input
                id="name"
                v-model="form.name"
                type="text"
                class="w-full rounded-md border bg-transparent px-3 py-2"
                :class="{ 'border-destructive': form.errors.name }"
                required
              />
              <p v-if="form.errors.name" class="mt-1 text-sm text-destructive">
                {{ form.errors.name }}
              </p>
            </div>

            <div>
              <label for="category_id" class="block text-sm font-medium mb-1">Category</label>
              <select
                id="category_id"
                v-model="form.category_id"
                class="w-full rounded-md border bg-transparent px-3 py-2"
                :class="{ 'border-destructive': form.errors.category_id }"
                required
              >
                <option value="">Select a category</option>
                <option
                  v-for="category in categories"
                  :key="category.id"
                  :value="category.id"
                >
                  {{ category.name }}
                </option>
              </select>
              <p v-if="form.errors.category_id" class="mt-1 text-sm text-destructive">
                {{ form.errors.category_id }}
              </p>
            </div>

            <div>
              <label for="description" class="block text-sm font-medium mb-1">Description</label>
              <textarea
                id="description"
                v-model="form.description"
                rows="4"
                class="w-full rounded-md border bg-transparent px-3 py-2"
                :class="{ 'border-destructive': form.errors.description }"
                required
              ></textarea>
              <p v-if="form.errors.description" class="mt-1 text-sm text-destructive">
                {{ form.errors.description }}
              </p>
            </div>
          </div>
        </div>

        <!-- Pricing & Inventory -->
        <div class="rounded-xl border bg-card">
          <div class="border-b p-4">
            <h2 class="font-semibold">Pricing & Inventory</h2>
          </div>
          <div class="p-4 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label for="price" class="block text-sm font-medium mb-1">Price</label>
                <div class="relative">
                  <span class="absolute left-3 top-2 text-foreground/70">$</span>
                  <input
                    id="price"
                    v-model="form.price"
                    type="number"
                    min="0"
                    step="0.01"
                    class="w-full rounded-md border bg-transparent px-3 py-2 pl-7"
                    :class="{ 'border-destructive': form.errors.price }"
                    required
                  />
                </div>
                <p v-if="form.errors.price" class="mt-1 text-sm text-destructive">
                  {{ form.errors.price }}
                </p>
              </div>

              <div>
                <label for="stock" class="block text-sm font-medium mb-1">Stock</label>
                <input
                  id="stock"
                  v-model="form.stock"
                  type="number"
                  min="0"
                  class="w-full rounded-md border bg-transparent px-3 py-2"
                  :class="{ 'border-destructive': form.errors.stock }"
                  required
                />
                <p v-if="form.errors.stock" class="mt-1 text-sm text-destructive">
                  {{ form.errors.stock }}
                </p>
              </div>
            </div>

            <div>
              <label class="flex items-center gap-2">
                <input
                  type="checkbox"
                  v-model="form.is_active"
                  class="rounded border-border"
                />
                <span class="text-sm">Product is active and available for purchase</span>
              </label>
            </div>
          </div>
        </div>

        <!-- Images -->
        <div class="rounded-xl border bg-card">
          <div class="border-b p-4">
            <h2 class="font-semibold">Product Images</h2>
          </div>
          <div class="p-4">
            <div
              class="grid grid-cols-2 md:grid-cols-4 gap-4"
              :class="{ 'border-destructive': form.errors.images }"
            >
              <!-- Existing Images -->
              <div
                v-for="image in existingImages"
                :key="image.id"
                class="relative aspect-square rounded-lg bg-muted overflow-hidden group"
              >
                <img
                  :src="image.path"
                  :alt="form.name"
                  class="h-full w-full object-cover object-center"
                />
                <button
                  type="button"
                  @click="removeExistingImage(image)"
                  class="absolute inset-0 flex items-center justify-center bg-destructive/90 opacity-0 group-hover:opacity-100 transition-opacity"
                >
                  <Trash2 class="h-4 w-4 text-destructive-foreground" />
                </button>
              </div>

              <!-- New Image Previews -->
              <div
                v-for="(preview, index) in imagePreviews"
                :key="index"
                class="relative aspect-square rounded-lg bg-muted overflow-hidden group"
              >
                <img
                  :src="preview"
                  :alt="form.name"
                  class="h-full w-full object-cover object-center"
                />
                <button
                  type="button"
                  @click="removeNewImage(index)"
                  class="absolute inset-0 flex items-center justify-center bg-destructive/90 opacity-0 group-hover:opacity-100 transition-opacity"
                >
                  <Trash2 class="h-4 w-4 text-destructive-foreground" />
                </button>
              </div>

              <!-- Upload Button -->
              <label class="relative aspect-square rounded-lg border-2 border-dashed border-border hover:border-primary cursor-pointer">
                <input
                  type="file"
                  @change="addImages"
                  multiple
                  accept="image/*"
                  class="hidden"
                />
                <div class="absolute inset-0 flex flex-col items-center justify-center text-foreground/70">
                  <Upload class="h-6 w-6 mb-2" />
                  <span class="text-sm">Add Images</span>
                </div>
              </label>
            </div>
            <p v-if="form.errors.images" class="mt-1 text-sm text-destructive">
              {{ form.errors.images }}
            </p>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end gap-4">
          <Button variant="outline" asChild>
            <Link href="/products">Cancel</Link>
          </Button>
          <Button
            type="submit"
            :disabled="form.processing"
            @click="submit"
          >
            <span v-if="form.processing" class="mr-2">
              <span class="h-4 w-4 border-2 border-current border-r-transparent rounded-full animate-spin" />
            </span>
            {{ product ? 'Update Product' : 'Create Product' }}
          </Button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>