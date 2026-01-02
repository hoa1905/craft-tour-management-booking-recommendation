"""
Craft Tour Recommendation Service
Content-Based Filtering API for Tour Recommendations
"""

import warnings

warnings.filterwarnings("ignore")

from flask import Flask, jsonify, request
from flask_cors import CORS
from sqlalchemy import create_engine, text
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity
import pandas as pd
from nltk.tokenize import word_tokenize
import logging
from config import config

# ===== FLASK APP SETUP =====
app = Flask(__name__)
CORS(app)

# ===== LOGGING SETUP =====
logging.basicConfig(
    level=logging.INFO, format="%(asctime)s - %(levelname)s - %(message)s"
)
logger = logging.getLogger(__name__)

# ===== VIETNAMESE STOPWORDS =====
VIETNAMESE_STOPWORDS = [
    "và",
    "là",
    "của",
    "các",
    "đã",
    "trong",
    "khi",
    "này",
    "một",
    "những",
    "được",
    "với",
    "cho",
    "thì",
    "tại",
    "bởi",
    "về",
    "để",
    "nếu",
    "sẽ",
    "không",
    "có",
    "đây",
    "đó",
    "này",
    "thấy",
    "ra",
    "phải",
    "ai",
    "gì",
    "nào",
    "lại",
    "hơn",
    "như",
    "vậy",
    "chỉ",
    "làm",
    "lúc",
    "người",
    "năm",
    "ngày",
    "tour",
    "du",
    "lịch",
]


# ===== Kết nối cơ sở dữ liệu =====
def create_connection():
    """Create database connection with error handling"""
    try:
        engine = create_engine(
            config.database_url,
            pool_pre_ping=True,
            pool_recycle=3600,
            pool_size=10,
            max_overflow=20,
        )

        # Test connection
        with engine.connect() as conn:
            conn.execute(text("SELECT 1"))

        logger.info("Database connection successful!")
        return engine
    except Exception as e:
        logger.error(f"Database connection failed: {e}")
        return None


def close_connection(engine):
    """Close database connection"""
    if engine:
        engine.dispose()
        logger.info("Database connection closed")


# ===== DATA FETCHING =====
def fetch_tours(engine):
    """Lấy tất cả dữ liệu tour trạng thái 'active' từ cơ sở dữ liệu."""
    try:
        query = """
            SELECT 
                t.tour_id,
                t.tour_name,
                t.description,
                t.price_adult,
                t.price_child,
                t.duration,
                t.departure_point,
                t.destination,
                t.transport,
                t.status
            FROM tours t
            WHERE t.status = 'active'
        """
        df = pd.read_sql(query, engine)
        logger.info(f"Fetched {len(df)} active tours")
        return df
    except Exception as e:
        logger.error(f"Error fetching tours: {e}")
        return pd.DataFrame()


# ===== TEXT PROCESSING =====
def combine_features(row):
    """Combine tour features into single text"""
    features = [
        "tour_name",
        "description",
        "duration",
        "destination",
        "transport",
        "departure_point",
    ]
    return " ".join([str(row[f]) for f in features if f in row and pd.notnull(row[f])])


# Hàm tiền xử lý văn bản
def preprocess_text(text):
    """
    Tiền xử lý văn bản bằng cách loại bỏ stop words và giữ lại các từ quan trọng.
    Args:
        text (str): Văn bản gốc.
    Returns:
        str: Văn bản đã được xử lý.
    """
    if pd.isnull(text):
        return ""

    # Chuyển thành chữ thường
    text = str(text).lower()

    try:
        # Tokenize văn bản
        tokens = word_tokenize(text)
    except:
        tokens = text.split()

    # Loại bỏ stop words
    processed = [
        word for word in tokens if word not in VIETNAMESE_STOPWORDS and word.isalnum()
    ]

    # Kết hợp lại thành chuỗi
    return " ".join(processed)


# ===== API ENDPOINTS =====


@app.route("/api/health", methods=["GET"])
def health_check():
    """Health check endpoint"""
    return jsonify(
        {
            "status": "OK",
            "service": "Craft Tour Recommendation API",
            "version": "1.0.0",
            "endpoints": [
                "GET /api/health",
                "GET /api/tour-recommendations?tour_id={id}&limit={n}",
                "GET /api/user-recommendations?user_id={id}&limit={n}",
                "GET /api/search-tours?keyword={text}&limit={n}",
            ],
        }
    )


