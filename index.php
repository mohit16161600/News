<?php
// Global News Aggregator
// A simple PHP website that fetches news from around the world

// Configuration
$newsApiKey = "1d3cc457b01f46d2b0c05b3eb9c0ad97"; // Replace with your NewsAPI key

// Debug - Check if API key has been changed
$demoMode = ($newsApiKey === "YOUR_API_KEY");
if (!$demoMode) {
    // Log actual API access attempt
    error_log("Attempting to access NewsAPI with provided key");
}
$defaultCategory = "general";
$defaultCountry = "us";

// Get parameters from URL
$category = isset($_GET['category']) ? $_GET['category'] : $defaultCategory;
$country = isset($_GET['country']) ? $_GET['country'] : $defaultCountry;

// Available options for selection
$categories = [
    "business" => "Business",
    "entertainment" => "Entertainment",
    "general" => "General",
    "health" => "Health",
    "science" => "Science",
    "sports" => "Sports",
    "technology" => "Technology"
];

$countries = [
    "ae" => "UAE",
    "ar" => "Argentina",
    "at" => "Austria",
    "au" => "Australia",
    "be" => "Belgium",
    "bg" => "Bulgaria",
    "br" => "Brazil",
    "ca" => "Canada",
    "ch" => "Switzerland",
    "cn" => "China",
    "co" => "Colombia",
    "cu" => "Cuba",
    "cz" => "Czech Republic",
    "de" => "Germany",
    "eg" => "Egypt",
    "fr" => "France",
    "gb" => "United Kingdom",
    "gr" => "Greece",
    "hk" => "Hong Kong",
    "hu" => "Hungary",
    "id" => "Indonesia",
    "ie" => "Ireland",
    "il" => "Israel",
    "in" => "India",
    "it" => "Italy",
    "jp" => "Japan",
    "kr" => "South Korea",
    "lt" => "Lithuania",
    "lv" => "Latvia",
    "ma" => "Morocco",
    "mx" => "Mexico",
    "my" => "Malaysia",
    "ng" => "Nigeria",
    "nl" => "Netherlands",
    "no" => "Norway",
    "nz" => "New Zealand",
    "ph" => "Philippines",
    "pl" => "Poland",
    "pt" => "Portugal",
    "ro" => "Romania",
    "rs" => "Serbia",
    "ru" => "Russia",
    "sa" => "Saudi Arabia",
    "se" => "Sweden",
    "sg" => "Singapore",
    "si" => "Slovenia",
    "sk" => "Slovakia",
    "th" => "Thailand",
    "tr" => "Turkey",
    "tw" => "Taiwan",
    "ua" => "Ukraine",
    "us" => "United States",
    "ve" => "Venezuela",
    "za" => "South Africa"
];

// Function to fetch news from NewsAPI
function fetchNews($apiKey, $country, $category) {
    $url = "https://newsapi.org/v2/everything?q=tesla&from=2025-04-07&sortBy=publishedAt&apiKey=1d3cc457b01f46d2b0c05b3eb9c0ad97";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    // Add User-Agent header to identify the application - this is required by NewsAPI
    $userAgent = 'GlobalNewsAggregator/1.0';
    curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
    
    // Add additional headers for better API compliance
    $headers = [
        'Accept: application/json',
        'Content-Type: application/json',
        'X-Api-Key: ' . $apiKey
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        return ["error" => curl_error($ch)];
    }
    
    curl_close($ch);
    
    return json_decode($response, true);
}

// Fetch news
$news = fetchNews($newsApiKey, $country, $category);

// Debug information
if (!$demoMode) {
    error_log("API Response: " . print_r($news, true));
    
    // Add explicit check for API errors
    if (isset($news['status']) && $news['status'] === 'error') {
        error_log("NewsAPI Error: " . ($news['message'] ?? 'Unknown error') . 
                  (isset($news['code']) ? " (Code: " . $news['code'] . ")" : ""));
    }
}

