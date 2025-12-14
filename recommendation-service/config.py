"""
Configuration Manager for Craft Tour Recommendation Service
"""

import os
from dotenv import load_dotenv

# Load environment variables from .env file
load_dotenv()


class Config:
    """Configuration class for database and API settings"""

    # ===== DATABASE CONFIGURATION =====
    DB_HOST = os.getenv("DB_HOST", "127.0.0.1")
    DB_USER = os.getenv("DB_USER", "root")
    DB_PASSWORD = os.getenv("DB_PASSWORD", "")
    DB_NAME = os.getenv("DB_NAME", "craft_tour_management_booking_recommendation")
    DB_PORT = int(os.getenv("DB_PORT", 3306))

    # ===== API SERVER CONFIGURATION =====
    API_HOST = os.getenv("API_HOST", "0.0.0.0")
    API_PORT = int(os.getenv("API_PORT", 5555))
    DEBUG = os.getenv("DEBUG", "True").lower() == "true"

    # ===== RECOMMENDATION SETTINGS =====
    DEFAULT_LIMIT = int(os.getenv("DEFAULT_LIMIT", 6))
    MAX_LIMIT = int(os.getenv("MAX_LIMIT", 20))

    @property
    def database_url(self):
        """Generate SQLAlchemy database URL"""
        return f"mysql+mysqlconnector://{self.DB_USER}:{self.DB_PASSWORD}@{self.DB_HOST}:{self.DB_PORT}/{self.DB_NAME}"

    def validate(self):
        """Validate configuration"""
        if not self.DB_NAME:
            raise ValueError("DB_NAME is required in .env file")
        if not self.DB_USER:
            raise ValueError("DB_USER is required in .env file")
        return True


# Create global config instance
config = Config()

# Validate on import
try:
    config.validate()
    print("Configuration loaded successfully")
except ValueError as e:
    print(f"Configuration Error: {e}")
    exit(1)
