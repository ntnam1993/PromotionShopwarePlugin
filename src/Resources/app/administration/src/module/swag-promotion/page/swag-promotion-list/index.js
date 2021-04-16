import template from './swag-promotion-list.html.twig';

const { Component } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('swag-promotion-list', {
    template,

    inject: [
        'repositoryFactory'
    ],

    data() {
        return {
            repository: null,
            promotions: null
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    computed: {
        columns() {
            return [{
                property: 'name',
                dataIndex: 'name',
                label: this.$t('swag-promotion.list.columnName'),
                routerLink: 'swag.promotion.detail',
                inlineEdit: 'string',
                allowResize: true,
                primary: true
            }, {
                property: 'discountRate',
                dataIndex: 'discountRate',
                label: this.$t('swag-promotion.list.columnDiscount'),
                inlineEdit: 'number',
                allowResize: true
            }, {
                property: 'startDate',
                dataIndex: 'startDate',
                label: this.$t('swag-promotion.list.columnStartDate'),
                allowResize: true
            }, {
                property: 'expiredDate',
                dataIndex: 'expiredDate',
                label: this.$t('swag-promotion.list.columnExpiredDate'),
                allowResize: true
            }];
        }
    },

    created() {
        this.repository = this.repositoryFactory.create('swag_promotion');

        this.repository
            .search(new Criteria(), Shopware.Context.api)
            .then((result) => {
                this.promotions = result;
            });
    }
});
