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
}

export interface ProductImage {
    id: number;
    product_id: number;
    url: string;
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
    product?: Product;
}

export interface Order {
    id: number;
    user_id: number;
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
