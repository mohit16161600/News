<?php
// Global News Website
// This script fetches news from around the world using NewsAPI

// Configuration
$apiKey = "1d3cc457b01f46d2b0c05b3eb9c0ad97"; // Replace with your NewsAPI key
$defaultCategory = "general";
$defaultCountry = "us";

// Get parameters from URL for filtering
$category = isset($_GET['category']) ? $_GET['category'] : $defaultCategory;
$country = isset($_GET['country']) ? $_GET['country'] : $defaultCountry;
$searchQuery = isset($_GET['q']) ? $_GET['q'] : "";

// Available categories and countries for filtering
$categories = ["business", "entertainment", "general", "health", "science", "sports", "technology"];
$countries = [
    "ae" => "UAE", "ar" => "Argentina", "at" => "Austria", "au" => "Australia", 
    "be" => "Belgium", "bg" => "Bulgaria", "br" => "Brazil", "ca" => "Canada", 
    "ch" => "Switzerland", "cn" => "China", "co" => "Colombia", "cu" => "Cuba", 
    "cz" => "Czech Republic", "de" => "Germany", "eg" => "Egypt", "fr" => "France", 
    "gb" => "United Kingdom", "gr" => "Greece", "hk" => "Hong Kong", "hu" => "Hungary", 
    "id" => "Indonesia", "ie" => "Ireland", "il" => "Israel", "in" => "India", 
    "it" => "Italy", "jp" => "Japan", "kr" => "South Korea", "lt" => "Lithuania", 
    "lv" => "Latvia", "ma" => "Morocco", "mx" => "Mexico", "my" => "Malaysia", 
    "ng" => "Nigeria", "nl" => "Netherlands", "no" => "Norway", "nz" => "New Zealand", 
    "ph" => "Philippines", "pl" => "Poland", "pt" => "Portugal", "ro" => "Romania", 
    "rs" => "Serbia", "ru" => "Russia", "sa" => "Saudi Arabia", "se" => "Sweden", 
    "sg" => "Singapore", "si" => "Slovenia", "sk" => "Slovakia", "th" => "Thailand", 
    "tr" => "Turkey", "tw" => "Taiwan", "ua" => "Ukraine", "us" => "United States", 
    "ve" => "Venezuela", "za" => "South Africa"
];

// Build API URL
$apiUrl = "https://newsapi.org/v2/top-headlines?";
if (!empty($searchQuery)) {
    $apiUrl .= "q=" . urlencode($searchQuery) . "&";
}
$apiUrl .= "country=" . urlencode($country) . "&";
$apiUrl .= "category=" . urlencode($category) . "&";
$apiUrl .= "apiKey=" . $apiKey;

// Fetch news data
function fetchNews($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        return ["status" => "error", "message" => curl_error($ch)];
    }
    
    curl_close($ch);
    return json_decode($response, true);
}

// Get news data
$newsData = fetchNews($apiUrl);

// Format date
function formatDate($dateString) {
    $date = new DateTime($dateString);
    return $date->format('F j, Y - g:i A');
}

// Handle errors if API request fails
$error = null;
if (isset($newsData['status']) && $newsData['status'] === 'error') {
    $error = $newsData['message'] ?? 'An error occurred while fetching news';
}

