<?php
/**
 * Content Model
 * 
 * Handles content generation, plagiarism checking, and readability assessment
 */

require_once __DIR__ . '/../db/connection.php';

class Content {
    /**
     * Get a content generation by ID
     * 
     * @param int $id The content ID
     * @return array|false The content data or false if not found
     */
    public static function getById($id) {
        return fetchRow("SELECT * FROM content_generations WHERE id = :id", ['id' => $id]);
    }
    
    /**
     * Get content generations by user ID
     * 
     * @param int $userId The user ID
     * @param int $limit The maximum number of records to return
     * @param int $offset The offset for pagination
     * @return array The content generations
     */
    public static function getByUserId($userId, $limit = 10, $offset = 0) {
        return fetchAll(
            "SELECT * FROM content_generations WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :limit OFFSET :offset",
            ['user_id' => $userId, 'limit' => $limit, 'offset' => $offset]
        );
    }
    
    /**
     * Create a new content generation
     * 
     * @param int $userId The user ID
     * @param string $topic The topic for content generation
     * @param array|string $keywords The keywords for content generation
     * @param int $wordCount The target word count
     * @param string $toneOfVoice The tone of voice (optional)
     * @param string $targetAudience The target audience (optional)
     * @param string $generatedContent The generated content
     * @param float $readabilityScore The readability score (optional)
     * @return int The ID of the created content generation
     */
    public static function create($userId, $topic, $keywords, $wordCount, $toneOfVoice, $targetAudience, $generatedContent, $readabilityScore = null) {
        // Convert keywords to string if it's an array
        if (is_array($keywords)) {
            $keywords = implode(',', $keywords);
        }
        
        return insertRow('content_generations', [
            'user_id' => $userId,
            'topic' => $topic,
            'keywords' => $keywords,
            'word_count' => $wordCount,
            'tone_of_voice' => $toneOfVoice,
            'target_audience' => $targetAudience,
            'generated_content' => $generatedContent,
            'readability_score' => $readabilityScore
        ]);
    }
    
    /**
     * Update a content generation
     * 
     * @param int $id The content ID
     * @param array $data The data to update
     * @return int The number of rows affected
     */
    public static function update($id, array $data) {
        // Convert keywords to string if it's an array
        if (isset($data['keywords']) && is_array($data['keywords'])) {
            $data['keywords'] = implode(',', $data['keywords']);
        }
        
        return updateRow('content_generations', $data, 'id = :id', ['id' => $id]);
    }
    
    /**
     * Delete a content generation
     * 
     * @param int $id The content ID
     * @return int The number of rows affected
     */
    public static function delete($id) {
        return deleteRow('content_generations', 'id = :id', ['id' => $id]);
    }
    