@app.route("/api/tour-recommendations", methods=["GET"])
def get_tour_recommendations():
    """
    Get similar tours based on a given tour ID

    Query Parameters:
        tour_id (int): ID of the tour
        limit (int): Number of recommendations (default: 4)

    Returns:
        JSON: List of recommended tour IDs
    """
    tour_id = request.args.get("tour_id")
    limit = int(request.args.get("limit", config.DEFAULT_LIMIT))

    # Validate
    if not tour_id or not tour_id.isdigit():
        return jsonify({"success": False, "error": "Invalid tour_id parameter"}), 400

    tour_id = int(tour_id)
    limit = min(limit, config.MAX_LIMIT)

    # Connect to database
    engine = create_connection()
    if not engine:
        return jsonify({"success": False, "error": "Database connection failed"}), 500

    try:
        # Fetch tours
        tours_df = fetch_tours(engine)
        if tours_df.empty:
            return jsonify({"success": False, "error": "No tours found"}), 404

        # Check if tour exists
        if tour_id not in tours_df["tour_id"].values:
            return (
                jsonify({"success": False, "error": f"Tour ID {tour_id} not found"}),
                404,
            )

        # Combine features
        tours_df["combined_features"] = tours_df.apply(combine_features, axis=1)
        tours_df["processed_features"] = tours_df["combined_features"].apply(
            preprocess_text
        )

        # TF-IDF
        tfidf = TfidfVectorizer(max_features=500, ngram_range=(1, 2))
        tfidf_matrix = tfidf.fit_transform(tours_df["processed_features"])

        # Cosine similarity
        cosine_sim = cosine_similarity(tfidf_matrix, tfidf_matrix)

        # Get index
        tour_index = tours_df[tours_df["tour_id"] == tour_id].index[0]

        # Calculate similarity scores
        sim_scores = list(enumerate(cosine_sim[tour_index]))
        sim_scores_sorted = sorted(sim_scores, key=lambda x: x[1], reverse=True)[
            1 : limit + 1
        ]

        # Get tour IDs
        similar_indices = [i[0] for i in sim_scores_sorted]
        related_tours = tours_df.iloc[similar_indices]["tour_id"].tolist()

        return jsonify(
            {
                "success": True,
                "tour_id": tour_id,
                "related_tours": related_tours,
                "count": len(related_tours),
            }
        )

    except Exception as e:
        logger.error(f"Error in tour recommendations: {e}")
        return jsonify({"success": False, "error": str(e)}), 500

    finally:
        close_connection(engine)


@app.route("/api/user-recommendations", methods=["GET"])
def get_user_recommendations():
    """
    Get personalized tour recommendations for a user

    Query Parameters:
        user_id (int): ID of the user
        limit (int): Number of recommendations (default: 4)

    Returns:
        JSON: List of recommended tour IDs
    """
    user_id = request.args.get("user_id")
    limit = int(request.args.get("limit", config.DEFAULT_LIMIT))

    # Validate
    if not user_id or not user_id.isdigit():
        return jsonify({"success": False, "error": "Invalid user_id parameter"}), 400

    user_id = int(user_id)
    limit = min(limit, config.MAX_LIMIT)

    engine = create_connection()
    if not engine:
        return jsonify({"success": False, "error": "Database connection failed"}), 500

    try:
        # Get user interaction history
        bookings_query = """
            SELECT DISTINCT tour_id 
            FROM bookings 
            WHERE user_id = %(user_id)s 
            AND status NOT IN ('canceled')
        """

        reviews_query = """
            SELECT DISTINCT tour_id, rating
            FROM reviews 
            WHERE user_id = %(user_id)s
        """

        user_bookings = pd.read_sql(bookings_query, engine, params={"user_id": user_id})
        user_reviews = pd.read_sql(reviews_query, engine, params={"user_id": user_id})

        # Get interacted tours
        interacted_tours = []
        if not user_bookings.empty:
            interacted_tours.extend(user_bookings["tour_id"].tolist())
        if not user_reviews.empty:
            interacted_tours.extend(user_reviews["tour_id"].tolist())

        interacted_tours = list(set(interacted_tours))

        # If no history, return popular tours
        if not interacted_tours:
            popular_query = """
                SELECT t.tour_id, COUNT(b.booking_id) as booking_count
                FROM tours t
                LEFT JOIN bookings b ON t.tour_id = b.tour_id
                WHERE t.status = 'active'
                GROUP BY t.tour_id
                ORDER BY booking_count DESC
                LIMIT %(limit)s
            """
            popular_tours = pd.read_sql(popular_query, engine, params={"limit": limit})

            return jsonify(
                {
                    "success": True,
                    "user_id": user_id,
                    "recommended_tours": popular_tours["tour_id"].tolist(),
                    "message": "Based on popularity (user has no history)",
                    "count": len(popular_tours),
                }
            )

        # Get all tours
        all_tours = fetch_tours(engine)

        # Remove interacted tours
        candidate_tours = all_tours[~all_tours["tour_id"].isin(interacted_tours)].copy()

        if candidate_tours.empty:
            return jsonify(
                {
                    "success": True,
                    "user_id": user_id,
                    "recommended_tours": [],
                    "message": "User has viewed all available tours",
                    "count": 0,
                }
            )

        # Combine features
        all_tours["combined_features"] = all_tours.apply(combine_features, axis=1)
        all_tours["processed_features"] = all_tours["combined_features"].apply(
            preprocess_text
        )

        # TF-IDF
        tfidf = TfidfVectorizer(max_features=500, ngram_range=(1, 2))
        tfidf_matrix = tfidf.fit_transform(all_tours["processed_features"])

        # Cosine similarity
        cosine_sim = cosine_similarity(tfidf_matrix, tfidf_matrix)

        # Calculate similarity
        interacted_indices = all_tours[
            all_tours["tour_id"].isin(interacted_tours)
        ].index.tolist()
        candidate_indices = candidate_tours.index.tolist()

        if interacted_indices and candidate_indices:
            sim_scores = cosine_sim[interacted_indices][:, candidate_indices].mean(
                axis=0
            )
            candidate_tours["similarity"] = sim_scores
        else:
            candidate_tours["similarity"] = 0

        # Get average ratings
        avg_rating_query = """
            SELECT tour_id, AVG(rating) as avg_rating, COUNT(*) as review_count
            FROM reviews
            GROUP BY tour_id
        """
        avg_ratings = pd.read_sql(avg_rating_query, engine)

        # Merge ratings
        candidate_tours = candidate_tours.merge(avg_ratings, on="tour_id", how="left")
        candidate_tours["avg_rating"].fillna(0, inplace=True)
        candidate_tours["review_count"].fillna(0, inplace=True)

        # Calculate final score
        candidate_tours["final_score"] = (
            candidate_tours["similarity"] * 0.7
            + (candidate_tours["avg_rating"] / 5) * 0.3
        )

        # Sort and get top N
        recommended = candidate_tours.nlargest(limit, "final_score")

        return jsonify(
            {
                "success": True,
                "user_id": user_id,
                "recommended_tours": recommended["tour_id"].tolist(),
                "count": len(recommended),
            }
        )

    except Exception as e:
        logger.error(f"Error in user recommendations: {e}")
        return jsonify({"success": False, "error": str(e)}), 500

    finally:
        close_connection(engine)


