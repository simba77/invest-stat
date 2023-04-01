declare module "*.vue" {
  import type { DefineComponent } from "vue";
  import 'vite/client'
  const component: DefineComponent<{}, {}, any>;
  export default component;
}

declare module 'lodash';