    /**
     * Generate content based on topic and keywords
     * 
     * In a real application, this would call an external AI service
     * For demonstration, we're using a template-based approach
     * 
     * @param string $topic The content topic
     * @param array|string $keywords Keywords to include
     * @param int $wordCount Target word count
     * @param string $toneOfVoice Tone of voice (optional)
     * @param string $targetAudience Target audience (optional)
     * @return string The generated content
     */
    public static function generateContent($topic, $keywords, $wordCount = 500, $toneOfVoice = 'professional', $targetAudience = null) {
        // Convert keywords to array if it's a string
        if (is_string($keywords)) {
            $keywords = explode(',', $keywords);
            $keywords = array_map('trim', $keywords);
        }
        
        // In a real application, this would call an AI service API
        // For demonstration, we'll use template paragraphs
        $paragraphs = [
            "The topic of {$topic} has been gaining significant attention in recent years. As businesses and individuals look to optimize their online presence, understanding the fundamental principles of {$topic} becomes crucial. With search engines constantly evolving their algorithms, staying updated with the latest trends is essential for success.",
            
            "When considering {$topic}, several key factors come into play. " . ucfirst($keywords[0] ?? "Research") . " shows that implementing strategic approaches can significantly improve results. " . ucfirst($keywords[1] ?? "Analysis") . " of market trends indicates a growing emphasis on user experience and content quality.",
            
            "Experts in the field recommend focusing on " . ($keywords[2] ?? "engagement") . " and " . ($keywords[3] ?? "optimization") . " to achieve optimal outcomes. By consistently applying best practices and monitoring performance metrics, organizations can enhance their {$topic} strategy and gain a competitive edge in the digital landscape."
        ];
        
        // Adjust tone based on specified tone of voice
        if ($toneOfVoice === 'casual') {
            $paragraphs[] = "Hey, let's not forget about {$topic} when we're planning our digital strategy! It's super important to keep an eye on those " . ($keywords[0] ?? "metrics") . " and make sure we're hitting our goals. After all, who wants to fall behind the competition?";
        } elseif ($toneOfVoice === 'formal') {
            $paragraphs[] = "It is imperative to acknowledge the significance of {$topic} in the formulation of comprehensive digital strategies. The meticulous monitoring of " . ($keywords[0] ?? "key performance indicators") . " is essential for ensuring the fulfillment of organizational objectives and maintaining competitive advantage.";
        } else {
            $paragraphs[] = "In conclusion, {$topic} remains a vital component of any comprehensive digital strategy. By leveraging the power of " . ($keywords[0] ?? "key principles") . " and implementing effective " . ($keywords[1] ?? "tactics") . ", businesses can achieve sustainable growth and improved visibility in an increasingly competitive online environment.";
        }
        
        // Add target audience specific paragraph if provided
        if ($targetAudience) {
            $paragraphs[] = "For {$targetAudience}, understanding {$topic} is particularly important. The specific challenges faced in this sector require tailored approaches that address unique needs and objectives. By focusing on " . ($keywords[1] ?? "specific strategies") . ", {$targetAudience} can maximize their results and achieve optimal outcomes.";
        }
        
        // Add more paragraphs if needed to approach target word count
        $content = implode("\n\n", $paragraphs);
        $currentWordCount = str_word_count($content);
        
        if ($currentWordCount < $wordCount) {
            $additionalParagraphs = [
                "Implementation of {$topic} strategies requires careful planning and execution. Organizations should begin by assessing their current position, identifying objectives, and developing a comprehensive roadmap. This systematic approach ensures that resources are allocated efficiently and that progress can be measured against established benchmarks.",
                
                "The future of {$topic} is likely to be shaped by emerging technologies and evolving consumer behaviors. " . ucfirst($keywords[2] ?? "Innovation") . " will play a critical role in adapting to these changes and maintaining competitive advantage. Organizations that embrace " . ($keywords[3] ?? "new methodologies") . " and remain agile in their approach will be best positioned for long-term success.",
                
                "Case studies have demonstrated the tangible benefits of effective {$topic} strategies. Companies that prioritize " . ($keywords[0] ?? "quality") . " and " . ($keywords[1] ?? "consistency") . " have reported significant improvements in key performance indicators. These real-world examples provide valuable insights and best practices for organizations seeking to enhance their own approaches."
            ];
            
            // Add additional paragraphs until we approach the target word count
            foreach ($additionalParagraphs as $paragraph) {
                $content .= "\n\n" . $paragraph;
                $currentWordCount = str_word_count($content);
                
                if ($currentWordCount >= $wordCount) {
                    break;
                }
            }
        }
        
        return $content;
    }
    
    /**
     * Check content for plagiarism
     * 
     * In a real application, this would call an external plagiarism checking service
     * For demonstration, we're returning dummy results
     * 
     * @param int $userId The user ID
     * @param string $content The content to check
     * @return array The plagiarism check results
     */
    public static function checkPlagiarism($userId, $content) {
        // In a real application, this would call a plagiarism checking API
        // For demonstration, we'll save the check and return dummy results
        
        // Insert the plagiarism check
        $checkId = insertRow('plagiarism_checks', [
            'user_id' => $userId,
            'content_id' => null, // Not associated with a specific content generation
            'content_text' => $content,
            'plagiarism_score' => 3.2 // Dummy score
        ]);
        
        // Insert some dummy matches
        $matches = [
            [
                'matched_text' => substr($content, 20, 40),
                'source_url' => 'https://example.com/article1',
                'match_percentage' => 95
            ],
            [
                'matched_text' => substr($content, 100, 40),
                'source_url' => 'https://blog.example.com/seo-tips',
                'match_percentage' => 85
            ]
        ];
        
        foreach ($matches as $match) {
            insertRow('plagiarism_matches', [
                'check_id' => $checkId,
                'matched_text' => $match['matched_text'],
                'source_url' => $match['source_url'],
                'match_percentage' => $match['match_percentage']
            ]);
        }
        
        return [
            'plagiarismScore' => 3.2,
            'matches' => $matches
        ];
    }
    