// This line is no longer needed as we've moved the check above
// $demoMode = ($newsApiKey === "YOUR_API_KEY");

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
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f9f9f9;
        }
        
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background-color: var(--primary-color);
            color: white;
            padding: 20px 0;
            margin-bottom: 30px;
        }
        
        header h1 {
            font-size: 2.2rem;
            text-align: center;
        }
        
        .subtitle {
            text-align: center;
            color: var(--light-color);
            margin-top: 5px;
            font-style: italic;
        }
        
        .filters {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
            margin-bottom: 30px;
            background-color: var(--light-color);
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
        
        .filter-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: var(--dark-color);
        }
        
        .filter-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: white;
            font-size: 1rem;
        }
        
        .btn {
            background-color: var(--secondary-color);
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 4px;
            font-size: 1rem;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #2980b9;
        }
        
        .news-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
        }
        
        .news-card {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .news-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        
        .news-image {
            height: 180px;
            background-color: #eee;
            background-size: cover;
            background-position: center;
        }
        
        .news-content {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        
        .news-source {
            color: var(--accent-color);
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .news-title {
            font-size: 1.2rem;
            margin-bottom: 10px;
            color: var(--dark-color);
        }
        
        .news-desc {
            color: #666;
            margin-bottom: 15px;
            flex-grow: 1;
        }
        
        .news-date {
            color: #999;
            font-size: 0.9rem;
            margin-top: auto;
        }
        
        .read-more {
            display: inline-block;
            margin-top: 10px;
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: bold;
        }
        
        .read-more:hover {
            text-decoration: underline;
        }
        
        .api-notice {
            background-color: #fff3cd;
            color: #856404;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            border-left: 5px solid #ffeeba;
        }
        
        footer {
            margin-top: 50px;
            text-align: center;
            color: #666;
            padding: 20px 0;
            border-top: 1px solid #eee;
        }
        
        @media (max-width: 768px) {
            .filter-group {
                min-width: 100%;
            }
            
            .news-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1><i class="fas fa-globe-americas"></i> Global News Aggregator</h1>
            <p class="subtitle">Stay informed with top headlines from around the world</p>
        </div>
    </header>
    
    <div class="container">
       
        
        <form class="filters" method="GET">
            <div class="filter-group">
                <label for="category">News Category:</label>
                <select name="category" id="category" onchange="this.form.submit()">
                    <?php foreach ($categories as $key => $name): ?>
                        <option value="<?php echo $key; ?>" <?php echo ($category === $key) ? 'selected' : ''; ?>>
                            <?php echo $name; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="country">Country:</label>
                <select name="country" id="country" onchange="this.form.submit()">
                    <?php foreach ($countries as $key => $name): ?>
                        <option value="<?php echo $key; ?>" <?php echo ($country === $key) ? 'selected' : ''; ?>>
                            <?php echo $name; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
        
        <div class="news-grid">
            <?php if ($demoMode): ?>
                <!-- Demo content -->
                <?php for ($i = 1; $i <= 6; $i++): ?>
                <div class="news-card">
                    <div class="news-image" style="background-image: url('/api/placeholder/600/400');"></div>
                    <div class="news-content">
                        <div class="news-source">Demo News Source</div>
                        <h3 class="news-title">This is a sample headline for demonstration purposes</h3>
                        <p class="news-desc">This is placeholder content. To see real news, please add your NewsAPI key to the PHP code.</p>
                        <div class="news-date"><?php echo date('F j, Y'); ?></div>
                        <a href="#" class="read-more">Read full story <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
                <?php endfor; ?>
            <?php elseif (isset($news['articles']) && !empty($news['articles'])): ?>
                <?php foreach ($news['articles'] as $article): ?>
                <div class="news-card">
                    <div class="news-image" style="background-image: url('<?php echo $article['urlToImage'] ?? '/api/placeholder/600/400'; ?>');"></div>
                    <div class="news-content">
                        <div class="news-source"><?php echo htmlspecialchars($article['source']['name'] ?? 'Unknown Source'); ?></div>
                        <h3 class="news-title"><?php echo htmlspecialchars($article['title'] ?? 'No Title'); ?></h3>
                        <p class="news-desc"><?php echo htmlspecialchars($article['description'] ?? 'No description available.'); ?></p>
                        <div class="news-date">
                            <?php 
                                $date = isset($article['publishedAt']) ? new DateTime($article['publishedAt']) : new DateTime();
                                echo $date->format('F j, Y');
                            ?>
                        </div>
                        <a href="<?php echo htmlspecialchars($article['url'] ?? '#'); ?>" class="read-more" target="_blank">
                            Read full story <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php elseif (isset($news['error'])): ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                    <h3>Error fetching news</h3>
                    <p><?php echo htmlspecialchars($news['error']); ?></p>
                </div>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                    <h3>No articles found</h3>
                    <p>Try changing the category or country selection.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Global News Aggregator. Powered by <a href="https://newsapi.org/" target="_blank">NewsAPI.org</a></p>
        </div>
    </footer>
</body>
</html>