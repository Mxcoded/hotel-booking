@include('layouts._feedback_modal')
<!-- WhatsApp Lead Capture Modal -->
<div id="whatsapp-modal" class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg p-8 shadow-2xl max-w-sm w-full relative">
        <button id="close-modal-btn"
            class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 text-3xl">&times;</button>
        <h3 class="text-2xl font-bold text-center mb-2">Chat on WhatsApp</h3>
        <p class="text-center text-gray-600 mb-6">Just enter your details below and we'll redirect you instantly.
        </p>
        <form id="whatsapp-lead-form">
            <div class="mb-4">
                <label for="lead-name" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" id="lead-name" name="name" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
            </div>
            <div class="mb-6">
                <label for="lead-phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                <input type="tel" id="lead-phone" name="phone" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
            </div>
            <button type="submit"
                class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded-lg flex items-center justify-center text-lg">
                <i class="fab fa-whatsapp mr-2"></i> Chat Now
            </button>
        </form>
    </div>
</div>

<a href="https://wa.me/{{ setting('whatsapp_number', '+2348099999620') }}?text=Hi,%20I'd%20like%20to%20make%20an%20inquiry%20about%20your%20room%20and%20rates."
    target="_blank"
    class="whatsapp-link fixed bottom-8 right-8 bg-green-500 hover:bg-green-600 text-white rounded-full p-4 shadow-lg transition duration-300 ease-in-out transform hover:scale-110 z-50">
    <i class="fab fa-whatsapp text-4xl"></i>
</a>