    /**
     * Assess content readability
     * 
     * In a real application, this would use sophisticated readability algorithms
     * For demonstration, we're returning dummy scores
     * 
     * @param int $userId The user ID
     * @param string $content The content to assess
     * @return array The readability assessment results
     */
    public static function assessReadability($userId, $content) {
        // In a real application, this would use readability formulas
        // For demonstration, we'll save the assessment and return dummy scores
        
        // Calculate some very basic text statistics
        $wordCount = str_word_count($content);
        $sentenceCount = preg_match_all('/[.!?]/', $content, $matches);
        $avgWordsPerSentence = $sentenceCount > 0 ? $wordCount / $sentenceCount : 0;
        
        // Simulate Flesch-Kincaid score (higher is easier to read)
        $fleschKincaid = 206.835 - (1.015 * $avgWordsPerSentence) - (84.6 * 1.5);
        $fleschKincaid = min(max($fleschKincaid, 0), 100); // Clamp between 0-100
        
        // Simulate other scores
        $scores = [
            'fleschKincaid' => round($fleschKincaid, 1),
            'smog' => round(8 + mt_rand(0, 20) / 10, 1),
            'colemanLiau' => round(10 + mt_rand(-20, 20) / 10, 1),
            'automatedReadability' => round(9 + mt_rand(-20, 20) / 10, 1),
        ];
        
        // Determine overall grade
        $avgScore = ($scores['fleschKincaid'] + 100 - $scores['smog'] * 5 + 100 - $scores['colemanLiau'] * 5 + 100 - $scores['automatedReadability'] * 5) / 4;
        
        if ($avgScore >= 90) {
            $overallGrade = 'A+';
        } elseif ($avgScore >= 80) {
            $overallGrade = 'A';
        } elseif ($avgScore >= 70) {
            $overallGrade = 'B';
        } elseif ($avgScore >= 60) {
            $overallGrade = 'C';
        } elseif ($avgScore >= 50) {
            $overallGrade = 'D';
        } else {
            $overallGrade = 'F';
        }
        
        $scores['overallGrade'] = $overallGrade;
        
        // Insert the readability check
        insertRow('readability_checks', [
            'user_id' => $userId,
            'content_id' => null, // Not associated with a specific content generation
            'content_text' => $content,
            'flesch_kincaid_score' => $scores['fleschKincaid'],
            'smog_score' => $scores['smog'],
            'coleman_liau_score' => $scores['colemanLiau'],
            'automated_readability_score' => $scores['automatedReadability'],
            'overall_grade' => $overallGrade
        ]);
        
        // Generate suggestions based on scores
        $suggestions = [];
        
        if ($avgWordsPerSentence > 20) {
            $suggestions[] = 'Use shorter sentences to improve readability.';
        }
        
        if ($scores['fleschKincaid'] < 60) {
            $suggestions[] = 'Consider simplifying vocabulary in the text.';
        }
        
        if (count($suggestions) === 0) {
            $suggestions[] = 'The content has good readability. Consider adding more transition words between paragraphs for even better flow.';
        }
        
        return [
            'scores' => $scores,
            'suggestions' => $suggestions
        ];
    }
    
    /**
     * Get all content generations
     * 
     * @param int $limit The maximum number of records to return
     * @param int $offset The offset for pagination
     * @return array The content generations
     */
    public static function getAll($limit = 10, $offset = 0) {
        return fetchAll(
            "SELECT cg.*, u.name as user_name, u.email as user_email 
             FROM content_generations cg 
             JOIN users u ON cg.user_id = u.id 
             ORDER BY cg.created_at DESC 
             LIMIT :limit OFFSET :offset",
            ['limit' => $limit, 'offset' => $offset]
        );
    }
    
    /**
     * Count the total number of content generations
     * 
     * @return int The number of content generations
     */
    public static function count() {
        $result = fetchRow("SELECT COUNT(*) as count FROM content_generations");
        return $result['count'];
    }
    
    /**
     * Calculate keyword density in content
     * 
     * @param string $content The content to analyze
     * @param array|string $keywords Keywords to check
     * @return array Keyword density information
     */
    public static function calculateKeywordDensity($content, $keywords) {
        // Convert keywords to array if it's a string
        if (is_string($keywords)) {
            $keywords = explode(',', $keywords);
            $keywords = array_map('trim', $keywords);
        }
        
        $content = strtolower($content);
        $wordCount = str_word_count($content);
        
        $result = [];
        
        foreach ($keywords as $keyword) {
            $keyword = strtolower(trim($keyword));
            $count = substr_count($content, $keyword);
            $density = ($wordCount > 0) ? ($count / $wordCount) * 100 : 0;
            
            $result[$keyword] = [
                'count' => $count,
                'density' => round($density, 2)
            ];
        }
        
        return $result;
    }
}