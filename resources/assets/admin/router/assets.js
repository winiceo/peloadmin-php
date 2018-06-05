import Main from '../components/modules/asset/Main';
import Config from '../components/pages/asset/Config';
import Statistics from '../components/pages/asset/Statistics';
import Water from '../components/pages/asset/Water';
import Cash from '../components/pages/asset/Cash';
import Currency from '../components/pages/asset/Currency';

export default {
    path: 'assets',
    component: Main,
    children: [
    	{path: '', component: Currency},
        {path: 'config', component: Config},
        {path: 'statistics', component: Statistics},
        {path: 'waters', component: Water},
        {path: 'cashes', component: Cash}
    ]
}