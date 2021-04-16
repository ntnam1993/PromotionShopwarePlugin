import './page/swag-promotion-list';
import './page/swag-promotion-detail';
import './page/swag-promotion-create';
// import deDE from './snippet/de-DE.json';
// import enGB from './snippet/en-GB.json';

const { Module } = Shopware;
Module.register('swag-promotion', {
    type: 'plugin',
    name: 'Promotion',
    title: 'Promotion',
    description: 'Promotion of product',
    color: '#09b6ff',
    icon: 'default-action-bulk-edit',

    // snippets: {
    //     'de-DE': deDE,
    //     'en-GB': enGB
    // },

    navigation: [{
        label: 'Promotion',
        color: '#09b6ff',
        path: 'swag.promotion.list',
        icon: 'default-action-bulk-edit',
        position: 99
    }],

    routes: {
        list: {
            component: 'swag-promotion-list',
            path: 'list'
        },
        detail: {
            component: 'swag-promotion-detail',
            path: 'detail/:id',
            meta: {
                parentPath: 'swag.promotion.list'
            }
        },
        create: {
            component: 'swag-promotion-create',
            path: 'create',
            meta: {
                parentPath: 'swag.promotion.list'
            }
        }
    },
});
