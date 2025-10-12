// This is for Ziggy's route() function typings
declare global {
    interface Window {
        route: (
            name: string,
            params?: Record<string, any>,
            absolute?: boolean,
        ) => string;
    }

    const route: (
        name: string,
        params?: Record<string, any>,
        absolute?: boolean,
    ) => string;
}

export {};
