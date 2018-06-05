// ad router
import Main from '../component/bill/Main';
import Home from '../component/bill/Home';
import Import from '../component/bill/Import.vue';

export default {
  path: 'bills',
  component: Main,
    children: [
        { path: '', component: Home },
        { path: 'import', component: Import },
    ],
};
