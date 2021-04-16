import template from './swag-promotion-detail.html.twig';

const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('swag-promotion-detail', {
    template,

    inject: [
        'repositoryFactory'
    ],

    mixins: [
        Mixin.getByName('notification')
    ],

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    data() {
        return {
            promotion: null,
            isLoading: false,
            processSuccess: false,
            repository: null,
        };
    },

    computed: {
        options() {
            return [
                // { value: 'absolute', name: this.$t('swag-bundle.detail.absoluteText') },
                { value: 'absolute', name: "absolute" },
                // { value: 'percentage', name: this.$t('swag-bundle.detail.percentageText') }
                { value: 'percentage', name: "percentage" }
            ];
        }
    },

    created() {
        this.repository = this.repositoryFactory.create('swag_promotion');
        this.getPromotion();
    },

    methods: {
        getPromotion() {
            this.repository
                .get(this.$route.params.id, Shopware.Context.api)
                .then((entity) => {
                    this.promotion = entity;
                });
        },

        onClickSave() {
            this.isLoading = true;
            this.processSuccess = true;

            this.repository
                .save(this.promotion, Shopware.Context.api)
                .then(() => {
                    this.getPromotion();
                    this.isLoading = false;
                    this.createNotificationSuccess({
                        title: "Success",
                        message: "Update promotion success !"
                    })
                }).catch((exception) => {
                this.isLoading = false;
                this.createNotificationError({
                    title: this.$t('swag-promotion.detail.errorTitle'),
                    message: exception
                });
            });
        },

        saveFinish() {
            this.processSuccess = false;
        }
    }
});
