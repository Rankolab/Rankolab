<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class ReadabilityService
{
    /**
     * Analyze content readability.
     *
     * @param string $content
     * @return float Readability score (0-100)
     */
    public function analyze(string $content): float
    {
        Log::info('Starting readability analysis for content', ['length' => strlen($content)]);
        
        try {
            // Clean the content (remove HTML tags, etc.)
            $cleanContent = $this->cleanContent($content);
            
            // Calculate Flesch-Kincaid Reading Ease score
            $score = $this->calculateFleschKincaidScore($cleanContent);
            
            // Convert to 0-100 scale (higher is better)
            $normalizedScore = $this->normalizeScore($score);
            
            Log::info('Readability analysis complete', [
                'raw_score' => $score,
                'normalized_score' => $normalizedScore
            ]);
            
            return $normalizedScore;
        } catch (\Exception $e) {
            Log::error('Readability analysis error', ['error' => $e->getMessage()]);
            return 70.0; // Return a default score on error
        }
    }
    
    /**
     * Clean content for analysis.
     *
     * @param string $content
     * @return string
     */
    private function cleanContent(string $content): string
    {
        // Remove HTML tags
        $text = strip_tags($content);
        
        // Replace multiple spaces, tabs, and newlines with a single space
        $text = preg_replace('/\s+/', ' ', $text);
        
        // Trim leading and trailing whitespace
        return trim($text);
    }
    
    /**
     * Calculate Flesch-Kincaid Reading Ease score.
     *
     * @param string $text
     * @return float
     */
    private function calculateFleschKincaidScore(string $text): float
    {
        // Count sentences
        $sentences = preg_split('/[.!?]+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        $sentenceCount = count($sentences);
        
        // Count words
        $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        $wordCount = count($words);
        
        // Count syllables
        $syllableCount = $this->countSyllables($text);
        
        // Handle division by zero
        if ($wordCount === 0 || $sentenceCount === 0) {
            return 50.0; // Return a middle value
        }
        
        // Calculate average sentence length
        $avgSentenceLength = $wordCount / $sentenceCount;
        
        // Calculate average syllables per word
        $avgSyllablesPerWord = $syllableCount / $wordCount;
        
        // Flesch-Kincaid Reading Ease formula
        // 206.835 - 1.015 * (words/sentences) - 84.6 * (syllables/words)
        $score = 206.835 - (1.015 * $avgSentenceLength) - (84.6 * $avgSyllablesPerWord);
        
        return $score;
    }
    
    /**
     * Count syllables in text.
     *
     * @param string $text
     * @return int
     */
    private function countSyllables(string $text): int
    {
        // Convert to lowercase
        $text = strtolower($text);
        
        // Remove punctuation
        $text = preg_replace('/[^\w\s]/', '', $text);
        
        // Split into words
        $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        
        $totalSyllables = 0;
        
        foreach ($words as $word) {
            $syllables = $this->countWordSyllables($word);
            $totalSyllables += $syllables;
        }
        
        return $totalSyllables;
    }
    
    /**
     * Count syllables in a word.
     *
     * @param string $word
     * @return int
     */
    private function countWordSyllables(string $word): int
    {
        // This is a simplified algorithm for counting syllables
        // For English words only
        
        // Count vowel groups
        $word = strtolower($word);
        
        // Special cases
        if (strlen($word) <= 3) {
            return 1;
        }
        
        // Remove 'es', 'ed' at the end of words
        $word = preg_replace('/e?s$|ed$/', '', $word);
        
        // Count vowel groups
        $vowelGroups = preg_match_all('/[aeiou]+/i', $word, $matches);
        
        // Count 'y' at the end as a vowel
        if (substr($word, -1) === 'y') {
            $vowelGroups++;
        }
        
        // Ensure at least one syllable
        return max(1, $vowelGroups);
    }
    
    /**
     * Normalize Flesch-Kincaid score to 0-100 scale.
     *
     * @param float $score
     * @return float
     */
    private function normalizeScore(float $score): float
    {
        // Flesch-Kincaid is 0-100 where higher is easier to read
        // But can sometimes go outside that range
        
        // Cap at 0-100
        $score = max(0, min(100, $score));
        
        // For grade 6 readability (target), Flesch-Kincaid score is around 80-90
        // Return as is since it's already 0-100
        return round($score, 1);
    }
    
    /**
     * Get detailed readability analysis.
     *
     * @param string $content
     * @return array
     */
    public function getDetailedAnalysis(string $content): array
    {
        // Clean the content
        $cleanContent = $this->cleanContent($content);
        
        // Count basic metrics
        $sentences = preg_split('/[.!?]+/', $cleanContent, -1, PREG_SPLIT_NO_EMPTY);
        $sentenceCount = count($sentences);
        
        $words = preg_split('/\s+/', $cleanContent, -1, PREG_SPLIT_NO_EMPTY);
        $wordCount = count($words);
        
        $charCount = strlen(preg_replace('/\s+/', '', $cleanContent));
        
        $syllableCount = $this->countSyllables($cleanContent);
        
        // Calculate averages
        $avgSentenceLength = $sentenceCount > 0 ? $wordCount / $sentenceCount : 0;
        $avgWordLength = $wordCount > 0 ? $charCount / $wordCount : 0;
        $avgSyllablesPerWord = $wordCount > 0 ? $syllableCount / $wordCount : 0;
        
        // Calculate various readability scores
        $fleschScore = $this->calculateFleschKincaidScore($cleanContent);
        $normalizedScore = $this->normalizeScore($fleschScore);
        
        // Estimate grade level
        $gradeLevel = $this->estimateGradeLevel($fleschScore);
        
        // Find complex words and sentences
        $complexWords = $this->findComplexWords($words);
        $longSentences = $this->findLongSentences($sentences);
        
        // Get passive voice sentences
        $passiveVoiceSentences = $this->findPassiveVoice($sentences);
        
        return [
            'score' => $normalizedScore,
            'grade_level' => $gradeLevel,
            'metrics' => [
                'word_count' => $wordCount,
                'sentence_count' => $sentenceCount,
                'syllable_count' => $syllableCount,
                'avg_sentence_length' => round($avgSentenceLength, 1),
                'avg_word_length' => round($avgWordLength, 1),
                'avg_syllables_per_word' => round($avgSyllablesPerWord, 1),
            ],
            'issues' => [
                'complex_words' => $complexWords,
                'long_sentences' => $longSentences,
                'passive_voice' => $passiveVoiceSentences,
            ],
            'recommendations' => $this->generateRecommendations(
                $normalizedScore,
                $avgSentenceLength,
                $avgSyllablesPerWord,
                count($complexWords),
                count($longSentences),
                count($passiveVoiceSentences)
            )
        ];
    }
    
    /**
     * Estimate grade level from Flesch-Kincaid score.
     *
     * @param float $score
     * @return string
     */
    private function estimateGradeLevel(float $score): string
    {
        if ($score >= 90) return 'Grade 5 (Very Easy)';
        if ($score >= 80) return 'Grade 6 (Easy)';
        if ($score >= 70) return 'Grade 7 (Fairly Easy)';
        if ($score >= 60) return 'Grade 8-9 (Standard)';
        if ($score >= 50) return 'Grade 10-12 (Fairly Difficult)';
        if ($score >= 30) return 'College (Difficult)';
        return 'College Graduate (Very Difficult)';
    }
    
    /**
     * Find complex words in content.
     *
     * @param array $words
     * @return array
     */
    private function findComplexWords(array $words): array
    {
        $complexWords = [];
        
        foreach ($words as $word) {
            if (strlen($word) >= 4 && $this->countWordSyllables($word) >= 3) {
                $complexWords[] = $word;
            }
        }
        
        // Return unique complex words
        return array_unique($complexWords);
    }
    
    /**
     * Find long sentences in content.
     *
     * @param array $sentences
     * @return array
     */
    private function findLongSentences(array $sentences): array
    {
        $longSentences = [];
        
        foreach ($sentences as $sentence) {
            $wordCount = str_word_count($sentence);
            
            if ($wordCount > 20) {
                $longSentences[] = $sentence;
            }
        }
        
        return $longSentences;
    }
    
    /**
     * Find passive voice sentences.
     *
     * @param array $sentences
     * @return array
     */
    private function findPassiveVoice(array $sentences): array
    {
        $passiveVoiceSentences = [];
        
        // Common passive voice patterns
        $patterns = [
            '/\bis [a-z]+ by\b/i',
            '/\bwas [a-z]+ by\b/i',
            '/\bare [a-z]+ by\b/i',
            '/\bwere [a-z]+ by\b/i',
            '/\bbeen [a-z]+ by\b/i',
            '/\bbe [a-z]+ by\b/i',
            '/\bbeing [a-z]+ by\b/i',
        ];
        
        foreach ($sentences as $sentence) {
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $sentence)) {
                    $passiveVoiceSentences[] = $sentence;
                    break; // Break after first match to avoid duplicates
                }
            }
        }
        
        return $passiveVoiceSentences;
    }
    
    /**
     * Generate readability recommendations.
     *
     * @param float $score
     * @param float $avgSentenceLength
     * @param float $avgSyllablesPerWord
     * @param int $complexWordCount
     * @param int $longSentenceCount
     * @param int $passiveVoiceCount
     * @return array
     */
    private function generateRecommendations(
        float $score,
        float $avgSentenceLength,
        float $avgSyllablesPerWord,
        int $complexWordCount,
        int $longSentenceCount,
        int $passiveVoiceCount
    ): array {
        $recommendations = [];
        
        if ($score < 70) {
            $recommendations[] = 'Improve overall readability to reach at least a 6th-grade level (score of 70+).';
        }
        
        if ($avgSentenceLength > 18) {
            $recommendations[] = 'Shorten sentences. Current average length (' . round($avgSentenceLength, 1) . ' words) is too high for easy reading.';
        }
        
        if ($avgSyllablesPerWord > 1.8) {
            $recommendations[] = 'Use simpler words with fewer syllables. Current average (' . round($avgSyllablesPerWord, 1) . ' syllables per word) makes reading difficult.';
        }
        
        if ($complexWordCount > 10) {
            $recommendations[] = 'Replace complex words with simpler alternatives. Found ' . $complexWordCount . ' complex words.';
        }
        
        if ($longSentenceCount > 5) {
            $recommendations[] = 'Break down ' . $longSentenceCount . ' long sentences into shorter ones.';
        }
        
        if ($passiveVoiceCount > 5) {
            $recommendations[] = 'Convert ' . $passiveVoiceCount . ' passive voice sentences to active voice.';
        }
        
        if (empty($recommendations)) {
            $recommendations[] = 'Content is well-optimized for readability at approximately a 6th-grade level.';
        }
        
        return $recommendations;
    }
}
