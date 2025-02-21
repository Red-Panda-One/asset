declare module '@inertiajs/vue3' {
  interface PageProps {
    errors: Record<string, string>;
    auth: {
      user: {
        id: string;
        name: string;
        email: string;
      };
    };
  }
}

declare module 'vue-router' {
  interface RouteMeta {
    layout?: string;
    middleware?: string[];
  }
}

interface RouteParams {
  'assets.index': void;
  'assets.create': void;
  'assets.edit': string;
  'assets.show': string;
  'assets.destroy': string;
}

declare function route<T extends keyof RouteParams>(
  name: T,
  params?: RouteParams[T] extends void ? void : RouteParams[T],
  absolute?: boolean
): string;

declare global {
  interface Window {
    route: typeof route;
  }

  const route: typeof route;
}

export {};