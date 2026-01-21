"""
Test Database Connection Script
Run this before starting the main API to verify database connectivity
"""

from config import config
from sqlalchemy import create_engine, text
import sys


def test_connection():
    """Test database connection and display table statistics"""

    print("=" * 60)
    print("Testing Database Connection")
    print("=" * 60)

    # Display configuration
    print(f"Host    : {config.DB_HOST}")
    print(f"Port    : {config.DB_PORT}")
    print(f"User    : {config.DB_USER}")
    print(f"Database: {config.DB_NAME}")
    print("-" * 60)

    try:
        # Create engine
        engine = create_engine(config.database_url, pool_pre_ping=True)

        with engine.connect() as conn:
            # Test basic query
            result = conn.execute(text("SELECT VERSION()"))
            mysql_version = result.fetchone()[0]
            print(f"MySQL Version: {mysql_version}")
            print()

            # Test each table
            print("Table Statistics:")
            print("-" * 60)

            tables = [
                "users",
                "tours",
                "bookings",
                "reviews",
                "tour_schedules",
                "tour_itineraries",
                "tour_village",
                "villages",
                "provinces",
                "regions",
            ]

            for table in tables:
                try:
                    result = conn.execute(text(f"SELECT COUNT(*) FROM {table}"))
                    count = result.fetchone()[0]
                    status = "✓" if count > 0 else "⚠"
                    print(f"{status} {table:20s}: {count:5d} records")
                except Exception as e:
                    print(f"✗ {table:20s}: Error - {str(e)[:40]}")

            print("-" * 60)

            # Test specific queries for recommendations
            print()
            print("Recommendation Data Check:")
            print("-" * 60)

            # Check active tours
            result = conn.execute(
                text("SELECT COUNT(*) FROM tours WHERE status = 'active'")
            )
            active_tours = result.fetchone()[0]
            print(f"Active Tours        : {active_tours}")

            # Check users with bookings
            result = conn.execute(
                text(
                    """
                SELECT COUNT(DISTINCT user_id) 
                FROM bookings 
                WHERE status NOT IN ('canceled')
            """
                )
            )
            users_with_bookings = result.fetchone()[0]
            print(f"Users with Bookings : {users_with_bookings}")

            # Check users with reviews
            result = conn.execute(text("SELECT COUNT(DISTINCT user_id) FROM reviews"))
            users_with_reviews = result.fetchone()[0]
            print(f"Users with Reviews  : {users_with_reviews}")

            print("-" * 60)

        engine.dispose()

        print()
        print("TEST PASSED! Database is ready for recommendation service.")
        print()
        print("You can now run:")
        print("python main.py")
        print()
        return True

    except Exception as e:
        print()
        print(f"TEST FAILED: {e}")
        print()
        print("Troubleshooting:")
        print("1. Check if MySQL is running")
        print("2. Verify .env file configuration")
        print("3. Confirm database exists:")
        print(f"CREATE DATABASE {config.DB_NAME};")
        print("4. Check user permissions:")
        print(
            f"GRANT ALL ON {config.DB_NAME}.* TO '{config.DB_USER}'@'localhost';"
        )
        print()
        return False


if __name__ == "__main__":
    success = test_connection()
    sys.exit(0 if success else 1)
