export interface User {
    id: number;
    name: string;
    email: string;
}
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
export interface Store {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    user_id: number;
    created_at: string;
    updated_at: string;
    user?: User;
}
export interface Category {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    store_id: number;
    created_at: string;
    updated_at: string;
    products_count?: number;
    image_url: string | null;
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

    shipping_address: string | null;
    shipping_city: string | null;
    shipping_state: string | null;
    shipping_zip_code: string | null;
    shipping_country: string | null;
}

export interface ProductImage {
    id: number;
    product_id: number;
    path: string;
    created_at: string;
    updated_at: string;
}

export interface Product {
    id: number;
    name: string;
    slug: string;
    description: string;
    price: number;
    store_id: number;
    category_id: number;
    user_id: number;
    stock: number;
    is_active: boolean;
    created_at: string;
    updated_at: string;
    store?: Store;
    category?: Category;
    images: ProductImage[];
    primary_image: string;
}

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface Pagination<T> {
    data: T[];
    links: PaginationLink[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

export interface CartItem {
    id: number;
    user_id: number;
    product_id: number;
    quantity: number;
    created_at: string;
    updated_at: string;
    product: Product;
}

export interface Order {
    id: number;
    slug: string;
    user_id: number;
    order_number: string;
    store_id: number;
    total: number;
    status: string;
    created_at: string;
    updated_at: string;
    user?: User;
    store?: Store;
    items?: OrderItem[];
}

export interface OrderItem {
    id: number;
    order_id: number;
    product_id: number;
    quantity: number;
    price: number;
    created_at: string;
    updated_at: string;
    product?: Product;
}