<script>
    document.getElementById('mobile-menu-button').addEventListener('click', function() {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Smart Modal Logic
        const modal = document.getElementById('whatsapp-modal');
        const closeModalBtn = document.getElementById('close-modal-btn');
        const leadForm = document.getElementById('whatsapp-lead-form');
        const whatsappLinks = document.querySelectorAll('.whatsapp-link');
        let targetUrl = '';
        whatsappLinks.forEach(link => {
            link.addEventListener('click', function(event) {
                event.preventDefault();
                targetUrl = this.href;
                if (localStorage.getItem('whatsappLead')) {
                    window.open(targetUrl, '_blank');
                } else {
                    modal.classList.remove('hidden');
                }
            });
        });
        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', () => modal.classList.add('hidden'));
        }
        if (leadForm) {
            leadForm.addEventListener('submit', function(event) {
                event.preventDefault();
                const formData = new FormData(this);
                const leadData = {
                    name: formData.get('name'),
                    phone: formData.get('phone')
                };
                fetch("{{ route('whatsapp.lead.store') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(leadData)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            localStorage.setItem('whatsappLead', JSON.stringify(leadData));
                            window.open(targetUrl, '_blank');
                            modal.classList.add('hidden');
                            this.reset();
                        } else {
                            alert('Something went wrong.');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });
        }

        // Favorites Logic
        const favorites = {
            get() {
                return JSON.parse(localStorage.getItem('favoriteRooms') || '[]');
            },
            set(ids) {
                localStorage.setItem('favoriteRooms', JSON.stringify(ids));
                this.updateCount();
            },
            add(id) {
                let ids = this.get();
                if (!ids.includes(id)) {
                    ids.push(id);
                    this.set(ids);
                }
            },
            remove(id) {
                let ids = this.get().filter(i => i !== id);
                this.set(ids);
            },
            toggle(id) {
                let ids = this.get();
                if (ids.includes(id)) {
                    this.remove(id);
                } else {
                    this.add(id);
                }
                this.updateHeart(id);
            },
            updateCount() {
                const count = this.get().length;
                const countEl = document.getElementById('favorites-count');
                if (countEl) {
                    countEl.textContent = count;
                    countEl.classList.toggle('hidden', count === 0);
                }
            },
            updateHeart(roomId) {
                const heart = document.querySelector(`.favorite-btn[data-room-id="${roomId}"] i`);
                if (heart) {
                    const isFavorited = this.get().includes(roomId);
                    heart.classList.toggle('fas', isFavorited); // Solid icon
                    heart.classList.toggle('far', !isFavorited); // Outline icon
                    heart.classList.toggle('text-red-500', isFavorited);
                }
            },
            initHearts() {
                document.querySelectorAll('.favorite-btn').forEach(btn => {
                    const roomId = parseInt(btn.dataset.roomId);
                    this.updateHeart(roomId);
                });
            }
        };

        favorites.updateCount();
        favorites.initHearts();

        window.toggleFavorite = (roomId) => {
            favorites.toggle(roomId);
        };

        // Logic for the favorites page
        if (document.getElementById('favorite-rooms-container')) {
            const container = document.getElementById('favorite-rooms-container');
            const noFavsMsg = document.getElementById('no-favorites-message');
            const spinner = document.getElementById('loading-spinner');
            const favoriteIds = favorites.get();

            if (favoriteIds.length > 0) {
                fetch("{{ route('api.favorites') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content'),
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            ids: favoriteIds
                        })
                    })
                    .then(response => response.json())
                    .then(rooms => {
                        spinner.classList.add('hidden');
                        if (rooms.length > 0) {
                            container.innerHTML = rooms.map(room => {
                                const usd_rate = {{ (float) setting('usd_exchange_rate', 0) }};
                                const priceNaira = new Intl.NumberFormat('en-NG', {
                                    style: 'currency',
                                    currency: 'NGN'
                                }).format(room.price).replace('NGN', '₦');
                                let priceUsd = '';
                                if (usd_rate > 0) {
                                    priceUsd =
                                        `<p class="text-sm">Approx. $${(room.price / usd_rate).toFixed(2)}</p>`;
                                }

                                return `
                                    <div class="bg-gray-50 rounded-lg shadow-lg overflow-hidden relative">
                                        <button onclick="toggleFavorite(${room.id})" class="favorite-btn absolute top-4 right-4 bg-white/80 rounded-full p-2 z-10" data-room-id="${room.id}">
                                            <i class="fas fa-heart text-red-500 text-xl"></i>
                                        </button>
                                        <a href="/rooms/${room.id}">
                                            <img src="/storage/${room.image}" alt="${room.name}" class="w-full h-64 object-cover">
                                        </a>
                                        <div class="p-6">
                                            <h3 class="text-2xl font-bold mb-2">${room.name}</h3>
                                            <div class="text-gray-600 mb-4">
                                                <p class="font-bold text-xl text-gray-900">From ${priceNaira} / night</p>
                                                ${priceUsd}
                                            </div>
                                            <a href="https://wa.me/{{ setting('whatsapp_number', '+2348099999620') }}?text=Hi,%20I'm%20interested%20in%20the%20${encodeURIComponent(room.name)}." target="_blank" class="whatsapp-link bg-gray-800 hover:bg-black text-white font-semibold py-2 px-4 rounded-lg w-full flex items-center justify-center">
                                                <i class="fab fa-whatsapp mr-2"></i> Reserve via WhatsApp
                                            </a>
                                        </div>
                                    </div>
                                `;
                            }).join('');
                        } else {
                            noFavsMsg.classList.remove('hidden');
                        }
                    });
            } else {
                spinner.classList.add('hidden');
                noFavsMsg.classList.remove('hidden');
            }
        }
    // --- Feedback Modal Logic ---
        const feedbackModal = document.getElementById('feedback-modal');
        const feedbackForm = document.getElementById('feedback-form');
        const feedbackLink = document.getElementById('feedback-link');
        const mobileFeedbackLink = document.getElementById('mobile-feedback-link');
        const closeFeedbackBtn = document.getElementById('close-feedback-modal-btn');
        const feedbackFormContent = document.getElementById('feedback-form-content');
        const feedbackSuccessMessage = document.getElementById('feedback-success-message');
        const submitBtn = document.getElementById('feedback-submit-btn');

        function openFeedbackModal(e) {
            e.preventDefault();
            feedbackModal.classList.remove('hidden');
        }

        function closeFeedbackModal() {
            feedbackModal.classList.add('hidden');
            setTimeout(() => {
                feedbackFormContent.classList.remove('hidden');
                feedbackSuccessMessage.classList.add('hidden');
                feedbackForm.reset();
                clearFeedbackErrors();
            }, 500);
        }
        
        function clearFeedbackErrors() {
            document.querySelectorAll('.form-error').forEach(el => el.textContent = '');
        }

        feedbackLink.addEventListener('click', openFeedbackModal);
        mobileFeedbackLink.addEventListener('click', openFeedbackModal);
        closeFeedbackBtn.addEventListener('click', closeFeedbackModal);

        feedbackForm.addEventListener('submit', function(e) {
            e.preventDefault();
            clearFeedbackErrors();
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Submitting...';

            const formData = new FormData(this);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch('{{ route('feedback.store') }}', {
                    method: 'POST', // <-- THIS IS THE FIX
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                    body: formData
                })
            .then(response => response.json().then(data => ({ status: response.status, body: data })))
            .then(({ status, body }) => {
                if (status === 200 && body.success) {
                    feedbackFormContent.classList.add('hidden');
                    feedbackSuccessMessage.classList.remove('hidden');
                    setTimeout(closeFeedbackModal, 2000);
                } else if (status === 422) { // Validation error
                    Object.keys(body.errors).forEach(key => {
                        const errorEl = document.getElementById(`${key}-error`);
                        if(errorEl) {
                           errorEl.textContent = body.errors[key][0];
                        }
                    });
                }
            })
            .catch(error => console.error('Error:', error))
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Submit Feedback';
            });
        });


        // Initial setup on page load
        // updateFavoritesCount();
        // updateFavoriteIcons();
    });
</script>
