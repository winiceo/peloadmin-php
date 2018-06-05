// ad router
import Main from '../component/order/Main';
import Home from '../component/order/Home';

export default {
  path: 'orders',
  component: Main,
    children: [
        { path: '', component: Home },
    ],
};
