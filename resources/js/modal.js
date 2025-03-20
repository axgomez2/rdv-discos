window.modal = (id) => ({
    show: false,
    id: id,
    init() {
        window.addEventListener(`open-modal`, (event) => {
            if (event.detail === this.id) {
                this.show = true;
            }
        });

        window.addEventListener(`close-modal`, () => {
            this.show = false;
        });
    }
});