// Function to truncate text to a specific length
function truncateText($text, $length = 150) {
    if (strlen($text) <= $length) {
        return $text;
    }
    
    return substr($text, 0, $length) . '...';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global News Aggregator</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #34495e;
            --text-color: #333;
            --light-text: #7f8c8d;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-color: var(--light-color);
        }
        
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        header {
            background-color: var(--primary-color);
            color: white;
            padding: 20px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        header h1 {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        nav {
            background-color: var(--dark-color);
            padding: 10px 0;
        }
        
        .filter-controls {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            padding: 10px 0;
        }
        
        .filter-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        select, input[type="text"], button {
            padding: 8px 12px;
            border-radius: 4px;
            border: 1px solid #ddd;
            font-size: 1rem;
        }
        
        button {
            background-color: var(--secondary-color);
            color: white;
            cursor: pointer;
            border: none;
            transition: background-color 0.3s;
        }
        
        button:hover {
            background-color: #2980b9;
        }
        
        .search-form {
            display: flex;
            gap: 10px;
        }
        
        .news-container {
            margin: 20px 0;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .news-card {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .news-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .news-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
        
        .news-image-placeholder {
            width: 100%;
            height: 180px;
            background-color: #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
        }
        
        .news-content {
            padding: 15px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        
        .news-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--dark-color);
        }
        
        .news-source {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            color: var(--light-text);
            font-size: 0.9rem;
        }
        
        .news-description {
            margin-bottom: 15px;
            flex-grow: 1;
        }
        
        .news-footer {
            margin-top: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .read-more {
            background-color: var(--primary-color);
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            font-size: 0.9rem;
            transition: background-color 0.3s;
        }
        
        .read-more:hover {
            background-color: var(--dark-color);
        }
        
        .news-date {
            font-size: 0.8rem;
            color: var(--light-text);
        }
        
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin: 20px 0;
        }
        
        .no-results {
            text-align: center;
            padding: 30px;
            font-size: 1.1rem;
            color: var(--light-text);
        }
        
        footer {
            background-color: var(--primary-color);
            color: white;
            text-align: center;
            padding: 20px 0;
            margin-top: 30px;
        }
        
        @media (max-width: 768px) {
            .filter-controls {
                flex-direction: column;
                align-items: stretch;
            }
            
            .filter-group {
                flex-wrap: wrap;
            }
            
            .filter-group select {
                flex: 1;
                min-width: 120px;
            }
            
            .search-form {
                width: 100%;
            }
            
            .search-form input {
                flex: 1;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1><i class="fas fa-globe"></i> Global News Aggregator</h1>
        </div>
    </header>
    
    <nav>
        <div class="container">
            <div class="filter-controls">
                <div class="filter-group">
                    <label for="country">Country:</label>
                    <select id="country" name="country" onchange="this.form.submit()">
                        <?php foreach ($countries as $code => $name): ?>
                            <option value="<?= $code ?>" <?= $country === $code ? 'selected' : '' ?>>
                                <?= $name ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <label for="category">Category:</label>
                    <select id="category" name="category" onchange="this.form.submit()">
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat ?>" <?= $category === $cat ? 'selected' : '' ?>>
                                <?= ucfirst($cat) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <form class="search-form" method="get">
                    <input type="hidden" name="country" value="<?= htmlspecialchars($country) ?>">
                    <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
                    <input type="text" name="q" placeholder="Search news..." value="<?= htmlspecialchars($searchQuery) ?>">
                    <button type="submit"><i class="fas fa-search"></i> Search</button>
                </form>
            </div>
        </div>
    </nav>
    
    <main class="container">
        <?php if ($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php elseif (isset($newsData['articles']) && count($newsData['articles']) > 0): ?>
            <div class="news-container">
                <?php foreach ($newsData['articles'] as $article): ?>
                    <div class="news-card">
                        <?php if (isset($article['urlToImage']) && $article['urlToImage']): ?>
                            <img class="news-image" src="<?= htmlspecialchars($article['urlToImage']) ?>" alt="<?= htmlspecialchars($article['title']) ?>">
                        <?php else: ?>
                            <div class="news-image-placeholder">
                                <i class="fas fa-newspaper fa-3x"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="news-content">
                            <h2 class="news-title"><?= htmlspecialchars($article['title']) ?></h2>
                            
                            <div class="news-source">
                                <span><i class="fas fa-building"></i> <?= htmlspecialchars($article['source']['name'] ?? 'Unknown Source') ?></span>
                                <?php if (isset($article['author']) && $article['author']): ?>
                                    <span><i class="fas fa-user"></i> <?= htmlspecialchars($article['author']) ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <p class="news-description">
                                <?= htmlspecialchars(truncateText($article['description'] ?? 'No description available')) ?>
                            </p>
                            
                            <div class="news-footer">
                                <a href="<?= htmlspecialchars($article['url']) ?>" target="_blank" class="read-more">
                                    Read More <i class="fas fa-external-link-alt"></i>
                                </a>
                                
                                <?php if (isset($article['publishedAt'])): ?>
                                    <span class="news-date">
                                        <i class="far fa-clock"></i> <?= formatDate($article['publishedAt']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-results">
                <i class="fas fa-search fa-3x"></i>
                <p>No news articles found. Try changing your filters or search query.</p>
            </div>
        <?php endif; ?>
    </main>
    
    <footer>
        <div class="container">
            <p>&copy; <?= date('Y') ?> Global News Aggregator. Powered by <a href="https://newsapi.org/" target="_blank" style="color: white;">NewsAPI.org</a></p>
        </div>
    </footer>
    
    <script>
        // JavaScript to auto-submit the form when select elements change
        document.addEventListener('DOMContentLoaded', function() {
            const countrySelect = document.getElementById('country');
            const categorySelect = document.getElementById('category');
            
            countrySelect.addEventListener('change', function() {
                const searchQuery = document.querySelector('input[name="q"]').value;
                window.location.href = `?country=${this.value}&category=${categorySelect.value}&q=${encodeURIComponent(searchQuery)}`;
            });
            
            categorySelect.addEventListener('change', function() {
                const searchQuery = document.querySelector('input[name="q"]').value;
                window.location.href = `?country=${countrySelect.value}&category=${this.value}&q=${encodeURIComponent(searchQuery)}`;
            });
        });
    </script>
</body>
</html>