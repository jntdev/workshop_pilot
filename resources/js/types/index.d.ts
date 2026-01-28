export interface User {
    id: number;
    name: string;
    email: string;
}

export interface PageProps {
    auth: {
        user: User | null;
    };
    flash: {
        message: string | null;
        error: string | null;
    };
}

declare module '@inertiajs/react' {
    export function usePage<T extends PageProps>(): {
        props: T;
        url: string;
        component: string;
    };
}
