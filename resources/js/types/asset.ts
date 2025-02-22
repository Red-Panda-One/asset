export interface Asset {
    id: string;
    name: string;
    description: string | null;
    value: number;
    category_id: string | null;
    location_id: string | null;
    image: string | null;
    created_at: string;
    updated_at: string;
    category?: {
        id: string;
        name: string;
    };
    location?: {
        id: string;
        name: string;
    };
    tags?: Array<{
        id: string;
        name: string;
    }>;
}

export interface AssetFilters {
    search?: string;
    per_page?: string;
}

export interface AssetFormData {
    name: string;
    description: string | null;
    value: number;
    category_id: string | null;
    location_id: string | null;
    tags: string[];
    image: File | null;
}

export interface AssetResponse {
    data: Asset;
}

export interface AssetsResponse {
    data: Asset[];
    links: {
        first: string;
        last: string;
        prev: string | null;
        next: string | null;
    };
    meta: {
        current_page: number;
        from: number;
        last_page: number;
        path: string;
        per_page: number;
        to: number;
        total: number;
    };
}