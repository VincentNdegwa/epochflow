<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';

interface Props {
  store?: {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    contact_email: string | null;
    contact_phone: string | null;
    address: string | null;
    is_active: boolean;
  };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Stores',
    href: '/stores',
  },
  {
    title: props.store ? 'Edit Store' : 'Create Store',
    href: props.store ? `/stores/${props.store.id}/edit` : '/stores/create',
  },
];

const form = useForm({
  name: props.store?.name || '',
  slug: props.store?.slug || '',
  description: props.store?.description || '',
  contact_email: props.store?.contact_email || '',
  contact_phone: props.store?.contact_phone || '',
  address: props.store?.address || '',
  is_active: props.store?.is_active ?? true,
});

const generateSlug = () => {
  form.slug = form.name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
};

const submit = () => {
  if (props.store) {
    form.put(`/stores/${props.store.id}`);
  } else {
    form.post('/stores');
  }
};
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head :title="props.store ? 'Edit Store' : 'Create Store'" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <div class="space-y-6">
        <!-- Basic Information -->
        <div class="rounded-xl border bg-card">
          <div class="border-b p-4">
            <h2 class="font-semibold">Basic Information</h2>
          </div>
          <div class="p-4 space-y-4">
            <div>
              <label for="name" class="block text-sm font-medium mb-1">Store Name</label>
              <input
                id="name"
                v-model="form.name"
                type="text"
                class="w-full rounded-md border bg-transparent px-3 py-2"
                :class="{ 'border-destructive': form.errors.name }"
                @input="generateSlug"
                required
              />
              <p v-if="form.errors.name" class="mt-1 text-sm text-destructive">
                {{ form.errors.name }}
              </p>
            </div>

            <div>
              <label for="slug" class="block text-sm font-medium mb-1">Store URL</label>
              <div class="flex items-center">
                <span class="text-gray-500 dark:text-gray-400 mr-1">/</span>
                <input
                  id="slug"
                  v-model="form.slug"
                  type="text"
                  class="flex-1 rounded-md border bg-transparent px-3 py-2"
                  :class="{ 'border-destructive': form.errors.slug }"
                  pattern="[a-z0-9-]+"
                  required
                />
              </div>
              <p class="mt-1 text-sm text-muted-foreground">
                Only lowercase letters, numbers, and hyphens are allowed.
              </p>
              <p v-if="form.errors.slug" class="mt-1 text-sm text-destructive">
                {{ form.errors.slug }}
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
              ></textarea>
              <p v-if="form.errors.description" class="mt-1 text-sm text-destructive">
                {{ form.errors.description }}
              </p>
            </div>
          </div>
        </div>

        <!-- Contact Information -->
        <div class="rounded-xl border bg-card">
          <div class="border-b p-4">
            <h2 class="font-semibold">Contact Information</h2>
          </div>
          <div class="p-4 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label for="contact_email" class="block text-sm font-medium mb-1">Contact Email</label>
                <input
                  id="contact_email"
                  v-model="form.contact_email"
                  type="email"
                  class="w-full rounded-md border bg-transparent px-3 py-2"
                  :class="{ 'border-destructive': form.errors.contact_email }"
                />
                <p v-if="form.errors.contact_email" class="mt-1 text-sm text-destructive">
                  {{ form.errors.contact_email }}
                </p>
              </div>

              <div>
                <label for="contact_phone" class="block text-sm font-medium mb-1">Contact Phone</label>
                <input
                  id="contact_phone"
                  v-model="form.contact_phone"
                  type="tel"
                  class="w-full rounded-md border bg-transparent px-3 py-2"
                  :class="{ 'border-destructive': form.errors.contact_phone }"
                />
                <p v-if="form.errors.contact_phone" class="mt-1 text-sm text-destructive">
                  {{ form.errors.contact_phone }}
                </p>
              </div>
            </div>

            <div>
              <label for="address" class="block text-sm font-medium mb-1">Address</label>
              <textarea
                id="address"
                v-model="form.address"
                rows="3"
                class="w-full rounded-md border bg-transparent px-3 py-2"
                :class="{ 'border-destructive': form.errors.address }"
              ></textarea>
              <p v-if="form.errors.address" class="mt-1 text-sm text-destructive">
                {{ form.errors.address }}
              </p>
            </div>
          </div>
        </div>

        <!-- Store Settings -->
        <div class="rounded-xl border bg-card">
          <div class="border-b p-4">
            <h2 class="font-semibold">Store Settings</h2>
          </div>
          <div class="p-4">
            <div>
              <label class="flex items-center gap-2">
                <input
                  type="checkbox"
                  id="is_active"
                  v-model="form.is_active"
                  class="rounded border-border"
                />
                <span class="text-sm">Store is active and visible to customers</span>
              </label>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end gap-4">
          <Button variant="outline" asChild>
            <Link href="/stores">Cancel</Link>
          </Button>
          <Button
            type="submit"
            :disabled="form.processing"
            @click="submit"
          >
            <span v-if="form.processing" class="mr-2">
              <span class="h-4 w-4 border-2 border-current border-r-transparent rounded-full animate-spin" />
            </span>
            {{ props.store ? 'Update Store' : 'Create Store' }}
          </Button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>