<div id="feedback-form-content">
    <h3 class="text-2xl font-bold text-center mb-2">Leave Your Feedback</h3>
    <p class="text-center text-gray-700 mb-6">We'd love to hear about your experience!</p>
    <form id="feedback-form" action="{{ route('feedback.store') }}" method="POST">
        @csrf
        <div class="mb-4 star-rating flex items-center justify-center flex-row-reverse">
            <input type="radio" id="star5" name="rating" value="5" required/><label for="star5" title="5 stars">&#9733;</label>
            <input type="radio" id="star4" name="rating" value="4" /><label for="star4" title="4 stars">&#9733;</label>
            <input type="radio" id="star3" name="rating" value="3" /><label for="star3" title="3 stars">&#9733;</label>
            <input type="radio" id="star2" name="rating" value="2" /><label for="star2" title="2 stars">&#9733;</label>
            <input type="radio" id="star1" name="rating" value="1" /><label for="star1" title="1 star">&#9733;</label>
        </div>
        <span id="rating-error" class="form-error block text-red-500 text-xs text-center mb-2"></span>

        <div class="mb-4">
            <label for="feedback-message" class="block text-sm font-medium text-gray-700">Your Message</label>
            <textarea id="feedback-message" name="message" rows="4" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"></textarea>
            <span id="message-error" class="form-error text-red-500 text-xs"></span>
        </div>
        <div class="grid grid-cols-2 gap-4">
             <div>
                <label for="feedback-name" class="block text-sm font-medium text-gray-700">Name (Optional)</label>
                <input type="text" id="feedback-name" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
            </div>
            <div>
                <label for="feedback-email" class="block text-sm font-medium text-gray-700">Email (Optional)</label>
                <input type="email" id="feedback-email" name="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
            </div>
        </div>
        <button type="submit" id="feedback-submit-btn" class="mt-6 w-full bg-orange-500 hover:bg-orange-900 text-white font-bold py-3 px-4 rounded-lg flex items-center justify-center text-lg">
            Submit Feedback
        </button>
    </form>
</div>
 <div id="feedback-success-message" class="hidden text-center">
    <div class="text-green-500 mb-4">
        <i class="fas fa-check-circle fa-4x"></i>
    </div>
    <h3 class="text-2xl font-bold">Thank You!</h3>
    <p class="text-gray-600 mt-2">Your feedback has been submitted successfully.</p>
</div>