@app.route("/api/search-tours", methods=["GET"])
def search_tours():
    """
    Search tours by keyword

    Query Parameters:
        keyword (str): Search keyword
        limit (int): Number of results (default: 4)

    Returns:
        JSON: List of matching tour IDs
    """
    keyword = request.args.get("keyword")
    limit = int(request.args.get("limit", 4))

    if not keyword:
        return jsonify({"success": False, "error": "Missing keyword parameter"}), 400

    limit = min(limit, config.MAX_LIMIT)

    engine = create_connection()
    if not engine:
        return jsonify({"success": False, "error": "Database connection failed"}), 500

    try:
        tours_df = fetch_tours(engine)
        if tours_df.empty:
            return jsonify({"success": False, "error": "No tours found"}), 404

        # Combine features
        tours_df["combined_features"] = tours_df.apply(
            lambda row: f"{row['tour_name']} {row['description']} {row['destination']} {row['departure_point']}",
            axis=1,
        )

        # Preprocess
        tours_df["processed_features"] = tours_df["combined_features"].apply(
            preprocess_text
        )
        processed_keyword = preprocess_text(keyword)

        # TF-IDF
        tfidf = TfidfVectorizer(max_features=500, ngram_range=(1, 2))
        tfidf_matrix = tfidf.fit_transform(tours_df["processed_features"])
        keyword_vector = tfidf.transform([processed_keyword])

        # Cosine similarity
        cosine_sim = cosine_similarity(keyword_vector, tfidf_matrix)

        # Rank results
        sim_scores = list(enumerate(cosine_sim[0]))
        sim_scores_sorted = sorted(sim_scores, key=lambda x: x[1], reverse=True)[:limit]

        # Filter results with similarity > 0
        similar_indices = [i[0] for i in sim_scores_sorted if i[1] > 0]

        if not similar_indices:
            return jsonify(
                {
                    "success": True,
                    "keyword": keyword,
                    "related_tours": [],
                    "message": "No matching tours found",
                    "count": 0,
                }
            )

        related_tours = tours_df.iloc[similar_indices]["tour_id"].tolist()

        return jsonify(
            {
                "success": True,
                "keyword": keyword,
                "related_tours": related_tours,
                "count": len(related_tours),
            }
        )

    except Exception as e:
        logger.error(f"Error in search: {e}")
        return jsonify({"success": False, "error": str(e)}), 500

    finally:
        close_connection(engine)


# ===== MAIN =====
if __name__ == "__main__":
    print("=" * 60)
    print("🚀 Craft Tour Recommendation Service")
    print("=" * 60)
    print(f"Database: {config.DB_NAME}")
    print(f"Running on: http://{config.API_HOST}:{config.API_PORT}")
    print(f"Debug Mode: {config.DEBUG}")
    print("=" * 60)

    app.run(host=config.API_HOST, port=config.API_PORT, debug=config.DEBUG)
