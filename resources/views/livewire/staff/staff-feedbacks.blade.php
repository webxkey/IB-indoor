
<style>

    :root {
        --primary-color: #19722d;
        --primary-gradient: linear-gradient(135deg, #195f0b 0%, #198754 100%);
        --secondary-color: #6c757d;
    
        --badminton-color: #2196F3;
        --tennis-color: #FF9800;
        --squash-color: #9C27B0;
        --basketball-color: #F44336;
        --available-color: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        --available-border: #bae6fd;
        --available-hover: linear-gradient(135deg, #0ea5e9 0%, #19722d 100%);
        --booked-color: linear-gradient(135deg, #fef2f2 0%, #fecaca 100%);
        --booked-border: #fca5a5;
        --pending-color: linear-gradient(135deg, #fffbeb 0%, #fed7aa 100%);
        --pending-border: #fdba74;
        --background-color: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        --card-bg: #ffffff;
        --border-radius: 16px;
        --small-radius: 12px;
        --shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        --small-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
    }
    .main{
        padding: 5px 25px;
    }
    .feedback-header {
        background: var(--primary-gradient);
        color: white;
        padding: 20px;
        border-radius: var(--border-radius);
        margin-bottom: 24px;
        margin-top: 20px;
        box-shadow: var(--shadow);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .feedback-header h2 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 700;
    }

    .feedback-stats {
        background: var(--card-bg);
        padding: 20px;
        margin: 20px 0;
        border-radius: var(--border-radius);
        box-shadow: var(--small-shadow);
        margin-bottom: 24px;
    }

    .stat-item {
        text-align: center;
        padding: 15px;
        border-right: 1px solid #e2e8f0;
    }

    .stat-item:last-child {
        border-right: none;
    }

    .stat-item h3 {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--total-revenue-text);
        margin-bottom: 5px;
    }

    .stat-item p {
        color: var(--subtitle-text);
        font-size: 0.9rem;
        margin: 0;
    }

    .rating-stars {
        color: #f59e0b;
        font-size: 1.2rem;
        margin-left: 5px;
    }

    .review-card {
        background: var(--card-bg);
        border-radius: 15px;
        border: 1px solid gray;
        box-shadow: var(--small-shadow);
        padding: 15px;
        margin-bottom: 20px;
        display: flex;
        align-items: flex-start;
    }

    .review-image {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        margin-right: 15px;
        object-fit: cover;
    }

    .review-content h4 {
        font-size: 1rem;
        font-weight: 600;
        margin: 0 0 5px 0;
        color: #374151;
    }

    .review-content p {
        font-size: 0.9rem;
        color: #4b5563;
        margin: 0 0 10px 0;
    }

    .review-date {
        font-size: 0.75rem;
        color: #6c757d;
        margin-bottom: 10px;
    }

    .review-actions {
        display: flex;
        gap: 10px;
    }

    .btn-feedback {
        background: #195f0b;
        color: white;
        padding: 5px 10px;
        font-size: 0.8rem;
        border-radius: var(--small-radius);
        transition: all 0.3s ease;
    }

    .btn-feedback:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
    }

    .btn-message {
        background: #195f0b;
        color: white;
        padding: 5px 10px;
        font-size: 0.8rem;
        border-radius: var(--small-radius);
        transition: all 0.3s ease;
    }

    .btn-message:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(139, 92, 246, 0.25);
    }
</style>


<div class="container-fluid main">
    <!-- Header -->
    <div class="feedback-header">
        <h2>Reviews</h2>
        <div class="d-flex align-items-center">
            <span>March 21 - February 2022</span>
        </div>
    </div>

    <!-- Stats -->
    <div class="feedback-stats row">
        <div class="col-md-4 stat-item">
            <h3>{{ number_format($totalReviews) }}</h3>
            <p>Total Reviews</p>
        </div>
        <div class="col-md-4 stat-item">
            <h3>{{ number_format($averageRating, 1) }} <span class="rating-stars">★★★★★</span></h3>
            <p>Average Rating</p>
        </div>
        <div class="col-md-4 stat-item">
            <h3>2.0k</h3>
            <p>Growth in Reviews</p>
        </div>
    </div>

    <!-- Reviews List -->
    <div class="row g-3">
        @foreach ($reviews as $review)
            <div class="col-12 col-md-6 col-lg-6">
                <div class="review-card">
                    <img src="{{ $review['user']['profile_image'] }}" alt="{{ $review['user']['name'] }}" class="review-image">
                    <div class="review-content">
                        <h4>{{ $review['user']['name'] }} <span class="rating-stars">★★★★★</span></h4>
                        <p class="review-date">{{ \Carbon\Carbon::parse($review['created_at'])->format('d-m-Y') }} on Etsy</p>
                        <p>{{ $review['comment'] }}</p>
                        <div class="review-actions">
                            <button class="btn btn-feedback">Public Comment</button>
                            <button class="btn btn-message">Direct Message <i class="fas fa-heart"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
        
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>