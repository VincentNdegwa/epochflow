export interface Store {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    contact_email: string | null;
    contact_phone: string | null;
    address: string | null;
    is_active: boolean;
    template: string;
    banner_url?: string;
    logo_url?: string;
    created_at: string;
    updated_at: string;
}

export interface Category {
    id: number;
    name: string;
    store_id: number;
    created_at: string;
    updated_at: string;
    products_count?: number;
}

export interface Product {
    id: number;
    store_id: number;
    category_id: number;
    name: string;
    slug: string;
    description: string;
    price: number;
    image_url: string;
    is_available: boolean;
    created_at: string;
    updated_at: string;
    category: Category;
    primary_image?: string;
}

export interface Customer {
    id: number;
    name: string;
    email: string;
    billing_address: string | null;
    billing_city: string | null;
    billing_state: string | null;
    billing_zip: string | null;
    billing_country: string | null;
}

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface Pagination<T> {
    current_page: number;
    data: T[];
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    links: PaginationLink[];
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
}

export interface Filters {
    category?: number;
    sort: 'latest' | 'price_asc' | 'price_desc';
}
