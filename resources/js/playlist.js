document.addEventListener('alpine:init', () => {
    Alpine.data('playlistForm', (data = { vinyls: [], currentImage: null }) => ({
        loading: false,
        saveLoading: false,
        modalTitle: '',
        modalMessage: '',
        modalStatus: '',
        imageUrl: null,
        currentImage: data.currentImage,
        selectedVinyls: data.vinyls || [],
        search: '',
        isOpen: false,
        results: [],

        init() {
            // Initialize any required setup
        },

        previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                this.imageUrl = URL.createObjectURL(file);
            }
        },

        async searchVinyls() {
            if (this.search.length < 2) {
                this.results = [];
                return;
            }

            try {
                const searchUrl = document.querySelector('meta[name="vinyl-search-url"]')?.content;
                if (!searchUrl) throw new Error('Search URL not found');

                const response = await fetch(`${searchUrl}?q=${encodeURIComponent(this.search)}`);
                if (!response.ok) throw new Error('Search failed');

                const data = await response.json();
                this.results = data.map(vinyl => ({
                    id: vinyl.id,
                    vinyl_master_id: vinyl.vinyl_master_id,
                    vinyl_sec_id: vinyl.id,
                    title: vinyl.vinyl_master.title,
                    artist: vinyl.vinyl_master.artists.map(a => a.name).join(', '),
                    display_title: `${vinyl.vinyl_master.title} - ${vinyl.vinyl_master.artists.map(a => a.name).join(', ')}`
                }));
            } catch (error) {
                console.error('Search error:', error);
                this.results = [];
            }
        },

        addVinyl(vinyl) {
            if (this.selectedVinyls.length >= 10) {
                alert('Maximum 10 vinyls allowed');
                return;
            }

            if (!this.selectedVinyls.some(v => v.vinyl_sec_id === vinyl.vinyl_sec_id)) {
                this.selectedVinyls.push({
                    vinyl_master_id: vinyl.vinyl_master_id,
                    vinyl_sec_id: vinyl.vinyl_sec_id,
                    title: vinyl.display_title
                });
            }
        },

        removeVinyl(index) {
            this.selectedVinyls.splice(index, 1);
        }
    }));
});
