const { Component } = Shopware;

Component.extend('swag-promotion-create', 'swag-promotion-detail', {
    methods: {
        getPromotion() {
            this.promotion = this.repository.create(Shopware.Context.api);
        },

        onClickSave() {
            this.isLoading = true;
            this.repository
                .save(this.promotion, Shopware.Context.api)
                .then(() => {
                    this.isLoading = false;
                    this.$router.push({ name: 'swag.promotion.detail', params: { id: this.promotion.id } });
                    this.createNotificationSuccess({
                        title: "Success",
                        message: "Create promotion success !"
                    })
                }).catch((exception) => {
                this.isLoading = false;

                this.createNotificationError({
                    title: this.$t('swag-promotion.detail.errorTitle'),
                    message: exception
                });
            });
        }
    }
});
